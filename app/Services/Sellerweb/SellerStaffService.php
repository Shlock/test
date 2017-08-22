<?php 
namespace YiZan\Services\Sellerweb;

use YiZan\Models\Sellerweb\SellerStaff;
use YiZan\Models\Sellerweb\SellerStaffExtend;
use YiZan\Models\Sellerweb\Seller;
use YiZan\Models\Sellerweb\User;
use YiZan\Models\StaffMap;
use YiZan\Models\Order;
use YiZan\Models\SellerDistrict;
use YiZan\Models\SellerStaffDistrict;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use DB, Validator;

class SellerStaffService extends \YiZan\Services\SellerStaffService
{
    

	/**
     * 员工列表
     * @param  int $sellerId 机构编号
     * @param  string $name 员工名称
     * @param  string $mobile 员工电话
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          员工信息
	 */
	public static function getSellerList($sellerId, $name, $mobile, $type = '', $page, $pageSize) {
		$list = SellerStaff::orderBy('id', 'desc');
        //$type = array(0, 3, $type);
        $list->where('seller_id', $sellerId);
        $name = empty($name) ? '' : String::strToUnicode($name,'+');

        if ($name == true) {
            $list->whereRaw('MATCH(name_match) AGAINST(\'' . $name . '\' IN BOOLEAN MODE)');
        }

        if (!empty($mobile)) {
            $list->where('mobile', 'like', '%' . $mobile . '%');
        }
		$totalCount = $list->count();
		$list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('seller','user')
            ->get()
            ->toArray();
        return ["list"=>$list, "totalCount"=>$totalCount];
	}
    /**
     * 添加服务人员
     * @param int $sellerId 机构编号
     * @param string $mobile 手机号
     * @param string $pwd 密码
     * @param string $name 姓名
     * @param string $avatar 头像
     * @param array $photos 人个相册
     * @param string $address 地址
     * @param string $mapPoint 纬度,经度(QQ地图坐标)
     * @param int $provinceId 所在省编号
     * @param int $cityId 所在市编号
     * @param int $areaId 所在县编号
     * @param string $brief 简介
     * @param int $sort 排序
     * @return array   添加结果
     */
    public static function saveStaff($id, $sellerId, $mobile, $pwd, $name, $avatar, $type, $address, $mapPoint, 
        $provinceId, $cityId, $areaId, $sex, $authentication, $authenticateImg, $mapPos, $status) {

        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );
        
        $rules = array(
            'sellerId'      => ['exists:seller,id'],
            'mobile'        => ['required','mobile'],
            'name'          => ['required', 'max:20'],
            'avatar'        => ['required'],
            'provinceId'    => ['min:1'],
            'cityId'        => ['min:1'],
            'areaId'        => ['min:1'],
            'address'       => ['required', 'max:20'],
            'mapPoint'      => ['required'],
            'mapPos'        => ['required'],
            'authentication'=> ['max:50'],
        );

        $messages = array(
            'sellerId.exists'   => 50208,
            'mobile.required'   => 50203,
            'mobile.mobile'     => 50203,
            'name.required'     => 50202,
            'name.max'          => 50229,
            'avatar.required'   => 50209,
            'provinceId.min'    => 50210,   
            'cityId.min'        => 50211,   
            'areaId.min'        => 50212,   
            'address.required'  => 50204,
            'address.max'       => 50229,
            'mapPoint.required' => 50213,
            'mapPos.required'   => 50220,
            'authentication.max'=> 50229, 
        );

