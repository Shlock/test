<?php

namespace YiZan\Services\System;

use YiZan\Models\System\Seller;
use YiZan\Models\SellerMap;
use YiZan\Models\Order;
use YiZan\Models\SellerBank;
use YiZan\Models\SellerCateRelated;
use YiZan\Models\System\User;
use YiZan\Models\GoodsCate;
use YiZan\Models\PropertyUser;
use YiZan\Models\System\SellerExtend;
use YiZan\Models\System\SellerAuthenticate;
use YiZan\Models\System\SellerMoneyLog;
use YiZan\Models\ReadMessage;
use YiZan\Services\PushMessageService;
use YiZan\Models\System\Promotion;
use YiZan\Models\System\PromotionSn;
use YiZan\Models\System\OrderRate;
use YiZan\Models\System\Goods;
use YiZan\Models\System\GoodsExtend;
use YiZan\Models\SellerStaff;
use YiZan\Models\SellerStaffExtend;
use YiZan\Models\UserVerifyCode;
use YiZan\Models\SellerDeliveryTime;
use YiZan\Models\District;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use DB,
    Validator,
    Lang;

class SellerService extends \YiZan\Services\SellerService {

    /**
     * 服务人员状态锁定
     */
    const SEARCH_STATUS_NO = 1;

    /**
     * 服务人员状态正常
     */
    const SEARCH_STATUS_OK = 2;

    /**
     * 服务人员接单状态(不接单)
     */
    const SEARCH_BUSINESS_STATUS_NO = 1;

    /**
     * 服务人员接单状态正常
     */
    const SEARCH_BUSINESS_STATUS_OK = 2;

    /**
     * 商家搜索
     * @param  [type] $mobileName 手机或者名称
     * @return [type]             [description]
     */
    public static function searchSeller($mobileName) {
        $list = Seller::select('id', 'name', 'mobile');
        //$match = empty($mobileName) ? '' : String::strToUnicode($mobileName, '+');
        $list->where('is_del', 0);
        if (!empty($mobileName)) {
            $list->where(function($query) use ($mobileName) {
                $query->where('name', 'like', '%' . $mobileName . '%')
                        ->orWhere('mobile', $mobileName);
            }); //->selectRaw("IF(name = '{$mobileName}' or mobile = '{$mobileName}',1,0) AS eq,
            //       MATCH(name_match) AGAINST('{$match}') AS similarity")
            //->whereRaw('MATCH(name_match) AGAINST(\'' . $match . '\' IN BOOLEAN MODE)')
        }

        return $list->whereIn('type', [1, 2])
                        ->orderBy('id', 'desc')
                        ->skip(0)
                        ->take(30)
                        ->get()
                        ->toArray();
    }

    /**
     * 服务人员列表
     * @param  string $name     	  名称
     * @param  int    $status         状态
     * @param  int    $page           页码
     * @param  int    $pageSize       每页数
     * @return array          订单列表
     */
    public static function getSystemList($mobileName, $mobile, $provinceId, $cityId, $areaId, $status, $cateId, $page, $pageSize) {
        $list = Seller::orderBy('id', 'desc');
        if (!empty($mobileName)) {
            $list->where('name', 'like', '%' . $mobileName . '%');
        }

        $list->where('is_del', 0);
        $list->where('is_check', 1);
        $list->whereIn('type', [1, 2]);
        if ($provinceId > 0) {
            $list->where('province_id', $provinceId);
        }

        if ($cityId > 0) {
            $list->where('city_id', $cityId);
        }

        if ($areaId > 0) {
            $list->where('area_id', $areaId);
        }

        if ($status > 0) {
            $list->where('status', $status - 1);
        }

        if ($mobile != '') {
            $list->where('mobile', $mobile);
        }

        if ($cateId > 0) {
            $list->whereIn('id', function($query) use ($cateId) {
                $query->select('seller_id')
                        ->from('seller_cate_related')
                        ->where('cate_id', $cateId);
            });
        }
        $totalCount = $list->count();
        //$list->select(DB::raw("*, (select count(1) from " . env('DB_PREFIX') . "seller_staff as s where s.seller_id = " . env('DB_PREFIX') . "seller.id) as staffCount"));
        $list->select(DB::raw("*"));
        $rows = $list->skip(($page - 1) * $pageSize)
                ->take($pageSize)
                ->with('province', 'city', 'area')
                ->get()
                ->toArray();
        //获取商家商品服务分类及人员统计
        foreach ($rows as $key => $value) {
            $rows[$key]['goodscount'] = Goods::where('seller_id', $value['id'])->where('type', 1)->count();
            $rows[$key]['servicecount'] = Goods::where('seller_id', $value['id'])->where('type', 2)->count();
            $rows[$key]['goodscatecount'] = GoodsCate::where('seller_id', $value['id'])->where('type', 1)->count();
            $rows[$key]['servicecatecount'] = GoodsCate::where('seller_id', $value['id'])->where('type', 2)->count();
            $rows[$key]['staffcount'] = SellerStaff::where('seller_id', $value['id'])->where('status', 1)->count();
        }
        // print_r($list);
        // exit;
        return ["list" => $rows, "totalCount" => $totalCount];
    }

    /*
     * 商家审核
     */

    public static function getSystemSellerList($name, $isCheck, $page, $pageSize) {
        $list = Seller::orderBy('id', 'desc');

        // $match = empty($name) ? '' : String::strToUnicode($name, '+');
        // if (!empty($match)) {
        // 	$list->whereRaw('MATCH(name_match) AGAINST(\'' . $match . '\' IN BOOLEAN MODE)');
        // }
        if (!empty($name)) {
            $list->where('name', 'like', '%' . $name . '%');
        }

        $list->where('is_del', 0);
        $list->where('is_authshow', 0);

        if ($isCheck > 0) {
            $list->where('is_check', $isCheck - 2);
        }

        $totalCount = $list->count();
        $list->select(DB::raw("*, (select count(1) from " . env('DB_PREFIX') . "seller_staff as s where s.seller_id = " . env('DB_PREFIX') . "seller.id) as staffCount"));
        $list = $list->skip(($page - 1) * $pageSize)
                ->take($pageSize)
                ->with('province', 'city', 'area')
                ->get()
                ->toArray();

        return ["list" => $list, "totalCount" => $totalCount];
    }

