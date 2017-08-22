<?php 
namespace YiZan\Services\Sellerweb;

use YiZan\Models\User;
use YiZan\Models\District;
use YiZan\Models\SellerMap;
use YiZan\Models\UserVerifyCode;
use YiZan\Models\Sellerweb\Seller;
use YiZan\Models\Sellerweb\SellerExtend;
use YiZan\Models\Sellerweb\SellerStaffExtend;
use YiZan\Models\SellerAppoint;
use YiZan\Models\SellerStaff;
use YiZan\Models\SellerCateRelated;
use YiZan\Models\Sellerweb\SellerAppointHour;
use YiZan\Models\Sellerweb\SellerAuthenticate;
use YiZan\Models\Sellerweb\SellerCertificate;
use YiZan\Models\Sellerweb\SellerCreditRank;
use YiZan\Models\SellerComplain;
use YiZan\Models\Sellerweb\SellerMoneyLog;
use YiZan\Models\Sellerweb\SellerWithdrawMoney;
use YiZan\Models\ReadMessage;
use YiZan\Models\Sellerweb\Promotion;
use YiZan\Models\Sellerweb\PromotionSn;
use YiZan\Models\Sellerweb\OrderRate;
use YiZan\Models\Sellerweb\Goods;
use YiZan\Models\Sellerweb\GoodsExtend; 
use YiZan\Models\SellerDeliveryTime;
use YiZan\Models\PropertyUser;
use YiZan\Services\SmsService;
use YiZan\Services\RegionService;
use YiZan\Utils\Http;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use Request, DB, Lang, Validator;

class SellerService extends \YiZan\Services\SellerService 
{
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
     * 根据手机号码获取会员
     * @param  string $mobile 手机号码
     * @return array          会员信息
     */
    public static function getByMobile($mobile) {
        return Seller::where('mobile', $mobile)->first();
    }

    /**
     * 获取卖家
     * @param int $sellerId 卖家id
     * @return object 卖家信息
     */
    public static function getById($sellerId = 0, $userId = 0)
    {
        if($sellerId > 0) {
            return Seller::with('province', 'city', 'area','authenticate', 'banks', 'sellerCate.cates', 'deliveryTimes', 'extend', 'district')->find($sellerId);
        }
        
        if($userId > 0) {
            return Seller::where("user_id", $userId)->with('province', 'city', 'area','authenticate', 'banks', 'sellerCate.cates', 'deliveryTimes', 'extend', 'district')->first();
        }
        return null;
        
    }
  
    /**
     * 创建seller
     * @param  int $sellerType 机构类型
     * @param  [type] $mobile     [description]
     * @param  [type] $verifyCode [description]
     * @param  [type] $pwd        [description]
     * @param  [type] $name     [description]
     * @param  [int] $sex     [description]
     * @param  [type] $avatar     [description]
     * @param  [int] $birthday     [description]
     * @param  [int] $provinceId     [description]
     * @param  [int] $cityId     [description]
     * @param  [int] $areaId     [description]
     * @param  [type] $idcardSn     [description]
     * @param  [type] $idcardPositiveImg     [description]
     * @param  [type] $idcardNegativeImg     [description]
     * @param  [type] $companyName           [description]
     * @param  [type] $businessLicenceSn     [description]
     * @param  [type] $businessLicenceImg     [description]
     * @param string $address 地址
     * @param string $mapPoint 纬度,经度(QQ地图坐标)
     * @param array $mapPos QQ地图坐标数组
     * @param  string $type       [description]
     * @return [type]             [description]
     */
    public static function createSeller($sellerType, $mobile, $verifyCode, $pwd, $name, $avatar, $serviceTel, $districtId, $provinceId, $cityId, $areaId, $idcardSn, $idcardPositiveImg, $idcardNegativeImg, $certificateImg, $businessLicenceImg, $address, $mapPos, $mapPoint, $cateIds, $contacts, $type = 'reg') {

        $pwd = strval($pwd);
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.create_user_'.$type)
        );

