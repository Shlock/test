<?php namespace YiZan\Services;

use YiZan\Models\User;
use YiZan\Models\UserVerifyCode;
use YiZan\Models\Restaurant;
use YiZan\Services\UserService;
use YiZan\Models\SystemConfig;
use YiZan\Services\SmsService;
use YiZan\Utils\String;
use Validator,Lang, DB;

class RestaurantService extends BaseService {
	
    /**
     * 根据会员ID 获取餐厅
     * @param  string $mobile 手机号码
     * @return \stdClass          会员信息
     */
    public static function getById($restaurantId) {
        return Restaurant::find($restaurantId);
    }
    /**
     * 根据会员ID 获取餐厅
     * @param  string $mobile 手机号码
     * @return \stdClass          会员信息
     */
    public static function getByUserId($userId) {
        return Restaurant::where('user_id', $userId)->first();
    }
     /**
     * 修改餐厅密码
     * @param  string $mobile 手机号码
     * @return \stdClass          会员信息
     */
    public static function save($userId,$mobile,$verifyCode,$pwdold,$pwd) { 
    	$result = array(
			'code'	=> create_user_repwd,
			'data'	=> null,
			'msg'	=> null
		);

		$rules = array(
		    'mobile' => ['required','regex:/^1[0-9]{10}$/'],
		    'verifyCode' => ['required','size:6'],
		    'pwd' 	 => ['required','min:6','max:20']
		);

		$messages = array(
		    'mobile.required'	  	=> '10101',
		    'mobile.regex'		  	=> '10102',
		    'verifyCode.required' 	=> '10115',
		    'verifyCode.size' 		=> '10109',
		    'pwd.required' 	    	=> '10103',
		    'pwd.min' 				=> '10104',
		    'pwd.max' 				=> '10105',
		);
		$validator = Validator::make([
				'mobile'     => $mobile,
				'verifyCode' => $verifyCode,
				'pwd' 	     => $pwd
			], $rules, $messages);

		if ($validator->fails()) {//验证信息
	    	$messages = $validator->messages();
	    	$result['code'] = $messages->first();
	    	return $result;
	    }
	    if($pwdold != $pwd){
	        $result['code'] = 10110;
	        return $result;
	    }
    	$user = UserService::getById($userId);
    	//会员不存在
    	if(!$user){
			$result['code'] = 10112;
	    	return $result;
    	}
    	if($user->mobile != $mobile){
    	    $result['code'] = 10114;
    	    return $result;
    	}
    	//检测验证码
	    $verifyCodeId = self::checkVerifyCode($verifyCode, $mobile, $type = 'reg', $user->id);
	    if (!$verifyCodeId) {
	    	$result['code'] = 10113;
	    	return $result;
	    }

	    $pwd = md5(md5($pwd) . $user->crypt);

	    if (false === User::where('id', $user->id)->update(
	     		['pwd'  => $pwd ]
	     	)
	     	) {
	    	$result['code'] = create_user_repwd_no;
	    }
	    $result['data'] = $user->find($user->id)->toArray();
	    return $result;
    }

    /**
	 * 检测验证码是否正确
	 * @param  string $code   验证码
	 * @param  string $mobile 手机号
	 * @param  string $type   验证类型
	 * @param  int    $userId 会员编号
	 * @return boolean        是否正确
	 */
	public static function checkVerifyCode($code, $mobile, $type = 'reg', $userId = 0) 
    {
    	// if ($code == "123456") {
    	// 	return true;
    	// }
		$userVerifyCode = UserVerifyCode::where('mobile', $mobile)
										//->where('type', $type)
										->first();
		//存在发送记录时
		if ($userVerifyCode) {
			if ($userVerifyCode->code != $code) {
				return false;
			}

			if ($userVerifyCode->user_id != $userId) {
				return false;
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
            return array('status' => 1);
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
}