    /**
     * 添加服务人员
     * @param  int   $type           机构类型
     * @param string $mobile         手机号
     * @param string $pwd            密码
     * @param string $name           名称
     * @param string $contacts       负责人
     * @param string $address        地址
     * @param int    $provinceId     所在省编号
     * @param int    $cityId         所在市编号
     * @param int    $areaId         所在县编号
     * @param string $brief          简介
     * @param int    $sort           排序
     * @param int    $businessStatus 营业状态
     * @param string $businessDesc   营业说明
     * @param int    $status         状态
     * @return array   添加结果
     */
    public static function saveSeller($id, $mobile, $pwd, $name, $type, $contacts, $address, $logo, $image, $provinceId, $cityId, $areaId, $brief, $mapPos, $mapPoint, $idcardSn, $businessLicenceImg, $idcardPositiveImg, $idcardNegativeImg, $cateIds, $serviceFee = 0, $deliveryFee = 0, $isAuthenticate, $certificateImg, $serviceTel, $deliveryTime, $deduct, $isCashOnDelivery) {
        $cateIds = is_array($cateIds) ? $cateIds : [$cateIds];
        $result = array('code' => self::SUCCESS,
            'data' => null,
            'msg' => '');

        $rules = array(
            'mobile' => ['required', 'regex:/^1[0-9]{10}$/'],
            'serviceTel' => ['regex:/^[0-9]{8,11}$/'],
            'name' => ['required', 'max:20'],
            'pwd' => ['max:20'],
            'contacts' => ['max:10'],
            'provinceId' => ['min:1'],
            'cityId' => ['min:1'],
            'areaId' => ['min:1'],
            'address' => ['required'],
            'idcardSn' => ['required'],
            'idcardPositiveImg' => ['required'],
            'idcardNegativeImg' => ['required'],
            'mapPoint' => ['required'],
            'mapPos' => ['required'],
            'logo' => ['required'],
            'serviceFee' => ['max:5'],
            'deliveryFee' => ['max:5'],
            'brief' => ['max:200']
        );

        $messages = array(
            'mobile.required' => 30601, // 请输入手机号码
            'mobile.regex' => 30602, // 手机号码格式错误
            'serviceTel.regex' => 30924,
            'name.required' => 30604,
            'name.max' => 30922,
            'contacts.max' => 30923,
            'pwd.max' => 30921,
            'provinceId.min' => 30607,
            'cityId.min' => 30609,
            'areaId.min' => 30611,
            'address.required' => 30613,
            'idcardSn.required' => 10114,
            'idcardSn.regex' => 10115,
            'idcardPositiveImg.required' => 10116,
            'idcardNegativeImg.required' => 10117,
            'mapPoint.required' => 30614, // 请选择地图定位
            'mapPos.required' => 30616, // 请选择服务范围
            'logo.required' => 10110,
            'serviceFee.max' => 10617,
            'brief.max' => 30923,
            'deliveryFee.max' => 10618,
        );

        $validator = Validator::make([
                    'mobile' => $mobile,
                    'name' => $name,
                    'provinceId' => $provinceId,
                    'cityId' => $cityId,
                    'areaId' => $areaId,
                    'address' => $address,
                    'idcardSn' => $idcardSn,
                    'idcardPositiveImg' => $idcardPositiveImg,
                    'idcardNegativeImg' => $idcardNegativeImg,
                    'mapPoint' => $mapPoint,
                    'mapPos' => $mapPos,
                    'logo' => $logo,
                    'serviceFee' => $serviceFee,
                    'deliveryFee' => $deliveryFee,
                    'contacts' => $contacts,
                    'serviceTel' => $serviceTel,
                    'brief' => $brief,
                        ], $rules, $messages);

        //验证信息
        if ($validator->fails()) {
            $messages = $validator->messages();
            $result['code'] = $messages->first();

            return $result;
        }

        $mapPoint = Helper::foramtMapPoint($mapPoint);
        if (!$mapPoint) {
            $result['code'] = 30615;    // 地图定位错误
            return $result;
        }

        $mapPos = Helper::foramtMapPos($mapPos);

        if (!$mapPos) {
            $result['code'] = 30617;    // 服务范围错误
            return $result;
        }

        /*
          $resu = SellerService::isCreditNo($idcardSn);
          if(!$resu || 1){
          $result['code'] = 10115;    // 身份证号格式错误
          return $result;
          } */
        if ($id > 0) {
            $seller = Seller::find($id);
            if (!$seller) {//服务站不存在
                $result['code'] = 30211;

                return $result;
            }
        } else {
            $seller = new Seller();
            $seller->type = $type;
        }

        $seller_id = (int) Seller::where("mobile", $mobile)->pluck('id');
        if ($seller_id > 0 && $id != $seller_id) {
            $result['code'] = 30603;    // 手机号码已被注册
            return $result;
        }

        DB::beginTransaction();
        try {
            //查找手机号码对应的会员编号
            $mobile_user_id = (int) User::where('mobile', $mobile)->pluck('id');
            //要更新会员的信息
            $update_user_info = [];

            if ($mobile_user_id > 0) {
                //会员存在且在员工表也同时存在
                $staff_check = (int) SellerStaff::where('user_id', $mobile_user_id)->where('seller_id', '!=', (int) $seller->id)->pluck('id');
                if ($staff_check > 0) {
                    $result['code'] = 50215;    // 手机号码已被注册
                    return $result;
                }
            }

            //当为修改服务站时
            if ($id > 0) {
                //如果修改手机号码
                $seller_old_user_id = $seller->user_id;
                if ($seller->mobile != $mobile) {
                    //如果被注册,则更新服务人员关联的会员编号
                    if ($mobile_user_id > 0) {
                        $seller->user_id = $mobile_user_id;
                    } else {//如果未被注册,则修改关联会员的手机号码
                        $update_user_info['mobile'] = $mobile;
                    }
                }

                if (!empty($pwd)) {//当为修改密码时
                    $crypt = String::randString(6);
                    $pwd = md5(md5($pwd) . $crypt);

                    $update_user_info['crypt'] = $crypt;
                    $update_user_info['pwd'] = $pwd;
                }
            } else {//当为新增服务人员时
                if ($mobile_user_id < 1) {//当手机号没有被注册时,创建会员
                    if (empty($pwd)) {
                        $result['code'] = 30629;

                        return $result;
                    }

                    $user = new User();
                    $user->mobile = $mobile;
                    $user->name_match = String::strToUnicode($name . $mobile);
                    $user->name = $name;
                    $user->reg_time = UTC_TIME;
                    $user->reg_ip = CLIENT_IP;
                    $user->province_id = $provinceId;
                    $user->city_id = $cityId;
                    $user->area_id = $areaId;
                    $user->is_sms_verify = 1;
                    $user->save();
                    $seller->user_id = $user->id;
                } else {
                    $seller->user_id = $mobile_user_id;
                }

                if (!empty($pwd)) {
                    $crypt = String::randString(6);
                    $pwd = md5(md5($pwd) . $crypt);
                    $update_user_info['crypt'] = $crypt;
                    $update_user_info['pwd'] = $pwd;
                }

                $seller->create_time = UTC_TIME;
                $seller->create_day = UTC_DAY;
            }

            //当会员数据有更新项时
            if (count($update_user_info) > 0) {
                User::where('id', $seller->user_id)->update($update_user_info);
            }
            $deduct = (double) $deduct;
            $serviceFee = (double) $serviceFee;
            $deliveryFee = (double) $deliveryFee;
            if ($deduct > 100) {
                $result['code'] = 80220; //佣金比例只能是 0% ~ 100% 范围
                return $result;
            }

            $seller->mobile = $mobile;
            $seller->name = $name;
            $seller->name_match = String::strToUnicode($name . $mobile);
            $seller->contacts = $contacts;
            $seller->address = $address;
            $seller->province_id = $provinceId;
            $seller->city_id = $cityId;
            $seller->area_id = $areaId;
            $seller->brief = $brief;
            $seller->service_fee = $serviceFee;
            $seller->delivery_fee = $deliveryFee;
            $seller->is_authenticate = (int) $isAuthenticate;
            $seller->service_tel = $serviceTel;
            $seller->map_point_str = $mapPoint;
            $seller->map_pos_str = $mapPos["str"];
            $seller->map_point = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
            $seller->map_pos = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
            $seller->is_check = 1;
            $seller->deduct = $deduct;
            $seller->is_cash_on_delivery = $isCashOnDelivery;
            $seller->save();

            $logo = self::moveSellerImage($seller->id, $logo);
            if (!$logo) {//转移图片失败
                $result['code'] = 10202;
                return $result;
            }
            if (!empty($image)) {
                $image = self::moveSellerImage($seller->id, $image);
                if (!$image) {//转移图片失败
                    $result['code'] = 10202;
                    return $result;
                }
            }
            $seller->image = $image;
            $seller->logo = $logo;
            $seller->save();

            /*
              $seller_idcard = SellerAuthenticate::where('idcard_sn', $idcardSn)->where('seller_id', '!=', $seller->id)->first();
              if($seller_idcard || 1){
              $result['code'] = 30621;    //身份证号码已存在
              DB::rollback();
              return $result;
              } */

            $idcardPositiveImg = self::moveSellerImage($seller->id, $idcardPositiveImg);
            if (!$idcardPositiveImg) {//转移图片失败
                $result['code'] = 10202;
                return $result;
            }

            $idcardNegativeImg = self::moveSellerImage($seller->id, $idcardNegativeImg);
            if (!$idcardNegativeImg) {//转移图片失败
                $result['code'] = 10202;
                return $result;
            }

            if ($seller->type == Seller::SERVICE_ORGANIZATION) {
                if ($businessLicenceImg == false) {
                    $result['code'] = 10207; // 公司营业执照相片不能为空
                    return $result;
                }

                $businessLicenceImg = self::moveSellerImage($seller->id, $businessLicenceImg);

                if (!$businessLicenceImg) {//转移图片失败
                    $result['code'] = 10202;
                    return $result;
                }
                $businessLicenceImg = $businessLicenceImg;
            }
            if ($seller->type == Seller::SELF_ORGANIZATION) {
                if ($certificateImg == false) {
                    $result['code'] = 30626; // 资质证书不能为空
                    return $result;
                }

                $certificateImg = self::moveSellerImage($seller->id, $certificateImg);

                if (!$certificateImg) {//转移图片失败
                    $result['code'] = 10202;
                    return $result;
                }
                $certificateImg = $certificateImg;
            }

            if ($id > 0) {
                SellerAuthenticate::where('seller_id', $seller->id)->update([
                    'idcard_sn' => $idcardSn, 'idcard_positive_img' => $idcardPositiveImg, 'idcard_negative_img' => $idcardNegativeImg, 'business_licence_img' => $businessLicenceImg, 'update_time' => UTC_TIME, 'certificate_img' => $certificateImg
                ]);
            } elseif ($id < 1) {
                SellerAuthenticate::insert([
                    'seller_id' => $seller->id, 'idcard_sn' => $idcardSn, 'idcard_positive_img' => $idcardPositiveImg, 'idcard_negative_img' => $idcardNegativeImg, 'business_licence_img' => $businessLicenceImg, 'update_time' => UTC_TIME, 'certificate_img' => $certificateImg
                ]);
            }
            //员工获取商家关联的会员id
            $staffUserId = $seller->user_id;
            // if($seller->type == Seller::SELF_ORGANIZATION){
            //如果个人加盟版 则保存至员工表
            //个人加盟、商家加盟都生成一个员工
            if ($id < 1) {
                $staff = new SellerStaff();
                $staff->seller_id = $seller->id;
                $staff->create_time = UTC_TIME;
                $staff->create_day = UTC_DAY;
                if ($seller->type == Seller::SELF_ORGANIZATION) {
                    $staff->type = 0;
                } else {
                    $staff->type = 3;
                }
            } else {
                $staff = SellerStaff::where('seller_id', $seller->id)->where('user_id', $seller_old_user_id)->first();
            }
            // var_dump($staff);
            // exit;
            $staff->user_id = $staffUserId;
            $staff->avatar = $logo;
            $staff->mobile = $mobile;
            $staff->name = $name;
            $staff->name_match = String::strToUnicode($name . $mobile);
            $staff->address = $address;
            // $staff->map_point          = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
            // $staff->map_point_str      = $mapPoint;
            $staff->province_id = $provinceId;
            $staff->city_id = $cityId;
            $staff->area_id = (int) $areaId;
            $staff->brief = $brief;
            $staff->order_status = 1;
            $staff->save();

            //保存员工扩展信息
            $staffextend = SellerStaffExtend::where('staff_id', $staff->id)->where('seller_id', $seller->id)->first();
            if (!$staffextend) {
                $sellerStaffExtend = new SellerStaffExtend();
                $sellerStaffExtend->staff_id = $staff->id;
                $sellerStaffExtend->seller_id = $seller->id;
                ;
                $sellerStaffExtend->save();
            }

            // }
            //保存扩展信息
            $sellerextend = SellerExtend::where('seller_id', $seller->id)->first();
            if (!$sellerextend) {
                $sellerExtend = new SellerExtend();
                $sellerExtend->seller_id = $seller->id;
                ;
                $sellerExtend->save();
            }

            SellerCateRelated::where('seller_id', $seller->id)->delete();
            $cateIds = is_array($cateIds) ? $cateIds : explode(',', $cateIds);
            foreach ($cateIds as $key => $value) {
                $cate = new SellerCateRelated();
                $cate->seller_id = $seller->id;
                $cate->cate_id = $value;
                $cate->save();
            }

            $deliveryTime = json_decode($deliveryTime, true); //配送时间
            $dtime = SellerDeliveryTime::where('seller_id', $seller->id)->get();
            if ($dtime) {
                SellerDeliveryTime::where('seller_id', $seller->id)->delete();
            }

            foreach ($deliveryTime['stimes'] as $key => $value) {
                $delivery = new SellerDeliveryTime();
                $delivery->seller_id = $seller->id;
                $delivery->stime = $value;
                $delivery->etime = $deliveryTime['etimes'][$key];
                $delivery->save();
            }

            DB::commit();

            // $sellerMap = new SellerMap();
            //  $sellerMap->seller_id       = $seller->id;
            //  $sellerMap->map_point       = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
            //  $sellerMap->map_pos         = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
            //  $sellerMap->save();
            if ($id > 0) {
                SellerMap::where("seller_id", $seller->id)->update([
                    'map_point' => DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')"),
                    'map_pos' => DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')")
                ]);
            } else {
                SellerMap::insert([
                    'seller_id' => $seller->id,
                    'map_point' => DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')"),
                    'map_pos' => DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')")
                ]);
            }
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }

        return $result;
    }