        $rules = array(
            'mobile'                        => ['required','regex:/^1[0-9]{10}$/'],
            'code'                          => ['required','size:6'],
            'pwd'                           => ['required','min:6','max:20'],
            'name'                          => ['required','min:2','max:30'],
           // 'birthday'                      => ['required'],
           // 'avatar'                        => ['required'],
            'provinceId'                    => ['min:1'],
            'cityId'                        => ['min:1'],
            'areaId'                        => ['min:1'],
            'idcardSn'                      => ['required','regex:/^[0-9]{18}|[0-9]{15}|[0-9]{17}[xX]{1}$/'],
            'idcardPositiveImg'             => ['required'],
            'idcardNegativeImg'             => ['required'],
           // 'address'                       => ['required'],
            'mapPoint'                      => ['required'],
            'mapPos'                        => ['required'],
        );

        $messages = array(
            'mobile.required'               => '10101',
            'mobile.regex'                  => '10102',
            'code.required'                 => '10103',
            'code.size'                     => '10104',
            'pwd.required'                  => '10105',
            'pwd.min'                       => '10106',
            'pwd.max'                       => '10106',
            'name.required'                 => '10107',
            'name.min'                      => '10108',
            'name.max'                      => '10108',
           // 'birthday.required'             => '10109',
           // 'avatar.required'               => '10110',
            'provinceId.min'                => '10111',
            'cityId.min'                    => '10112',
            'areaId.min'                    => '10113',
            'idcardSn.required'             => '10114',
            'idcardSn.regex'                => '10115',
            'idcardPositiveImg.required'    => '10116',
            'idcardNegativeImg.required'    => '10117',
            //'address.required'              => '30613',   // 请输入地址
            'mapPoint.required'             => '30614',   // 请选择地图定位
            'mapPos.required'               => '30616',    // 请选择服务范围

        );

