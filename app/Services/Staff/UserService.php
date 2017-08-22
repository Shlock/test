<?php namespace YiZan\Services\Staff;

use YiZan\Models\User;
use YiZan\Models\UserVerifyCode;
use YiZan\Models\Seller;
use YiZan\Models\SellerStaff;
use YiZan\Utils\String;
use YiZan\Utils\Image;
use YiZan\Services\RegionService;
use YiZan\Services\PromotionService;
use Request, DB, Lang, Validator;

class UserService extends \YiZan\Services\UserService {


	public static function createUser($mobile, $verifyCode, $pwd, $type = 'reg') {
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
        if($type == 'repwd' || $type == 'repass' ) {
            if (!$user) {
                    $result['code'] = 10116; //未注册的账号不能修改密码
                    return $result;
            } else {
                $staff = SellerStaff::where('user_id', $user->id)->first();
                $seller = Seller::where('user_id', $user->id)->first();
                if (!$staff && !$seller) {
                   $result['code'] = 10124;
                    return $result;
                }
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
	    }

	    $user->crypt 			= $crypt;
	    $user->pwd 				= $pwd;
	    
	    if ($user->save()) {
	    	if ($is_new_user) {
	    		PromotionService::issueUserRegPromotion($user->id);
	    	}
	    	UserVerifyCode::destroy($verifyCodeId);
	    	$result['data'] = $user;
	    } else {
	    	$result['code'] = 10107;
	    }
	    return $result;
	}


}