    /**
     * 获取服务人员
     * @param  int $id 服务人员id
     * @return array   服务人员
     */
    public static function getSystemSellerById($id) {
        $seller = Seller::where('id', $id)
                ->with('province', 'city', 'area', 'authenticate', 'banks', 'sellerCate.cates', 'deliveryTimes', 'district')
                ->first();

        return $seller;
    }

    /**
     * 删除服务人员
     * @param int $id 服务人员id
     * @return array   删除结果
     */
    public static function deleteSystem($id) {
        $result = ['code' => 0,
            'data' => null,
            'msg' => ""
        ];

        $seller = Seller::find($id);

        if (!$seller) {
            $result['code'] = 30630;
            return $result;
        }

        DB::beginTransaction();
        try {
            //删除机构或个人
            Seller::destroy($id);
            //删除员工
            \YiZan\Models\SellerStaff::where('seller_id', $id)->delete();
            //删除银行
            \YiZan\Models\SellerBank::where('seller_id', $id)->delete();
            //删除扩展
            \YiZan\Models\SellerExtend::where('seller_id', $id)->delete();
            //删除资金日志
            \YiZan\Models\SellerMoneyLog::where('seller_id', $id)->delete();
            //删除认证信息
            \YiZan\Models\SellerAuthenticate::where('seller_id', $id)->delete();
            //删除员工扩展信息
            \YiZan\Models\SellerStaffExtend::where('seller_id', $id)->delete();
            // 删除员工预约信息
            \YiZan\Models\SellerCateRelated::where('seller_id', $id)->delete();
            // 删除配送时间
            \YiZan\Models\SellerDeliveryTime::where('seller_id', $id)->delete();
            //删除提现
            \YiZan\Models\SellerWithdrawMoney::where('seller_id', $id)->delete();
            //删除员工请假
            \YiZan\Models\StaffLeave::where('seller_id', $id)->delete();
            //删除服务时间
            \YiZan\Models\StaffServiceTime::where('seller_id', $id)->delete();
            \YiZan\Models\StaffServiceTimeSet::where('seller_id', $id)->delete();

            //删除服务商品相关
            \YiZan\Models\Goods::where('seller_id', $id)->delete();
            \YiZan\Models\GoodsCate::where('seller_id', $id)->delete();
            \YiZan\Models\GoodsExtend::where('seller_id', $id)->delete();
            \YiZan\Models\GoodsNorms::where('seller_id', $id)->delete();
            \YiZan\Models\GoodsStaff::where('seller_id', $id)->delete();

            //删除物业相关
            \YiZan\Models\PropertyBuilding::where('seller_id', $id)->delete();
            \YiZan\Models\PropertyRoom::where('seller_id', $id)->delete();
            $puserId = \YiZan\Models\PropertyUser::where('seller_id', $id)->pluck('id');
            \YiZan\Models\PuserDoor::where('puser_id', $id)->delete();
            \YiZan\Models\DoorOpenLog::where('seller_id', $id)->delete();
            \YiZan\Models\PropertyUser::where('seller_id', $id)->delete();
            \YiZan\Models\District::where('seller_id', $id)->delete();


            DB::commit();

            //删除地图
            \YiZan\Models\SellerMap::where('seller_id', $id)->delete();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }

        return $result;
    }

    /**
     * 查询所有的服务站
     */
    public static function all() {
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => ""
        ];