        $validator = Validator::make([
                'sellerId'      => $sellerId,
                'mobile'        => $mobile,
                'name'          => $name,
                'avatar'        => $avatar,
                'provinceId'    => $provinceId,
                'cityId'        => $cityId,
                'areaId'        => $areaId,
                'address'       => $address,
                'mapPoint'      => $mapPoint,
                'mapPos'        => $mapPos,
                'authentication'=> $authentication,
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        $mapPoint = Helper::foramtMapPoint($mapPoint);
        if (!$mapPoint){
            $result['code'] = 50214;    // 地图定位错误
            return $result;
        }

        $mapPos = Helper::foramtMapPos($mapPos);
        if (!$mapPos){
            $result['code'] = 50220;    // 服务范围定位错误
            return $result;
        }

        $user_check = User::where('mobile',$mobile)->first();
        if ($id > 0) {
            $staff = SellerStaff::where('id',$id)
                    ->where('seller_id',$sellerId)
                    ->first();
            if (!$staff) {//员工不存在
                $result['code'] = 50201;
                return $result;
            }
            if ($staff && $staff->mobile != $mobile && $user_check) {//修改员工信息,修改手机号码时
                $staff_check = (int)SellerStaff::where('user_id',$user_check->id)->where('seller_id', '<>', $sellerId)->pluck('id');
                if ($staff_check > 0 ) {
                    $result['code'] = 50215;    // 手机号码已被注册
                    return $result;
                }
                //如果电话存在商家且员工不属于此商家,则不能添加
                $seller_check = Seller::where('user_id', $user_check->id)->pluck('id');
                if ($seller_check > 0 && $seller_check != $sellerId) {
                    $result['code'] = 30603;    // 手机号码已被注册
                    return $result;
                }
                $password = md5(md5($pwd).$user_check->crypt);
                if ($password != $user_check->pwd) {
                    $result['code'] = 50218;//密码输入错误
                    return $result;
                }
            }
        }else {
            $staff = new SellerStaff();
            if ($pwd == '') {//创建员工时,密码不能为空
                $result['code'] = 50206;
                return $result;
            }
            if ($user_check) {//该手机号码会员已存在时验证时候已关联员工,密码是否正确
                $staff_check = (int)SellerStaff::where('user_id',$user_check->id)->pluck('id');
                if ($staff_check > 0) {
                    $result['code'] = 50215;    // 手机号码已被注册
                    return $result;
                }
                //如果电话存在商家且员工不属于此商家,则不能添加
                $seller_check = Seller::where('user_id', $user_check->id)->pluck('id');
                if ($seller_check > 0 && $seller_check != $sellerId) {
                    $result['code'] = 30603;    // 手机号码已被注册
                    return $result;
                }
                $password = md5(md5($pwd).$user_check->crypt);
                if ($password != $user_check->pwd) {
                    $result['code'] = 50218;//密码输入错误
                    return $result;
                }
            }
        } 

        if ($sellerId > 0) {
            $seller = Seller::find($sellerId);
            if (!$seller) {//机构不存在
                $result['code'] = 50208;
                return $result;
            }
        }

        $staff_id = (int)SellerStaff::where("mobile", $mobile)->pluck('id');
        if ($staff_id > 0 && $id != $staff_id) {
            $result['code'] = 50215;    // 手机号码已被注册
            return $result;
        }

        DB::beginTransaction();
        try {
            $remove_images = [];

            //查找手机号码对应的会员编号
            $mobile_user_id = (int)User::where('mobile', $mobile)->pluck('id');
            //要更新会员的信息
            $update_user_info = [];
            
            //当为修改机构员工时
            if ($id > 0) {
                $staff_old_user_id = $staff->user_id;
                //如果修改手机号码
                if ($staff->mobile != $mobile) {
                    //如果被注册,则更新服务人员关联的会员编号
                    if ($mobile_user_id > 0) {
                        $staff->user_id = $mobile_user_id;
                    } else {//如果未被注册,则修改关联会员的手机号码
                        $update_user_info['mobile'] = $mobile;
                    }
                }

                if (!empty($pwd)) {//当为修改密码时
                    $crypt  = String::randString(6);
                    $pwd    = md5(md5($pwd) . $crypt);

                    $update_user_info['crypt'] = $crypt;
                    $update_user_info['pwd']   = $pwd;
                }

            } else {//当为新增服务人员时
                if ($mobile_user_id < 1) {//当手机号没有被注册时,创建会员
                    if (empty($pwd)) {
                        $result['code'] = 50206;
                        return $result;
                    }

                    $user                   = new User();
                    $user->mobile           = $mobile;
                    $user->name_match       = String::strToUnicode($name.$mobile);
                    $user->name             = $name;
                    $user->reg_time         = UTC_TIME;
                    $user->reg_ip           = CLIENT_IP;
                    $user->province_id      = $provinceId;
                    $user->city_id          = $cityId;
                    $user->area_id          = $areaId;
                    $user->is_sms_verify    = 1;
                    $user->save();
                    $staff->user_id = $user->id;

                    $avatar_user = self::moveUserImage($user->id, $avatar);
                    if (!$avatar_user) {//转移图片失败
                        $result['code'] = 50216;
                        return $result;
                    }
                    $update_user_info['avatar'] = $avatar_user;
                } else {//当手机号已存在时关联会员编号
                    $staff->user_id = $mobile_user_id;
                }
                
                if (!empty($pwd)) {
                    $crypt  = String::randString(6);
                    $pwd    = md5(md5($pwd) . $crypt);
                    $update_user_info['crypt'] = $crypt;
                    $update_user_info['pwd']   = $pwd;
                }
            }

            //当会员数据有更新项时
            if (count($update_user_info) > 0) {
                User::where('id', $staff->user_id)->update($update_user_info);
            }
            
            $staff->seller_id          = $sellerId;
            $staff->mobile             = $mobile;
            $staff->name               = $name;
            $staff->name_match         = String::strToUnicode($name.$mobile);
            $staff->address            = $address;
            $staff->map_point_str      = $mapPoint;
            $staff->map_pos_str        = $mapPos["str"];
            $staff->map_point          = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
            $staff->map_pos            = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
            $staff->province_id        = $provinceId;
            $staff->city_id            = $cityId;
            $staff->area_id            = $areaId;
            $staff->sort               = 100;
            $staff->status             = $status;
            $staff->create_time        = UTC_TIME;
            $staff->create_day         = UTC_DAY;
            $staff->sex                = $sex;
            $staff->authentication     = $authentication;
            $staff->authenticate_img   = $authenticateImg;
            $staff->type               = $type;
            $staff->save();

            if ($staff->authenticate_img != $authenticateImg) {//当原图与新图不一样时
                $authenticateImg = self::moveStaffImage($sellerId, $staff->id, $authenticateImg);
                if (!$authenticateImg) {//转移图片失败
                    $result['code'] = 50216;
                    return $result;
                }
                if (!empty($staff->avatar)) {
                    $remove_images[] = $staff->avatar;
                }
                $staff->avatar = $authenticateImg;
            }
            //更新员工地图坐标表
            // $staff_map = StaffMap::where('staff_id', $staff->id)->first();
            // if ($staff_map) {
            //     StaffMap::where('staff_id', $staff->id)->update([
            //         'map_pos' => DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')"),
            //         'map_point' => DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')")
            //     ]);
            // } else {
            //     StaffMap::insert([
            //         'seller_id' => $sellerId,
            //         'staff_id' => $staff->id,
            //         'map_pos' => DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')"),
            //         'map_point' => DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')")
            //     ]);
            // }
            if ($staff->avatar != $avatar) {//当原图与新图不一样时
                $avatar = self::moveStaffImage($sellerId, $staff->id, $avatar);
                if (!$avatar) {//转移图片失败
                    $result['code'] = 50216;
                    return $result;
                }
                if (!empty($staff->avatar)) {
                    $remove_images[] = $staff->avatar;
                }
                $staff->avatar = $avatar;
            }
            $staff->save();

            //员工修改时查找相同手机商家更新相关信息
            if ($staff_old_user_id) {
                Seller::where('id', $sellerId)->where('user_id', $staff_old_user_id)->update([
                        'mobile'=>$mobile, 
                        'user_id'=>$staff->user_id
                    ]);
            }
            //添加要删除的原图到删除图片数组
            $remove_images = array_merge($remove_images, $old_images);

            if ($id < 1) {
                $sellerStaffExtend = new SellerStaffExtend();
                $sellerStaffExtend->staff_id = $staff->id;
                $sellerStaffExtend->seller_id = $sellerId;
                $sellerStaffExtend->save();
            }

            if (0 < count($remove_images)) {//删除多余图片
                self::removeImage($remove_images);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        
        return $result;
    }

   

    /**
     * 获取员工
     * @param  int $id 员工id
     * @return array   员工
     */
	public static function getSystemSellerStaffById($id, $sellerId) {
		return SellerStaff::where('id', $id)
            ->where('seller_id', $sellerId)
		    ->with('seller', 'user', 'province', 'city', 'area')
		    ->first();
	}

    /**
     * 删除员工
     * @param int  $id 员工id
     * @param int  $sellerId 机构编号
     * @return array   删除结果
     */
	public static function deleteSeller($id,$sellerId) {
		$result = [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		];

        $SellerStaff = SellerStaff::where('id',$id)
                       ->where('seller_id',$sellerId)
                       ->first();
        if (!$SellerStaff) {
            $result['code'] = 50201;
        }

        DB::beginTransaction();
        try {
            //删除员工
            SellerStaff::destroy($id);
            //删除员工提供的服务
            \YiZan\Models\GoodsStaff::where('staff_id', $id)->delete();
            //删除员工扩展信息
            \YiZan\Models\SellerStaffExtend::where('staff_id', $id)->delete();
            //删除员工预约信息
           // \YiZan\Models\StaffAppoint::where('staff_id', $id)->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }

		return $result;
	}

    /**
     * 更改员工状态
     * @param int  $sellerId 机构编号
     * @param int $id;  员工编号
     * @return [type] [description]
     */
    public static function updateStaffStatus($sellerId,$id,$status){
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];
        $staff = SellerStaff::where('id',$id)
            ->where('seller_id',$sellerId)
            ->first();
        if (!$staff) {
            $result['code'] = 50201;
            return $result;
        }
        $staff_result = SellerStaff::where('id',$id)->update(['status'=>$status]);
        if ($staff_result !== false) {
            $result['code'] = 0;
            $result['status'] = 1;
        } else {
            $result['code'] = 99999;
        }
        return $result;
    }

    //查看员工排期
    public static function getStaffSchedule($id, $sellerId){
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];
        $staff = SellerStaff::where('id',$id)->where('seller_id',$sellerId)->first();
        if (!$staff) {
            $result['code'] = 50201;
            return $result;
        }
        $list = [];
        $daytime = UTC_DAY;
        $endTime = $daytime + 8 * 24 * 3600;

        for ($i = $daytime; $i < $endTime; $i += 86400) { 
            $day = Time::toDate($i, 'Y-m-d');
            $list[$day] = Order::where('create_day', $i)
                                        ->where('seller_staff_id', $id)
                                        ->get()
                                        ->toArray();
            
        }
       //  print_r($list);
       // exit;
        ksort($list);
        return $list;
    }
}
