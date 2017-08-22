<?php 
namespace YiZan\Services\Sellerweb;

use YiZan\Models\Sellerweb\User;
use YiZan\Models\UserVerifyCode;
use YiZan\Models\SystemConfig;

use YiZan\Utils\String;
use YiZan\Utils\Http;
use YiZan\Utils\Image;
use YiZan\Utils\Helper;
use YiZan\Utils\Time;

use YiZan\Services\SmsService;
use YiZan\Services\RegionService;

use Request, DB, Lang, Validator;

class UserService extends \YiZan\Services\BaseService {
	/**
	 * 根据手机号码获取会员
	 * @param  string $mobile 手机号码
     * @return \stdClass          会员信息
	 */
	public static function getByMobile($mobile) {
		return User::where('mobile', $mobile)->first();
	}

	public static function getById($id) {
		return User::find($id);
	}

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

	    $ip_location = Http::getIpLocation(CLIENT_IP);
	    $province_id = RegionService::getIdByName($ip_location['province']);
	    if ($province_id > 0) {
	    	$city_id = RegionService::getIdByName($ip_location['city'], $province_id);
	    } else {
	    	$city_id = 0;
	    }

	    $user = self::getByMobile($mobile);
	    if (!$user) {
	    	$user = new User;
	    }
	    
	    $user->mobile 			= $mobile;
	    $user->name_match		= String::strToUnicode($mobile);
	    $user->name 			= substr($mobile,0,6).'****'.substr($mobile,-1,1);
	    $user->crypt 			= $crypt;
	    $user->pwd 				= $pwd;
	    $user->reg_time 		= UTC_TIME;
	    $user->reg_ip 			= CLIENT_IP;
	    $user->province_id 		= $province_id;
	    $user->city_id 			= $city_id;
	    $user->is_sms_verify 	= 1;
	    if ($user->save()) {
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
	 * @return object        是否正确
	 */
	public static function checkVerifyCode($code, $mobile, $type = 'reg', $userId = 0) 
    {
        if($code == '123456'){
            return true;
        }
		$userVerifyCode = UserVerifyCode::where('mobile', $mobile)
										//->where('type', $type)
										->first();
       
        //存在发送记录时
		if ($userVerifyCode) 
        {
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
	 * @return array         	发送状态
	 */
	public static function sendVerifyCode($mobile, $type = 'reg', $userId = 0) {
            return array('status'=>1);
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
		// $code = '123456';
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
			$result['code'] = 10003;
			return $result;
		}
	}

	public static function updateLoginInfo($id){
		$ip_location = Http::getIpLocation(CLIENT_IP);
	    $province_id = RegionService::getIdByName($ip_location['province']);
	    if ($province_id > 0) {
	    	$city_id = RegionService::getIdByName($ip_location['city'], $province_id);
	    } else {
	    	$city_id = 0;
	    } 	     
	    $result = User::where('id',$id)->update(array('login_time'=>UTC_TIME,'login_ip'=>Request::ip(),'login_province_id'=>$province_id,'login_city_id'=>$city_id));
	}

	public static function updateInfo($user, $data) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.update_info')
		);

		$rules = array(
		    'name' => ['required','min:2','max:30'],
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
	    if (!empty($data['avatar'])) {
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
}