        $result['data'] = Seller::all();
        return $result;
    }

    public static function updateCheckStatus($id, $status, $content, $deduct) {

        $result = [
            'code' => 0,
            'data' => null,
            'msg' => ""
        ];

        $seller = Seller::find($id);
        if (!$seller) {
            $result['code'] = 30630;
            return $result;
        }

        if ($status == 1) {
            $model = 'seller.reg.success';
        } else if ($status == -1) {
            if (empty($content)) {
                $result['code'] = 40105;
                return $result;
            }
            $model = 'seller.reg.reject';
        }

        Seller::where('id', $id)->update(['is_check' => $status, 'check_val' => $content, 'deduct' => $deduct]);

        if ($status != 0) {
            PushMessageService::notice($seller->user_id, $seller->mobile, $model, '', ['sms', 'app'], 'seller', 1, 0, '');
        }

        return $result;
    }

    /**
     * 更新商家状态
     */
    public static function updateStatus($id, $status, $field) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => ""
        ];

        $seller = Seller::find($id);
        if (!$seller) {
            $result['code'] = 30630;
            return $result;
        }

        Seller::where('id', $id)->update([$field => $status]);
        return $result;
    }

    public static function updateProStatus($id, $isCheck, $content) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => ""
        ];

        $seller = Seller::find($id);
        if (!$seller) {
            $result['code'] = 30630;
            return $result;
        }

        if ($isCheck == -1) {
            if (empty($content)) {
                $result['code'] = 40105;
                return $result;
            }
        }

        Seller::where('id', $id)->update(['is_check' => $isCheck, 'check_val' => $content]);

        return $result;
    }

    public static function updateBankInfo($sellerId, $bank, $bankNo, $mobile, $name, $verifyCode) {

        $result = array(
            'code' => self::SUCCESS,
            'data' => null,
            'msg' => ''
        );

        $rules = array(
            'bank' => ['required'],
            'bank_no' => ['required'],
            'mobile' => ['required', 'mobile'],
            'name' => ['required'],
            'verifyCode' => ['required', 'max:6']
        );
        $messages = array(
            'bank.required' => 10150, // 请输入银行 
            'bank_no.required' => 10151, // 请输入银行卡号
            'mobile.required' => 10101,
            'mobile.mobile' => 10156,
            'name.required' => 30604,
            'verifyCode.required' => 10155,
            'verifyCode.max' => 10154
        );
        $validata = array(
            'seller_id' => $sellerId,
            'bank' => $bank,
            'bank_no' => $bankNo,
            'mobile' => $mobile,
            'name' => $name,
            'verifyCode' => $verifyCode
        );
        $validator = Validator::make($validata, $rules, $messages);

        //验证信息
        if ($validator->fails()) {
            $messages = $validator->messages();
            $result['code'] = $messages->first();

            return $result;
        }
        //检测验证码
        $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_BANKINFO);
        if (!$verifyCodeId) {
            $result['code'] = 10153;
            return $result;
        }
        $banks = SellerBank::where('bank_no', $bankNo)->where('name', $name)->first();
        if ($banks) { //银行卡已存在
            $result['code'] = 10106;
            return $result;
        }
        $bankObj = new SellerBank();
        $bankObj->seller_id = $sellerId;
        $bankObj->bank = $bank;
        $bankObj->bank_no = $bankNo;
        $bankObj->mobile = $mobile;
        $bankObj->name = $name;
        $bankObj->save();

        $result['data'] = SellerBank::where('seller_id', $sellerId)->get()->toArray();
        return $result;
    }

    public static function deleteBankInfo($id, $sellerId) {
        $result = ['code' => 0,
            'data' => null,
            'msg' => ""
        ];

        $seller = Seller::find($sellerId);

        if (!$seller) {
            $result['code'] = 30630;
            return $result;
        }

        SellerBank::where('seller_id', $sellerId)->where('id', $id)->delete();

        return $result;
    }

    /**
     * 获取物业列表
     */
    public static function getPropertysList($name, $districtName, $provinceId, $cityId, $areaId, $status, $isTotal, $page, $pageSize) {
        $list = Seller::orderBy('seller.id', 'DESC')
                ->where('seller.type', 3); //物业

        if ($name == true) {
            $list->where('seller.name', $name);
        }

        if ($districtName == true || $provinceId == true || $cityId == true || $areaId == true) {
            $list->join('district', function($join) use($districtName, $provinceId, $cityId, $areaId) {
                $join->on('district.seller_id', '=', 'seller.id');
                if ($districtName == true) {
                    $join->where('district.name', 'like', "%{$districtName}%");
                }
                if ($provinceId == true) {
                    $join->where('district.province_id', '=', $provinceId);
                }
                if ($cityId == true) {
                    $join->where('district.city_id', '=', $cityId);
                }
                if ($areaId == true) {
                    $join->where('district.area_id', '=', $areaId);
                }
            });
        }

        if ($status > 0) { // 审核状态
            $list->where('seller.is_check', $status - 2);
        }

        $totalCount = $list->count();

        if ($isTotal) {
            $list = $list->with('district')
                    ->get()
                    ->toArray();
        } else {
            // DB::connection()->enableQueryLog();
            $list = $list->select('seller.*')
                    ->skip(($page - 1) * $pageSize)
                    ->take($pageSize)
                    ->with('district')
                    ->get()
                    ->toArray();
            // print_r(DB::getQueryLog());
        }
        return ["list" => $list, "totalCount" => $totalCount];
    }

    /**
     * 保存物业信息
     */
    public static function createProperty($id = 0, $name, $mobile, $pwd, $contacts, $districtId, $serviceTel, $idcardSn, $idcardPositiveImg, $idcardNegativeImg, $businessLicenceImg) {
        $pwd = strval($pwd);
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.create_property')
        );

        $rules = array(
            'mobile' => ['required', 'regex:/^1[0-9]{10}$/'],
            // 'pwd'                           => ['required','min:6','max:20'],
            'name' => ['required'],
            'contacts' => ['required'],
            'districtId' => ['required'],
            'idcardSn' => ['required', 'regex:/^[0-9]{18}|[0-9]{15}|[0-9]{17}[xX]{1}$/'],
            'idcardPositiveImg' => ['required'],
            'idcardNegativeImg' => ['required'],
            'businessLicenceImg' => ['required'],
        );

        $messages = array(
            'mobile.required' => '10101',
            'mobile.regex' => '60009',
            //  'pwd.required'                  => '10105',
            //  'pwd.min'                       => '10106',
            //  'pwd.max'                       => '10106',
            'name.required' => '30023',
            'contacts.required' => '30024',
            'districtId.min' => '30025',
            'idcardSn.required' => '10114',
            'idcardSn.regex' => '10115',
            'idcardPositiveImg.required' => '10116',
            'idcardNegativeImg.required' => '10117',
            'businessLicenceImg.required' => '10207',
        );

        $validator = Validator::make([
                    'mobile' => $mobile,
                    // 'pwd'                   => $pwd,
                    'name' => $name,
                    'contacts' => $contacts,
                    'provinceId' => $provinceId,
                    'cityId' => $cityId,
                    'areaId' => $areaId,
                    'districtId' => $districtId,
                    'idcardSn' => $idcardSn,
                    'idcardPositiveImg' => $idcardPositiveImg,
                    'idcardNegativeImg' => $idcardNegativeImg,
                    'businessLicenceImg' => $idcardNegativeImg,
                        ], $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        if ($id > 0) {
            $seller = Seller::find($id);
            if (!$seller) {//服务站不存在
                $result['code'] = 30211;
                return $result;
            }
        } else {
            $seller = new Seller();
            $seller->type = 3;
        }

        $seller_id = (int) Seller::where("mobile", $mobile)->pluck('id');
        if ($seller_id > 0 && $id != $seller_id) {
            $result['code'] = 30603;    // 手机号码已被注册
            return $result;
        }

        preg_match('/^1[0-9]{10}$/', $serviceTel, $arr);

        if ($serviceTel == true && empty($arr)) {
            $result['code'] = 30022;
            return $result;
        }

        DB::beginTransaction();
        try {
            //查找手机号码对应的会员编号
            $mobile_user_id = (int) User::where('mobile', $mobile)->pluck('id');
            //要更新会员的信息
            $update_user_info = [];
            //当为修改服务站时
            if ($id > 0) {
                //如果修改手机号码
                if ($seller->mobile != $mobile) {
                    //如果被注册,则更新服务人员关联的会员编号
                    if ($mobile_user_id > 0) {
                        $seller->user_id = $mobile_user_id;
                    } else {//如果未被注册,则修改关联会员的手机号码
                        $update_user_info['mobile'] = $mobile;
                    }
                }

                if (!empty($pwd)) {//当为修改密码时
                    $crypt = String::randString(6);
                    $pwd = md5(md5($pwd) . $crypt);

                    $update_user_info['crypt'] = $crypt;
                    $update_user_info['pwd'] = $pwd;
                }
            } else {//当为新增服务人员时
                if ($mobile_user_id < 1) {//当手机号没有被注册时,创建会员
                    if (empty($pwd)) {
                        $result['code'] = 30629;
                        return $result;
                    }

                    $user = new User();
                    $user->mobile = $mobile;
                    $user->name_match = String::strToUnicode($name . $mobile);
                    $user->name = $name;
                    $user->reg_time = UTC_TIME;
                    $user->reg_ip = CLIENT_IP;
                    $user->province_id = $provinceId;
                    $user->city_id = $cityId;
                    $user->area_id = $areaId;
                    $user->is_sms_verify = 1;
                    $user->save();
                    $seller->user_id = $user->id;
                } else {
                    $seller->user_id = $mobile_user_id;
                }

                if (!empty($pwd)) {
                    $crypt = String::randString(6);
                    $pwd = md5(md5($pwd) . $crypt);
                    $update_user_info['crypt'] = $crypt;
                    $update_user_info['pwd'] = $pwd;
                }

                $seller->create_time = UTC_TIME;
                $seller->create_day = UTC_DAY;
            }

            //当会员数据有更新项时
            if (count($update_user_info) > 0) {
                User::where('id', $seller->user_id)->update($update_user_info);
            }

            //$seller->type             = 3;//物业
            // $seller->user_id          = $user->id;
            $seller->mobile = $mobile;
            $seller->name = $name;
            $seller->name_match = String::strToUnicode($name . $mobile);
            $seller->service_tel = $serviceTel;
            //$seller->district_id 	  = $districtId;
            // $seller->map_pos          = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
            // $seller->create_time      = UTC_TIME;
            // $seller->create_day       = UTC_DAY;
            $seller->contacts = $contacts;
            $seller->is_check = 1;
            // $seller->status 		  = 1;
            $seller->save();

            //如果是添加多个小区
            if (is_array($districtId)) {
                foreach ($districtId as $did) {

                    $district = District::find($districtId);

                    if (empty($district)) {
                        $result['code'] = 30027;
                        return $result;
                    }

                    $district->seller_id = $seller->id;
                    $district->save();
                }
            } else {
                $district = District::find($districtId);

                if (empty($district)) {
                    $result['code'] = 30027;
                    return $result;
                }

                $district->seller_id = $seller->id;
                $district->save();
            }

            //保存小区的地址信息到物业信息
            $seller->address = $district->address;
            $seller->map_point_str = $district->map_point_str;
            $seller->map_point = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', str_replace(',', ' ', $district->map_point_str)) . ")')");
            $seller->province_id = $district->province_id;
            $seller->city_id = $district->city_id;
            $seller->area_id = $district->area_id;
            $seller->save();

            $seller_idcard = SellerAuthenticate::where('idcard_sn', $idcardSn)->where('seller_id', '!=', $seller->id)->first();
            if ($seller_idcard) {
                $result['code'] = 30621;    //身份证号码已存在
                DB::rollback();
                return $result;
            }

            $idcardPositiveImg = self::moveSellerImage($seller->id, $idcardPositiveImg);
            if (!$idcardPositiveImg) {//转移图片失败
                $result['code'] = 10203;
                return $result;
            }

            $idcardNegativeImg = self::moveSellerImage($seller->id, $idcardNegativeImg);
            if (!$idcardNegativeImg) {//转移图片失败
                $result['code'] = 10204;
                return $result;
            }

            if ($businessLicenceImg == false) {
                $result['code'] = 10207; // 公司营业执照相片不能为空
                return $result;
            }

            $businessLicenceImg = self::moveSellerImage($seller->id, $businessLicenceImg);

            if (!$businessLicenceImg) {//转移图片失败
                $result['code'] = 10204;
                return $result;
            }

            if ($id > 0) {
                $auth = SellerAuthenticate::where('seller_id', $id)->first();
            } else {
                $auth = new SellerAuthenticate();
                $auth->seller_id = $seller->id;
            }
            $auth->idcard_sn = $idcardSn;
            // $auth->real_name            = $name;
            $auth->idcard_positive_img = $idcardPositiveImg;
            $auth->idcard_negative_img = $idcardNegativeImg;
            // $auth->business_licence_sn  = $businessLicenceSn;
            $auth->business_licence_img = $businessLicenceImg;
            $auth->update_time = UTC_TIME;
            $auth->save();

            //更新所有在小区关联物业公司之前的业主
            PropertyUser::where('district_id', $districtId)->update(['seller_id' => $seller->id]);
            DB::commit();
            $result['data'] = Seller::find($seller->id);
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 10119;
        }
        return $result;
    }

    /**
     * 更新物业信息
     */
    public static function updateProperty($id, $name, $mobile, $pwd, $contacts, $districtId, $serviceTel, $idcardSn, $idcardPositiveImg, $idcardNegativeImg, $businessLicenceImg) {
        $pwd = strval($pwd);
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.update_property')
        );

        $rules = array(
            'name' => ['required', 'min:2', 'max:10'],
            'contacts' => ['required'],
            'districtId' => ['required'],
            'idcardSn' => ['required', 'regex:/^[0-9]{18}|[0-9]{15}|[0-9]{17}[xX]{1}$/'],
            'idcardPositiveImg' => ['required'],
            'idcardNegativeImg' => ['required'],
            'businessLicenceImg' => ['required'],
        );

        $messages = array(
            'name.required' => '30023',
            'contacts.required' => '30023',
            'districtId.min' => '30025',
            'idcardSn.required' => '10114',
            'idcardSn.regex' => '10115',
            'idcardPositiveImg.required' => '10116',
            'idcardNegativeImg.required' => '10117',
            'businessLicenceImg.required' => '10207',
        );

        $validator = Validator::make([
                    'mobile' => $mobile,
                    'pwd' => $pwd,
                    'name' => $name,
                    'contacts' => $contacts,
                    'districtId' => $districtId,
                    'idcardSn' => $idcardSn,
                    'idcardPositiveImg' => $idcardPositiveImg,
                    'idcardNegativeImg' => $idcardNegativeImg,
                    'businessLicenceImg' => $idcardNegativeImg,
                        ], $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        preg_match('/^1[0-9]{10}$/', $serviceTel, $arr);

        if ($serviceTel == true && empty($arr)) {
            $result['code'] = 30022;
            return $result;
        }

        $seller = Seller::find($id);
        if (!$seller) {//服务人员不存在
            $result['code'] = 30026;
            return $result;
        }

        $district = District::find($districtId);

        if (empty($district)) {
            $result['code'] = 30027;
            return $result;
        }

        $seller->mobile = $mobile;
        $seller->name = $name;
        $seller->address = $district->address;
        $seller->service_tel = $serviceTel;
        // $seller->map_pos          = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
        $seller->create_time = UTC_TIME;
        $seller->create_day = UTC_DAY;
        $seller->contacts = $contacts;
        // if($status != 0){
        // 	$seller->status 	  = $status;
        // } 
        //如果是添加多个小区
        if (is_array($districtId)) {
            foreach ($districtId as $did) {

                $district = District::find($districtId);

                if (empty($district)) {
                    $result['code'] = 30027;
                    return $result;
                }

                $district->seller_id = $seller->id;
            }
        } else {
            $district = District::find($districtId);

            if (empty($district)) {
                $result['code'] = 30027;
                return $result;
            }

            $district->seller_id = $seller->id;
        }

        //保存小区的地址信息到物业信息
        $seller->address = $district->address;
        $seller->map_point_str = $district->map_point_str;
        $seller->map_point = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', str_replace(',', ' ', $district->map_point_str)) . ")')");
        $seller->province_id = $district->province_id;
        $seller->city_id = $district->city_id;
        $seller->area_id = $district->area_id;

        $seller_idcard = SellerAuthenticate::where('idcard_sn', $idcardSn)->where('seller_id', '!=', $id)->first();
        if ($seller_idcard) {
            $result['code'] = 30621;    //身份证号码已存在
            DB::rollback();
            return $result;
        }

        $idcardPositiveImg = self::moveSellerImage($seller->id, $idcardPositiveImg);
        if (!$idcardPositiveImg) {//转移图片失败
            $result['code'] = 10203;
            return $result;
        }

        $idcardNegativeImg = self::moveSellerImage($seller->id, $idcardNegativeImg);
        if (!$idcardNegativeImg) {//转移图片失败
            $result['code'] = 10204;
            return $result;
        }

        if ($businessLicenceImg == false) {
            $result['code'] = 10207; // 公司营业执照相片不能为空
            return $result;
        }

        $businessLicenceImg = self::moveSellerImage($seller->id, $businessLicenceImg);

        if (!$businessLicenceImg) {//转移图片失败
            $result['code'] = 10204;
            return $result;
        }

        if ($seller->save()) {
            $auth = SellerAuthenticate::where('seller_id', $seller->id)->first();
            $auth->idcard_sn = $idcardSn;
            // $auth->real_name            = $name;
            $auth->idcard_positive_img = $idcardPositiveImg;
            $auth->idcard_negative_img = $idcardNegativeImg;
            // $auth->business_licence_sn  = $businessLicenceSn;
            $auth->business_licence_img = $businessLicenceImg;
            $auth->update_time = UTC_TIME;
            $auth->save();
            return $result;
        } else {
            $result['code'] = 99999;
            return $result;
        }

        return $result;
    }

    /**
     * 商家营业统计列表
     */
    public static function getBusinessStatisticsList($name, $month, $year, $cityId, $page, $pageSize) {
        //DB::connection()->enableQueryLog();
        $prefix = DB::getTablePrefix();

        $sumsql = "SELECT IFNULL(sum(" . $prefix . "order.total_fee), 0) as totalPayfee,
					count(" . $prefix . "order.id) as totalNum,
					IFNULL(sum(" . $prefix . "order.drawn_fee), 0) as totalDrawnfee ,IFNULL((select SUM(od.pay_fee) from " . $prefix . "order as od where FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m') = '" . $year . "-" . sprintf("%02d", $month) . "' and od.pay_status = 1 and od.pay_type <> 'cashOnDelivery' AND (od.status in (" . ORDER_STATUS_FINISH_SYSTEM . ", " . ORDER_STATUS_FINISH_USER . ") OR (od.status = " . ORDER_STATUS_USER_DELETE . " and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = " . ORDER_STATUS_SELLER_DELETE . " and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = " . ORDER_STATUS_ADMIN_DELETE . " and od.auto_finish_time > 0 AND od.cancel_time IS NULL)))  , 0) as totalOnline,
					IFNULL((select SUM(od.pay_fee) from " . $prefix . "order as od where FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m') = '" . $year . "-" . sprintf("%02d", $month) . "' and od.pay_status = 1 and od.pay_type <> '' and od.pay_type = 'cashOnDelivery' AND (od.status in (" . ORDER_STATUS_FINISH_SYSTEM . ", " . ORDER_STATUS_FINISH_USER . ") OR (od.status = " . ORDER_STATUS_USER_DELETE . " and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = " . ORDER_STATUS_SELLER_DELETE . " and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = " . ORDER_STATUS_ADMIN_DELETE . " and od.auto_finish_time > 0 AND od.cancel_time IS NULL))) , 0) as totalCash, 
					IFNULL(sum(IF(discount_fee > total_fee, total_fee, discount_fee)), 0) as totalDiscountFee
					FROM " . $prefix . "order
					WHERE pay_status = 1 
					AND (status IN (" . ORDER_STATUS_FINISH_SYSTEM . ", " . ORDER_STATUS_FINISH_USER . ")
					OR (status = " . ORDER_STATUS_USER_DELETE . " AND buyer_finish_time > 0 AND cancel_time IS NULL) 
					OR (status = " . ORDER_STATUS_SELLER_DELETE . " AND auto_finish_time > 0 AND cancel_time IS NULL) 
					OR (status = " . ORDER_STATUS_ADMIN_DELETE . " AND auto_finish_time > 0 AND cancel_time IS NULL))
					AND FROM_UNIXTIME(" . $prefix . "order.buyer_finish_time,'%Y-%m') = '" . $year . "-" . sprintf("%02d", $month) . "'";

        $sum = DB::select($sumsql);

        /**
          sql说明：左连接order表 查询 id, name, totalPayfee(总金额), totalNum(总数量), totalDrawnfee(总佣金), totalOnline(总在线支付), totalCash(总现金支付), totalDiscountFee(总优惠金额)
          在线和现金支付数据 通过子查询实现
         */
        $sql = "select " . $prefix . "seller.id, 
				" . $prefix . "seller.name, 
				IFNULL(sum(" . $prefix . "order.total_fee), 0) as totalPayfee,
				count(" . $prefix . "order.id) as totalNum,
				IFNULL(sum(" . $prefix . "order.drawn_fee), 0) as totalDrawnfee ,
				IFNULL((select SUM(od.pay_fee) from " . $prefix . "order as od where od.seller_id = " . $prefix . "seller.id and FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m') = '" . $year . "-" . sprintf("%02d", $month) . "' and od.pay_status = 1 and od.pay_type <> 'cashOnDelivery' AND (od.status in (" . ORDER_STATUS_FINISH_SYSTEM . ", " . ORDER_STATUS_FINISH_USER . ") OR (od.status = " . ORDER_STATUS_USER_DELETE . " and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = " . ORDER_STATUS_SELLER_DELETE . " and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = " . ORDER_STATUS_ADMIN_DELETE . " and od.auto_finish_time > 0 AND od.cancel_time IS NULL))), 0) as totalOnline,
				IFNULL((select SUM(od.pay_fee) from " . $prefix . "order as od where od.seller_id = " . $prefix . "seller.id and FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m') = '" . $year . "-" . sprintf("%02d", $month) . "' and od.pay_status = 1 and od.pay_type <> '' and od.pay_type = 'cashOnDelivery' AND (od.status in (" . ORDER_STATUS_FINISH_SYSTEM . ", " . ORDER_STATUS_FINISH_USER . ") OR (od.status = " . ORDER_STATUS_USER_DELETE . " and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = " . ORDER_STATUS_SELLER_DELETE . " and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = " . ORDER_STATUS_ADMIN_DELETE . " and od.auto_finish_time > 0 AND od.cancel_time IS NULL))), 0) as totalCash,
				IFNULL(sum(IF(discount_fee > total_fee, total_fee, discount_fee)), 0) as totalDiscountFee 
				from " . $prefix . "seller left join " . $prefix . "order 
				on " . $prefix . "order.seller_id = " . $prefix . "seller.id 
				and " . $prefix . "order.pay_status = 1 
				and FROM_UNIXTIME(" . $prefix . "order.buyer_finish_time,'%Y-%m') = '" . $year . "-" . sprintf("%02d", $month) . "' 
				and (" . $prefix . "order.status IN (" . ORDER_STATUS_FINISH_SYSTEM . ", " . ORDER_STATUS_FINISH_USER . ") OR (" . $prefix . "order.status = " . ORDER_STATUS_USER_DELETE . " AND " . $prefix . "order.buyer_finish_time > 0 AND " . $prefix . "order.cancel_time IS NULL) OR (" . $prefix . "order.status = " . ORDER_STATUS_SELLER_DELETE . " AND " . $prefix . "order.auto_finish_time > 0 AND " . $prefix . "order.cancel_time IS NULL) OR (" . $prefix . "order.status = " . ORDER_STATUS_ADMIN_DELETE . " AND " . $prefix . "order.auto_finish_time > 0 AND " . $prefix . "order.cancel_time IS NULL))
				group by " . $prefix . "seller.id 
				order by totalPayfee DESC
				limit " . ($page - 1) * $pageSize . ", " . $pageSize;

        $list = DB::select($sql);
        $totalCount = Seller::count();
        // print_r(DB::getQueryLog());exit;
        //file_put_contents('/mnt/test/sq/storage/logs/asdfsdf.log', print_r(DB::getQueryLog(),true)."\n", FILE_APPEND);
        return ["list" => $list, "totalCount" => $totalCount, "sum" => $sum[0]];
    }

    /**
     * 平台信息统计
     */
    public static function getBusinessPlatformInfo($month, $year, $cityId) {
        $prefix = DB::getTablePrefix();
        $current = $year . '-' . sprintf("%02d", $month);
        $t = Time::toDate(Time::toTime($current), 't');
        if ($current == Time::toDate(UTC_TIME, 'Y-m')) {
            $t = Time::toDate(UTC_TIME, 'd');
        } else if (Time::toTime($current) > Time::toTime(Time::toDate(UTC_DAY, 'Y-m'))) {
            return ["list" => [], "sum" => []];
        }
        $sql = "SELECT daytime, 
				sum(regNum) as totalRegNum , 
				sum(drawnFee) as totalDrawnFee , 
				sum(sellerFee) as totalSellerFee , 
				sum(sellerCharge) as totalSellerCharge , 
				sum(buyerCharge) as totalBuyerCharge 
				FROM (
				SELECT 
						FROM_UNIXTIME(reg_time,'%Y-%m-%d') as daytime, 
						COUNT(1) as regNum,
						0 AS drawnFee,
						0 AS sellerFee,
						0 as sellerCharge,
						0 as buyerCharge
				from " . $prefix . "user
				where reg_time <> ''
				and FROM_UNIXTIME(reg_time,'%Y-%m') = '" . $current . "'
				GROUP BY FROM_UNIXTIME(reg_time,'%Y-%m-%d')
				UNION ALL 
				SELECT 
						FROM_UNIXTIME(buyer_finish_time,'%Y-%m-%d') as daytime, 
						0 AS regNum,
						SUM(drawn_fee) as drawnFee,
						0 as sellerFee,
						0 as sellerCharge,
						0 as buyerCharge
				from " . $prefix . "order
				where buyer_finish_time <> ''
				and pay_status = 1
				and status in (" . ORDER_STATUS_FINISH_SYSTEM . ", " . ORDER_STATUS_FINISH_USER . ")
				and FROM_UNIXTIME(buyer_finish_time,'%Y-%m') = '" . $current . "'
				GROUP BY FROM_UNIXTIME(buyer_finish_time,'%Y-%m-%d')
				UNION ALL
				SELECT 
						FROM_UNIXTIME(create_time,'%Y-%m-%d') as daytime, 
						0 AS regNum,
						0 as drawnFee,
						SUM(money) as sellerFee,
						0 as sellerCharge,
						0 as buyerCharge
				from " . $prefix . "seller_money_log
				where create_time <> ''
				and type = 'apply_withdraw'
				and status = 1
				and FROM_UNIXTIME(create_time,'%Y-%m') = '" . $current . "'
				GROUP BY FROM_UNIXTIME(create_time,'%Y-%m-%d')
				UNION ALL
				SELECT 
						FROM_UNIXTIME(pay_time,'%Y-%m-%d') as daytime, 
						0 AS regNum,
						0 as drawnFee,
						0 as sellerFee,
						SUM(money) as sellerCharge,
						0 as buyerCharge
				from " . $prefix . "seller_pay_log
				where pay_time <> ''
				and status = 1
				and FROM_UNIXTIME(pay_time,'%Y-%m') = '" . $current . "'
				GROUP BY FROM_UNIXTIME(pay_time,'%Y-%m-%d')
				UNION ALL
				SELECT 
						FROM_UNIXTIME(pay_time,'%Y-%m-%d') as daytime, 
						0 AS regNum,
						0 as drawnFee,
						0 as sellerFee,
						0 as sellerCharge,
						SUM(money) as buyerCharge
				from " . $prefix . "user_pay_log
				where pay_time <> ''
				and pay_type = 2
				and FROM_UNIXTIME(pay_time,'%Y-%m') = '" . $current . "'
				GROUP BY FROM_UNIXTIME(pay_time,'%Y-%m-%d')
				) as tmp GROUP BY daytime;
				";
        $queryData = DB::select($sql);
        $list = [];
        for ($i = 1; $i <= $t; $i++) {
            $daytime = $current . '-' . sprintf("%02d", $i);
            $dayData = [
                'totalRegNum' => 0,
                'totalDrawnFee' => 0,
                'totalSellerFee' => 0,
                'totalSellerCharge' => 0,
                'totalBuyerCharge' => 0,
                'daytime' => $daytime,
            ];
            $bool = false;
            foreach ($queryData as $item) {
                $item = (array) $item;
                if ($item['daytime'] == $daytime) {
                    $bool = true;
                    break;
                }
            }
            if ($bool) {
                $list[] = $item;
            } else {
                $list[] = $dayData;
            }
        }

        $sumsql = $sql = "SELECT 
        		sum(regNum) as totalRegNum , 
				sum(drawnFee) as totalDrawnFee , 
				sum(sellerFee) as totalSellerFee , 
				sum(sellerCharge) as totalSellerCharge , 
				sum(buyerCharge) as totalBuyerCharge 
				FROM (
				SELECT 
						FROM_UNIXTIME(reg_time,'%Y-%m-%d') as daytime, 
						COUNT(1) as regNum,
						0 AS drawnFee,
						0 AS sellerFee,
						0 as sellerCharge,
						0 as buyerCharge
				from " . $prefix . "user
				where reg_time <> ''
				and FROM_UNIXTIME(reg_time,'%Y-%m') = '" . $current . "' 
				UNION ALL
				SELECT 
						FROM_UNIXTIME(buyer_finish_time,'%Y-%m-%d') as daytime, 
						0 AS regNum,
						SUM(drawn_fee) as drawnFee,
						0 as sellerFee,
						0 as sellerCharge,
						0 as buyerCharge
				from " . $prefix . "order
				where buyer_finish_time <> ''
				and pay_status = 1
				and status in (" . ORDER_STATUS_FINISH_SYSTEM . ", " . ORDER_STATUS_FINISH_USER . ")
				and FROM_UNIXTIME(buyer_finish_time,'%Y-%m') = '" . $current . "' 
				UNION ALL
				SELECT 
						FROM_UNIXTIME(create_time,'%Y-%m-%d') as daytime, 
						0 AS regNum,
						0 as drawnFee,
						SUM(money) as sellerFee,
						0 as sellerCharge,
						0 as buyerCharge
				from " . $prefix . "seller_money_log
				where create_time <> ''
				and type = 'apply_withdraw'
				and status = 1
				and FROM_UNIXTIME(create_time,'%Y-%m') = '" . $current . "'
				GROUP BY FROM_UNIXTIME(create_time,'%Y-%m-%d')
				UNION ALL
				SELECT 
						FROM_UNIXTIME(pay_time,'%Y-%m-%d') as daytime, 
						0 AS regNum,
						0 as drawnFee,
						0 as sellerCharge,
						SUM(money) as sellerCharge,
						0 as buyerCharge
				from " . $prefix . "seller_pay_log
				where pay_time <> ''
				and status = 1
				and FROM_UNIXTIME(pay_time,'%Y-%m') = '" . $current . "' 
				UNION ALL
				SELECT 
						FROM_UNIXTIME(pay_time,'%Y-%m-%d') as daytime, 
						0 AS regNum,
						0 as drawnFee,
						0 as sellerFee,
						0 as sellerCharge,
						SUM(money) as buyerCharge
				from " . $prefix . "user_pay_log
				where pay_time <> ''
				and pay_type = 2
				and FROM_UNIXTIME(pay_time,'%Y-%m') = '" . $current . "' 
				) as tmp;
				";
        $sum = DB::select($sumsql);
        return ["list" => $list, "sum" => $sum[0]];
    }

    /**
     * 平台销售信息统计
     */
    public static function getBusinessPlatformSelling($month, $year, $cityId) {
        // DB::connection()->enableQueryLog();
        $prefix = DB::getTablePrefix();
        $current = $year . '-' . sprintf("%02d", $month);
        $t = Time::toDate(Time::toTime($current), 't');
        if ($current == Time::toDate(UTC_TIME, 'Y-m')) {
            $t = Time::toDate(UTC_TIME, 'd');
        } else if (Time::toTime($current) > Time::toTime(Time::toDate(UTC_DAY, 'Y-m'))) {
            return ["list" => [], "sum" => []];
        }
        $sumsql = " SELECT 
        			IFNULL(sum(total_fee), 0) as totalPayfee, 
        			count(" . $prefix . "order.id) as totalNum,
        			((select count(1) from " . $prefix . "refund INNER JOIN " . $prefix . "order on " . $prefix . "order.id = " . $prefix . "refund.order_id and " . $prefix . "refund.status = 1 and " . $prefix . "order.pay_status = 1 and " . $prefix . "order.status not in (" . ORDER_STATUS_CANCEL_USER . "," . ORDER_STATUS_CANCEL_AUTO . "," . ORDER_STATUS_CANCEL_SELLER . "," . ORDER_STATUS_USER_DELETE . "," . ORDER_STATUS_SELLER_DELETE . "," . ORDER_STATUS_ADMIN_DELETE . ") and FROM_UNIXTIME(" . $prefix . "refund.create_time,'%Y-%m') = '" . $current . "') + (select count(1) from " . $prefix . "order as od where od.status in (" . ORDER_STATUS_CANCEL_USER . "," . ORDER_STATUS_CANCEL_AUTO . "," . ORDER_STATUS_CANCEL_SELLER . "," . ORDER_STATUS_USER_DELETE . "," . ORDER_STATUS_SELLER_DELETE . "," . ORDER_STATUS_ADMIN_DELETE . ") and od.pay_status = 1 and FROM_UNIXTIME(od.cancel_time,'%Y-%m') = '" . $current . "') ) as totalCancleNum,
        			IFNULL(sum(" . $prefix . "order.drawn_fee), 0) as totalDrawnfee ,IFNULL((select SUM(od.pay_fee) from " . $prefix . "order as od where FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m') = '" . $current . "' and od.pay_status = 1 and od.pay_type <> 'cashOnDelivery' AND (od.status in (" . ORDER_STATUS_FINISH_SYSTEM . ", " . ORDER_STATUS_FINISH_USER . ") OR (od.status = " . ORDER_STATUS_USER_DELETE . " and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = " . ORDER_STATUS_SELLER_DELETE . " and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = " . ORDER_STATUS_ADMIN_DELETE . " and od.auto_finish_time > 0 AND od.cancel_time IS NULL))), 0) as totalOnline,
        			IFNULL((select SUM(od.pay_fee) from " . $prefix . "order as od where FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m') = '" . $current . "' and od.pay_status = 1 and od.pay_type <> '' and od.pay_type = 'cashOnDelivery' AND (od.status in (" . ORDER_STATUS_FINISH_SYSTEM . ", " . ORDER_STATUS_FINISH_USER . ") OR (od.status = " . ORDER_STATUS_USER_DELETE . " and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = " . ORDER_STATUS_SELLER_DELETE . " and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = " . ORDER_STATUS_ADMIN_DELETE . " and od.auto_finish_time > 0 AND od.cancel_time IS NULL))), 0) as totalCash,
        			IFNULL(sum(IF(discount_fee > total_fee, total_fee, discount_fee)), 0) as totalDiscountFee
        			from " . $prefix . "order
        			where pay_status = 1 
        			and FROM_UNIXTIME(buyer_finish_time,'%Y-%m') = '" . $current . "'
        			and (status IN (" . ORDER_STATUS_FINISH_SYSTEM . ", " . ORDER_STATUS_FINISH_USER . ") OR (status = " . ORDER_STATUS_USER_DELETE . " AND buyer_finish_time > 0 AND cancel_time IS NULL) OR (status = " . ORDER_STATUS_SELLER_DELETE . " AND auto_finish_time > 0 AND cancel_time IS NULL) OR (status = " . ORDER_STATUS_ADMIN_DELETE . " AND auto_finish_time > 0 AND cancel_time IS NULL))";

        $sum = DB::select($sumsql);
        $sum['seller'] = Seller::find($sellerId);
        /**
          sql说明：左连接order表 查询 id, name, totalPayfee(总金额), totalNum(总数量), totalDrawnfee(总佣金), totalOnline(总在线支付), totalCash(总现金支付), totalDiscountFee(总优惠金额), totalSellerFee(商家盈利)
          在线和现金支付数据 通过子查询实现
         */
        $sql = "SELECT 
        		sum(totalPayfee) as totalPayfee , 
				sum(totalNum) as totalNum , 
				sum(totalCancleNum) as totalCancleNum , 
				sum(totalOnline) as totalOnline , 
				sum(totalCash) as totalCash , 
				sum(totalDiscountFee) as totalDiscountFee , 
				daytime as daytime 
				FROM ( select IFNULL(sum(" . $prefix . "order.total_fee), 0) as totalPayfee,
                count(" . $prefix . "order.id) as totalNum, 
    			0 as totalCancleNum,
                IFNULL((select SUM(od.pay_fee) from " . $prefix . "order as od where FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m-%d') = FROM_UNIXTIME(" . $prefix . "order.buyer_finish_time,'%Y-%m-%d') and od.pay_status = 1 and od.pay_type <> 'cashOnDelivery' AND (od.status in (" . ORDER_STATUS_FINISH_SYSTEM . ", " . ORDER_STATUS_FINISH_USER . ") OR (od.status = " . ORDER_STATUS_USER_DELETE . " and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = " . ORDER_STATUS_SELLER_DELETE . " and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = " . ORDER_STATUS_ADMIN_DELETE . " and od.auto_finish_time > 0 AND od.cancel_time IS NULL))), 0) as totalOnline,
                IFNULL((select SUM(od.pay_fee) from " . $prefix . "order as od where FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m-%d') = FROM_UNIXTIME(" . $prefix . "order.buyer_finish_time,'%Y-%m-%d') and od.pay_status = 1 and od.pay_type <> '' and od.pay_type = 'cashOnDelivery' AND (od.status in (" . ORDER_STATUS_FINISH_SYSTEM . ", " . ORDER_STATUS_FINISH_USER . ") OR (od.status = " . ORDER_STATUS_USER_DELETE . " and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = " . ORDER_STATUS_SELLER_DELETE . " and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = " . ORDER_STATUS_ADMIN_DELETE . " and od.auto_finish_time > 0 AND od.cancel_time IS NULL))), 0) as totalCash,
                IFNULL(sum(IF(discount_fee > total_fee, total_fee, discount_fee)), 0) as totalDiscountFee,
                FROM_UNIXTIME(buyer_finish_time,'%Y-%m-%d') as daytime
                from " . $prefix . "order 
                where " . $prefix . "order.pay_status = 1  
                and FROM_UNIXTIME(" . $prefix . "order.buyer_finish_time,'%Y-%m') = '" . $current . "' 
                AND (" . $prefix . "order.status IN (" . ORDER_STATUS_FINISH_SYSTEM . ", " . ORDER_STATUS_FINISH_USER . ")
                OR (" . $prefix . "order.status = " . ORDER_STATUS_USER_DELETE . " AND " . $prefix . "order.buyer_finish_time > 0 AND " . $prefix . "order.cancel_time IS NULL) 
                OR (" . $prefix . "order.status = " . ORDER_STATUS_SELLER_DELETE . " AND " . $prefix . "order.auto_finish_time > 0 AND " . $prefix . "order.cancel_time IS NULL) 
                OR (" . $prefix . "order.status = " . ORDER_STATUS_ADMIN_DELETE . " AND " . $prefix . "order.auto_finish_time > 0 AND " . $prefix . "order.cancel_time IS NULL))
                GROUP BY FROM_UNIXTIME(buyer_finish_time,'%Y-%m-%d')
                UNION ALL
                select 0 as totalPayfee,
                0 as totalNum,
                ((select count(1) from " . $prefix . "refund INNER JOIN " . $prefix . "order as od on od.id = " . $prefix . "refund.order_id and " . $prefix . "refund.status = 1 and od.pay_status = 1 and od.status not in (" . ORDER_STATUS_CANCEL_USER . "," . ORDER_STATUS_CANCEL_AUTO . "," . ORDER_STATUS_CANCEL_SELLER . "," . ORDER_STATUS_USER_DELETE . "," . ORDER_STATUS_SELLER_DELETE . "," . ORDER_STATUS_ADMIN_DELETE . ") where FROM_UNIXTIME(od.cancel_time,'%Y-%m-%d') = FROM_UNIXTIME(" . $prefix . "order.cancel_time,'%Y-%m-%d')) + (select count(1) from " . $prefix . "order as od where od.status in (" . ORDER_STATUS_CANCEL_USER . "," . ORDER_STATUS_CANCEL_AUTO . "," . ORDER_STATUS_CANCEL_SELLER . "," . ORDER_STATUS_USER_DELETE . "," . ORDER_STATUS_SELLER_DELETE . "," . ORDER_STATUS_ADMIN_DELETE . ") and od.pay_status = 1 and FROM_UNIXTIME(od.cancel_time,'%Y-%m-%d') = FROM_UNIXTIME(" . $prefix . "order.cancel_time,'%Y-%m-%d'))) as totalCancleNum,
                0 as totalOnline,
                0 as totalCash,
                0 as totalDiscountFee,
                FROM_UNIXTIME(cancel_time,'%Y-%m-%d') as daytime 
                from " . $prefix . "order
                where pay_status = 1 
                and FROM_UNIXTIME(cancel_time,'%Y-%m') = '" . $current . "'
                GROUP BY FROM_UNIXTIME(cancel_time,'%Y-%m-%d') ) as tmp
                GROUP by tmp.daytime 
                ";
        $queryData = DB::select($sql);
        $list = [];
        for ($i = 1; $i <= $t; $i++) {
            $daytime = $current . '-' . sprintf("%02d", $i);
            $dayData = [
                'totalPayfee' => 0,
                'totalNum' => 0,
                'totalCancleNum' => 0,
                'totalOnline' => 0,
                'totalCash' => 0,
                'totalDiscountFee' => 0,
                'daytime' => $daytime,
            ];
            $bool = false;
            foreach ($queryData as $item) {
                $item = (array) $item;
                if ($item['daytime'] == $daytime) {
                    $bool = true;
                    break;
                }
            }
            if ($bool) {
                $list[] = $item;
            } else {
                $list[] = $dayData;
            }
        }
        // print_r(DB::getQueryLog());exit;  
        //file_put_contents('/mnt/test/sq/storage/logs/sellerservice1.log', print_r(DB::getQueryLog(),true)."\n", FILE_APPEND);
        return ["list" => $list, "sum" => $sum[0]];
    }

}