        $validator = Validator::make([
                'mobile'                => $mobile,
                'code'                  => $verifyCode,
                'pwd'                   => $pwd,
                'name'                  => $name,
                'provinceId'            => $provinceId,
                'cityId'                => $cityId,
                'areaId'                => $areaId,
              //  'birthday'              => $birthday,
              //  'avatar'                => $avatar,
                'idcardSn'              => $idcardSn,
                'idcardPositiveImg'     => $idcardPositiveImg,
                'idcardNegativeImg'     => $idcardNegativeImg,
               // 'address'               => $address,
                'mapPoint'              => $mapPoint,
                'mapPos'                => $mapPos,
            ], $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        //检测验证码
        $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_REG);
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            return $result;
        }
        $mapPoint = Helper::foramtMapPoint($mapPoint);
        if (!$mapPoint){
            $result['code'] = 30615;    // 地图定位错误
            return $result;
        }
        
        $mapPos = Helper::foramtMapPos($mapPos);
        
        if (!$mapPos) {
            $result['code'] = 30617;    // 服务范围错误
            return $result;
        }
       // $birthday = Time::toTime($birthday);
        $seller = self::getByMobile($mobile);
        if ($seller) {
            $result['code'] = 10118;
            return $result;
        }

        DB::beginTransaction();
        try {
            $user = UserService::getByMobile($mobile);
            if (!$user) {
                $user = new User;
                $user->mobile           = $mobile;
                $user->name             = $name;
                $user->name_match       = String::strToUnicode($name . $mobile);
                $user->reg_time         = UTC_TIME;
                $user->reg_ip           = CLIENT_IP;
                $user->province_id      = (int)$provinceId;
                $user->city_id          = (int)$cityId;
                $user->area_id          = (int)$areaId;
                // $user->sex              = (int)$sex;
                // $user->birthday         = $birthday;
                $user->is_sms_verify    = 1;
                $user->save();
            } else {
                //会员存在且在员工表也同时存在
                $staff_check = (int)SellerStaff::where('user_id', $user->id)->pluck('id');
                if ($staff_check > 0) {
                    $result['code'] = 10118;    // 手机号码已被注册
                    return $result;
                }
            }

            if (!empty($avatar)) {
                $user_avatar = self::moveUserImage($user->id, $avatar);
                if (!$user_avatar) {//转移图片失败
                    $result['code'] = 10201;
                    return $result;
                }
            }
            
            $crypt        = String::randString(6);
            $pwd          = md5(md5($pwd) . $crypt);
            $user->crypt  = $crypt;
            $user->pwd    = $pwd;
            $user->avatar = $avatar;
            $user->save();

            $seller = new Seller;
            $seller->type             = $sellerType;
            $seller->user_id          = $user->id;
            $seller->mobile           = $mobile;
            $seller->name             = $name;
            $seller->name_match       = String::strToUnicode($name.$mobile);
            $seller->address          = $address;
            $seller->map_point_str    = $mapPoint;
            $seller->map_pos_str      = $mapPos["str"];
            $seller->map_point        = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
            $seller->map_pos          = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
            $seller->create_time      = UTC_TIME;
            $seller->create_day       = UTC_DAY;
            $seller->province_id      = (int)$provinceId;
            $seller->city_id          = (int)$cityId;
            $seller->area_id          = (int)$areaId;
            $seller->contacts         = $contacts;
            $seller->service_tel      = $serviceTel;

            $seller->save();

            if (!empty($avatar)) {
                $logo = self::moveSellerImage($seller->id, $avatar);
                if (!$logo) {//转移图片失败
                    $result['code'] = 10202;
                    return $result;
                }
            }
            
            $seller->logo = $logo;
            $seller->save();

            //var_dump($seller);
            //创建商家扩展信息表
            $sellerExtend = new SellerExtend();
            $sellerExtend->seller_id = $seller->id;
            $sellerExtend->save();

            if ($sellerType == Seller::PROPERTY_ORGANIZATION) { // 是物业公司关联小区
                District::where('id', $districtId)->update(['seller_id'=>$seller->id]);
                //更新所有在小区关联物业公司之前的业主
                PropertyUser::where('district_id', $districtId)->update(['seller_id'=>$seller->id]);
            }

            $seller_idcard = SellerAuthenticate::where('idcard_sn', $idcardSn)->first();
            if($seller_idcard){
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
           
            if($seller->type == Seller::SERVICE_ORGANIZATION)
            {
                
                if($businessLicenceImg == false)
                {
                    $result['code'] = 10207; // 公司营业执照相片不能为空
                    return $result;
                }
                
                $businessLicenceImg = self::moveSellerImage($seller->id, $businessLicenceImg);
                
                if (!$businessLicenceImg) {//转移图片失败
                    $result['code'] = 10204;
                    return $result;
                }
            }
            if ($seller->type == Seller::SELF_ORGANIZATION) {
                if($certificateImg == false)
                {
                    $result['code'] = 10207; // 资质证书不能为空
                    return $result;
                }
                
                $certificateImg = self::moveSellerImage($seller->id, $certificateImg);
                
                if (!$certificateImg) {//转移图片失败
                    $result['code'] = 10204;
                    return $result;
                }
            }
            
            $auth = new SellerAuthenticate();
            $auth->seller_id            = $seller->id;
            $auth->idcard_sn            = $idcardSn;
           // $auth->real_name            = $name;
            $auth->idcard_positive_img  = $idcardPositiveImg;
            $auth->idcard_negative_img  = $idcardNegativeImg;
            $auth->certificate_img      = $certificateImg;
           // $auth->business_licence_sn  = $businessLicenceSn;
            $auth->business_licence_img = $businessLicenceImg;
            $auth->update_time          = UTC_TIME;
            $auth->save();

            if($sellerType != PROPERTY_ORGANIZATION){  // 不是物业公司类型
                //如果个人加盟版 则保存至员工表
                $staff = new SellerStaff();
                if($sellerType == Seller::SELF_ORGANIZATION) {
                    $staff->type           = 0;
                } else {
                    $staff->type           = 3;
                }
                $staff->user_id            = $user->id;
                $staff->seller_id          = $seller->id;
                $staff->avatar             = $avatar;
                $staff->mobile             = $mobile;
                $staff->name               = $name;
                $staff->name_match         = String::strToUnicode($name.$mobile);
                $staff->address            = $address;
                // $staff->map_point          = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
                // $staff->map_point_str      = $mapPoint;
                $staff->province_id        = $provinceId;
                $staff->city_id            = $cityId;
                $staff->area_id            = (int)$areaId;
                $staff->status             = 1;
                $staff->create_time        = UTC_TIME;
                $staff->create_day         = UTC_DAY;
                $staff->save();
         
                //保存员工扩展信息
                $sellerStaffExtend = new SellerStaffExtend();
                $sellerStaffExtend->staff_id = $staff->id;
                $sellerStaffExtend->seller_id = $seller->id;;
                $sellerStaffExtend->save();

            }
               
            UserVerifyCode::destroy($verifyCodeId);
            if ($cateIds != '') {
                SellerCateRelated::where('seller_id', $seller->id)->delete();
                $cateIds = is_array($cateIds) ? $cateIds : explode(',', $cateIds);
                foreach (array_filter($cateIds) as $key => $value) {
                    $cate = new SellerCateRelated();
                    $cate->seller_id = $seller->id;
                    $cate->cate_id = $value;
                    $cate->save();
                }
            }
            
          
            DB::commit();

            $sellerMap = new SellerMap();
            $sellerMap->seller_id       = $seller->id;
            $sellerMap->map_point       = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
            $sellerMap->map_pos         = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
            $sellerMap->save();

            $result['data'] = $seller;
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 10119;
        }
        return $result;
    }

    /**
     * 服务人员搜索
     * @param  [type] $mobileName 手机或者名称
     * @return [type]             [description]
     */
    public static function searchSeller($mobileName) {
        $list = Seller::select('id', 'name', 'mobile');
       // $match = empty($mobileName) ? '' : String::strToUnicode($mobileName,'+');
        if (!empty($mobileName)) {
            $list->where('mobile', 'like', $mobileName.'%')
                //->selectRaw("IF(name = '{$mobileName}' or mobile = '{$mobileName}',1,0) AS eq,
                //        MATCH(name_match) AGAINST('{$match}') AS similarity")
                // ->whereRaw('MATCH(name_match) AGAINST(\'' . $match . '\' IN BOOLEAN MODE)')
				 ->where('type', 2)
                 ->orderBy('eq', 'desc')
                 ->orderBy('similarity', 'desc');
        }
        return $list->orderBy('id', 'desc')->skip(0)->take(30)->get()->toArray();
    }


    /**
     * 获取服务人员
     * @param  int $id 服务人员id
     * @return array   服务人员
     */
	public static function getSystemSellerById($id) 
    {
		return Seller::where('id', $id)
		    ->with('province', 'city', 'area','authenticate','certificate')
		    ->first();
	}
    /**
     * 删除服务人员
     * @param int  $id 服务人员id
     * @return array   删除结果
     */
	public static function deleteSystem($id) 
    {
		$result = [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		];
        
        $seller = Seller::find($id);
        if (!$goods) {
            $result['code'] = 30630;
        }

        DB::beginTransaction();
        try {
            Seller::where('id', $id)->delete();
            SellerExtend::where('seller_id', $id)->delete();
            SellerAppointHour::where('seller_id', $id)->delete();
            SellerAuthenticate::where('seller_id', $id)->delete();
            SellerBank::where('seller_id', $id)->delete();
            SellerCertificate::where('seller_id', $id)->delete();
            SellerComplain::where('seller_id', $id)->delete();
            SellerMoneyLog::where('seller_id', $id)->delete();
            SellerWithdrawMoney::where('seller_id', $id)->delete();
            ReadMessage::where('seller_id', $id)->delete();
            Promotion::where('seller_id', $id)->delete();
            PromotionSn::where('seller_id', $id)->delete();
            OrderRate::where('seller_id', $id)->delete();
            Goods::where('seller_id', $id)->delete();
            GoodsExtend::where('seller_id', $id)->delete();
            
            self::removeSellerImage($id);
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
	}

    /**
     * 修改服务人员资料
     * @param  int $sellerId 服务人员id
     * @param array $logo LOGO
     * @param array $photos 人个相册
     * @param string $address 地址
     * @param string $mapPoint 纬度,经度(QQ地图坐标)
     * @param array $mapPos QQ地图坐标数组
     * @param int $provinceId 所在省编号
     * @param int $cityId 所在市编号
     * @param int $areaId 所在县编号
     * @param string $brief 简介
     * @param int $status 状态
     * @return [type]  [description]
     */
    public static function updateSeller($sellerId, $logo, $image, $name, $address, $mapPoint, $mapPos, $provinceId, $cityId, $areaId, $contacts, $cateIds, $status, $deliveryFee, $serviceFee, $deliveryTime, $deduct, $isCashOnDelivery, $serviceTel) 
    {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'provinceId'    => ['min:1'],
            'cityId'        => ['min:1'],
            'areaId'        => ['min:1'],
            'address'       => ['required'],
            'mapPoint'      => ['required'],
            'mapPos'        => ['required'],
            'logo'          => ['required'],
            'name'          => ['required','min:2','max:30'],
        );

        $messages = array(
            'provinceId.min'    => 10111,   // 请选择所在省
            'cityId.min'        => 10112,   // 请选择所在市
            'areaId.min'        => 10113,   // 请选择所在县
            'address.required'  => 30613,   // 请输入地址
            'mapPoint.required' => 30614,   // 请选择地图定位
            'mapPos.required'   => 30616,    // 请选择服务范围
            'logo.required'     => 30605,   //请上传LOGO图片
            'name.required'     => 10107,
            'name.min'          => 10108,
            'name.max'          => 10108,
        );

        $validator = Validator::make([
                'provinceId'    => $provinceId,
                'cityId'        => $cityId,
                'areaId'        => $areaId,
                'address'       => $address,
                'mapPoint'      => $mapPoint,
                'mapPos'        => $mapPos,
                'logo'          => $logo,
                'name'          => $name
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        $mapPoint = Helper::foramtMapPoint($mapPoint);
        if (!$mapPoint){
            $result['code'] = 30615;    // 地图定位错误
            return $result;
        }
        
        $mapPos = Helper::foramtMapPos($mapPos);
        
        if (!$mapPos) {
            $result['code'] = 30617;    // 服务范围错误
            return $result;
        }

        $seller = Seller::find($sellerId);
        if (!$seller) {//服务人员不存在
            $result['code'] = 30629;
            return $result;
        }

        $logo = self::moveSellerImage($seller->id, $logo);
        if (!$logo) {//转移图片失败
            $result['code'] = 30606;
            return $result;
        }
        if (!empty($image)) {
            $image = self::moveSellerImage($seller->id, $image);
            if (!$image) {//转移图片失败
                $result['code'] = 30606;
                return $result;
            }
        }
        
        $seller->logo               = $logo;
        $seller->image              = $image;
        $seller->name               = $name;
        $seller->name_match         = String::strToUnicode($name.$seller->mobile);
        $seller->address            = $address;
        $seller->map_point_str      = $mapPoint;
        $seller->map_pos_str        = $mapPos["str"];
        $seller->map_point          = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
        $seller->map_pos            = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
        $seller->province_id        = $provinceId;
        $seller->city_id            = $cityId;
        $seller->area_id            = $areaId;
        $seller->contacts           = $contacts;
        $seller->status             = $status;
        $seller->delivery_fee       = $deliveryFee;
        $seller->service_fee        = $serviceFee;
        $seller->service_tel        = $serviceTel;
        //$seller->deduct             = $deduct;
        $seller->is_cash_on_delivery= $isCashOnDelivery;
        
        if ($seller->save()) {
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
                $delivery->seller_id     = $seller->id;
                $delivery->stime         = $value;
                $delivery->etime         = $deliveryTime['etimes'][$key];
                $delivery->save(); 
            }
            SellerMap::where('seller_id',$seller->id)->update([
                'map_pos'=>DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')"),
                'map_point'=>DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')")
                ]);
            $result['code'] = 0;
            return $result;
        } else {
            $result['code'] = 99999;
            return $result;
        }

        return $result;
    }   

    /**
     * 资质设置
     * @param int  $sellerId 服务人员id
     * @param array $certs 资质图片
     * @param int $status 状态
     * @return array   删除结果
     */
    public static function saveCert($sellerId, $authenticate) 
    {
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];
        // var_dump($authenticate);
        // exit;
        $seller = Seller::find($sellerId);
        if (!$seller) {//服务人员不存在
            $result['code'] = 30629;
            return $result;
        }
        
        $cert = SellerAuthenticate::where("seller_id", $sellerId)->first();
        if (!$cert) {
            $cert = new SellerAuthenticate();
        }
        $idcardNegativeImg = $authenticate['idcardNegativeImg'];
        $idcardPositiveImg = $authenticate['idcardPositiveImg'];
        $idcardSn = $authenticate['idcardSn'];
        $businessLicenceImg = isset($authenticate['businessLicenceImg']) ? $authenticate['businessLicenceImg'] : '';
        $certificateImg = isset($authenticate['certificateImg']) ? $authenticate['certificateImg'] : '';

        $seller_idcard = SellerAuthenticate::where('idcard_sn', $idcardSn)->where("seller_id", '!=',$sellerId)->first();
            if($seller_idcard){
                $result['code'] = 30621;    //身份证号码已存在
                DB::rollback();
                return $result;
            }

            $idcardPositiveImg = self::moveSellerImage($seller->id, $authenticate['idcardPositiveImg']);
            if (!$idcardPositiveImg) {//转移图片失败
                $result['code'] = 10203;
                return $result;
            }

            $idcardNegativeImg = self::moveSellerImage($seller->id, $idcardNegativeImg);
            if (!$idcardNegativeImg) {//转移图片失败
                $result['code'] = 10204;
                return $result;
            }
           
            if($seller->type == Seller::SERVICE_ORGANIZATION)
            {
                
                if($businessLicenceImg == false)
                {
                    $result['code'] = 10207; // 公司营业执照相片不能为空
                    return $result;
                }
                
                $businessLicenceImg = self::moveSellerImage($seller->id, $businessLicenceImg);
                
                if (!$businessLicenceImg) {//转移图片失败
                    $result['code'] = 10204;
                    return $result;
                }
            }
            if ($seller->type == Seller::SELF_ORGANIZATION) {

                if($certificateImg == false)
                {
                    $result['code'] = 10207; // 资质证书不能为空
                    return $result;
                }
                
                $certificateImg = self::moveSellerImage($seller->id, $certificateImg);
                
                if (!$certificateImg) {//转移图片失败
                    $result['code'] = 10204;
                    return $result;
                }
            }

        $cert->seller_id            = $seller->id;
        $cert->idcard_positive_img    = $idcardPositiveImg;
        $cert->idcard_negative_img    = $idcardNegativeImg;
        $cert->business_licence_img   = $businessLicenceImg;
        $cert->certificate_img       = $certificateImg;
        $cert->idcard_sn             = $idcardSn;
        $cert->update_time          = UTC_TIME;
        $cert->status =1;
       // var_dump($cert);
        $cert->save();
   
        return $result;
    }

    /**
     * [getList 获取卖家某一天日程列表]
     * @param  [int] $sellerId [卖家编号]
     * @return [array] $list          [description]
     */
    /*
    public static function getDayList($sellerId, $date) 
    {
        if ($sellerId < 1) 
        {
            return [];
        }
        
        $hours = [];
        
        $beginTime = Time::toTime($date);
        
        $endTime = $beginTime + 24 * 60 * 60 - 1;
                
        $appoint = SellerAppoint::where('seller_id', $sellerId)
            ->whereBetween('appoint_time', [$beginTime, $endTime])
            ->select('appoint_time', 'status')
            ->get();
        
        foreach($appoint as $value)
        {
            $hour = Time::toDate($value->appoint_time, 'H:i');
            
            $iHour = (int)Time::toDate($value->appoint_time, 'H');
            
            if($iHour * 3600 >= SellerAppoint::DEFAULT_BEGIN_ORDER_DATE &&
                $iHour * 3600 <= SellerAppoint::DEFAULT_END_ORDER_DATE)
            {
                $hours[$hour] = 
                [
                    'hour'      => $hour,
                    'status'    => $value->status
                ];
            }
        }
        
        //当表中无预约时间数据,返回默认数据
        for (; $beginTime <= $endTime; $beginTime += SellerAppoint::SERVICE_SPAN)
        {
            $iHour = (int)Time::toDate($beginTime, 'H');
            
            if($iHour * 3600 >= SellerAppoint::DEFAULT_BEGIN_ORDER_DATE &&
                $iHour * 3600 <= SellerAppoint::DEFAULT_END_ORDER_DATE)
            {
                $hour = Time::toDate($beginTime, 'H:i');
                
                if(array_key_exists($hour, $hours) == false)
                {
                    $hours[$hour] = 
                    [
                        'hour'      => $hour,
                        'status'    => SellerAppoint::ACCEPT_APPOINT_STATUS
                    ];
                }
            }
        }

        ksort($hours);
        
        return ['day' => Time::toTime($date), 'hours' => array_values($hours)];
    }
    */

    /**
     * 其他设置
     * @param  int $sellerId 服务人员id
     * @param  int $businessStatus 接单状态
     * @param  sting $businessDesc 接单说明
     * @param  int $sort 排序
     * @param  int $status 状态
     * @return 
     */
    public static function extendSet($sellerId, $brief, $sort, $status) 
    {
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];
        $seller = Seller::find($sellerId);
        if (!$seller) {//服务人员不存在
            $result['code'] = 30629;
            return $result;
        }
        
        $seller->brief        = $brief;
        $seller->sort         = $sort;
        $seller->status       = $status;

        if ($seller->save()) {
            $result['code'] = 0;
        } else {
            $result['code'] = 99999;
        }


        return $result;
    }

    /**
     * 检测手机号验证码
     * @param  sting $mobile 手机号
     * @param  sting $verifyCode 验证码
     * @return 
     */
    public static function checkTelcode($mobile, $verifyCode) {
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];
        $rules = array(
            'mobile'           => ['required','regex:/^1[0-9]{10}$/'],
            'code'             => ['required','size:6'],
        );

        $messages = array(
            'mobile.required'               => '10101',
            'mobile.regex'                  => '10102',
            'code.required'                 => '10103',
            'code.size'                     => '10104',
        );

        $validator = Validator::make([
                'mobile'     => $mobile,
                'code'       => $verifyCode,
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        //检测验证码
        $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_REG);
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            return $result;
        }

        $mobilecheck = self::getByMobile($mobile);
        if ($mobilecheck) { //手机号已存在
            $result['code'] = 10118;
            return $result;
        }

        return $result;
    }

    /**
     * 更改手机号
     * @param  int $sellerId 服务人员id
     * @param  int $mobile 原手机号
     * @param  string $pwd 密码
     * @param  int $newMobile 新手机号
     * @param  int $verifyCode 验证码
     * @return 
     */
    public static function updateMobile($sellerId, $oldMobile, $pwd, $mobile, $verifyCode) 
    {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'mobile'           => ['required','regex:/^1[0-9]{10}$/'],
            'code'             => ['required','size:6'],
            'pwd'              => ['required','min:6','max:20'],
            'newMobile'        => ['required','regex:/^1[0-9]{10}$/']
        );

        $messages = array(
            'mobile.required'               => '10101',
            'mobile.regex'                  => '10102',
            'code.required'                 => '10103',
            'code.size'                     => '10104',
            'pwd.required'                  => '10105',
            'pwd.min'                       => '10106',
            'pwd.max'                       => '10106',
            'newMobile.required'            => '10101',
            'newMobile.regex'               => '10102',
        );

        $validator = Validator::make([
                'mobile'     => $oldMobile,
                'code'       => $verifyCode,
                'pwd'        => $pwd,
                'newMobile'  => $mobile,
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        //检测验证码
        $verifyCodeId = UserService::checkVerifyCode($verifyCode, $oldMobile, UserVerifyCode::TYPE_REG);
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            return $result;
        }
        $seller = Seller::find($sellerId);
        if (!$seller) {//服务人员不存在
            $result['code'] = 30629;
            return $result;
        }
        $user = User::where('id',$seller->user_id)->first();
        $pwd = md5(md5($pwd) . $user->crypt);

        if ($user->pwd !== $pwd ) { //原密码不对
            $result['code'] = 10106;
            return $result;
        }

        $mobilecheck = self::getByMobile($mobile);
        if ($mobilecheck) { //新手机号已存在
            $result['code'] = 10118;
            return $result;
        }
        //不属于此商家的员工手机已存在
        $staff_check = (int)SellerStaff::where('mobile', $mobile)->where('seller_id', '!=', $sellerId)->pluck('id');
        if ($staff_check > 0) {
            $result['code'] = 10118;    // 手机号码已被注册
            return $result;
        }

        $seller->mobile  = $mobile;
        $user = User::where('id', $seller->user_id)->update(['mobile' => $mobile]);
        if ($user && $seller->save()) {
            if (SellerStaff::where('seller_id', $sellerId)->where('user_id', $seller->user_id)->first() !== false) {
                SellerStaff::where('seller_id', $sellerId)->where('user_id', $seller->user_id)->update(['mobile',$mobile]);
            }
            $result['code'] = 0;
            return $result;
        }
        return $result;
    }

    /**
     * 找回密码/修改密码
     * @param  int $sellerId 服务人员id
     * @param  int $mobile 原手机号
     * @param  string $idcardSn 证件号
     * @param  sting $pwd 新密码
     * @param  int $newpwd 确认密码
     * @param  int $verifyCode 验证码
     * @param  type $type 类型back/change
     * @return 
     */
    public static function updatePass($sellerId, $mobile, $idcardSn, $pwd, $newpwd, $verifyCode, $type) {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'mobile'           => ['required','regex:/^1[0-9]{10}$/'],
            'code'             => ['required','size:6'],
            'idcardSn'         => ['required','regex:/^[0-9]{18}|[0-9]{15}|[0-9]{17}[xX]{1}$/'],
            'pwd'              => ['required','min:6','max:20'],
            'newpwd'           => ['required','min:6','max:20'],
        );

        $messages = array(
            'mobile.required'               => '10101',
            'mobile.regex'                  => '10102',
            'code.required'                 => '10103',
            'code.size'                     => '10104',
            'idcardSn.required'             => '10114',
            'idcardSn.regex'                => '10115',
            'pwd.required'                  => '10105',
            'pwd.min'                       => '10106',
            'pwd.max'                       => '10106',
            'newpwd.required'               => '10105',
            'newpwd.min'                    => '10106',
            'newpwd.max'                    => '10106',
        );

        $validator = Validator::make([
                'mobile'     => $mobile,
                'code'       => $verifyCode,
                'pwd'        => $pwd,
                'idcardSn'   => $idcardSn,
                'newpwd'     => $newpwd,
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        //检测验证码
        $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_REG);
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            return $result;
        }

        if (trim($pwd) !== trim($newpwd)) { //两次密码不一致
            $result['code'] = 10121;
            return $result;
        }
        if ($type == 'change') {
            $seller = Seller::find($sellerId);
        } else {
            $seller = self::getByMobile($mobile);
        }
        if (!$seller) {//服务人员不存在
            $result['code'] = 30629;
            return $result;
        }
        $auth = SellerAuthenticate::where('seller_id', $seller->id)->first();
        if ($auth->idcard_sn !== $idcardSn) {//证件号不对
            $result['code'] = 10122;
            return $result;
        }
        $crypt  = String::randString(6);
        $pwd    = md5(md5($pwd) . $crypt);
        $users = User::where('id',$seller->user_id)->update(['pwd' => $pwd, 'crypt'=> $crypt]);

        if ($users) {
            $result['code'] = 0;
            return $result;
        } else {
            $result['code'] = 99999;
            return $result;
        } 
        return $result;

    }

    /**
     * 员工搜索
     * @param  [type] $mobileName 手机或者名称
     * @return [type]             [description]
     */
    public static function searchStaff($sellerId,$mobileName){
        $list = SellerStaff::select('id', 'name', 'mobile')->where('seller_id',$sellerId)->where("status", 1)->whereIn('type', [0, 2, 3]);
       // $match = empty($mobileName) ? '' : String::strToUnicode($mobileName,'+');
        if (!empty($mobileName)) {
            //->selectRaw("IF(name = '{$mobileName}' or mobile = '{$mobileName}',1,0) AS eq,
                  //      MATCH(name_match) AGAINST('{$match}') AS similarity")
            $list->where('mobile', 'like', $mobileName.'%')
                // ->whereRaw('MATCH(name_match) AGAINST(\'' . $match . '\' IN BOOLEAN MODE)')
                 ->orderBy('eq', 'desc')
                 ->orderBy('similarity', 'desc');
        } 
        return $list->orderBy('id', 'desc')->skip(0)->take(30)->get()->toArray();
    }

    /*
    * 物业公司修改
    */
    public static function updateBasic($sellerId, $businessLicenceImg) {
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];

        $seller = Seller::find($sellerId);
        if (!$seller) { 
            $result['code'] = 30629;
            return $result;
        }

        if (!empty($businessLicenceImg)) {
            $businessLicenceImg = self::moveSellerImage($seller->id, $businessLicenceImg);
            if (!$businessLicenceImg) {//转移图片失败
                $result['code'] = 10204;
                return $result;
            }
            SellerAuthenticate::where('seller_id', $sellerId)->update(['business_licence_img'=>$businessLicenceImg]);
        }
        
        return $result;
    }
}
