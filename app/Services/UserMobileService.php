<?php namespace YiZan\Services;

use YiZan\Models\UserMobile;

use YiZan\Utils\String;
use YiZan\Utils\Http;
use YiZan\Utils\Image;
use YiZan\Utils\Helper;
use YiZan\Utils\Time;

use YiZan\Services\SmsService;
use YiZan\Services\RegionService;

use DB, Lang, Validator;

class UserMobileService extends BaseService {
	
	public static function getMobile($userId) {
		$address = UserMobile::where('user_id', $userId)
							->orderBy('is_default','desc')
							->orderBy('id','asc')
							->first();
		if ($address) {
			return $address->toArray();
		} else {
			return null;
		}
	}

	/**
	 * 根据编号获取会员常用电话
	 * @param  integer $userId 会员编号
	 * @return array           地址数组
	 */
	public static function getMobileList($userId) {
		return UserMobile::where('user_id', $userId)
			->orderBy('is_default','desc')
			->orderBy('id','asc')
			->get()->toArray();
	}

	/**
	 * 创建会员常用电话
	 * @param  integer $userId   会员编号
	 * @param  string  $mobile   电话
	 * @return array             处理结果
	 */
	public static function createMobile($userId, $mobile) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.create_user_mobile')
		);

		$rules = array(
		    'mobile' => ['required','regex:/^1[0-9]{10}$/']
		);

		$messages = array(
		    'mobile.required'	=> '10601',
		    'mobile.regex' 	=> '10602'
		);

		$validator = Validator::make([
				'mobile' 	=> $mobile
			], $rules, $messages);
		if ($validator->fails()) {//验证信息
	    	$messages = $validator->messages();
	    	$result['code'] = $messages->first();
	    	return $result;
	    }

	    $count = UserMobile::where('user_id', $userId)->count();
	    if ($count > 4) {//不能超过5个
	    	$result['code'] = 10603;
	    	return $result;
	    }

	    $userMobile = new UserMobile;
	    $userMobile->user_id = $userId;
	    $userMobile->mobile = $mobile;
	    $userMobile->is_default = $count == 0 ? 1 : 0;

	    if ($userMobile->save()) {
	    	$result['data'] = $userMobile->toArray();
	    } else {
	    	$result['code'] = 10604;
	    }
	    return $result;
	}

	/**
	 * 常用地址设为默认
	 * @param integer $userId    会员编号
	 * @param integer $mobileId  电话编号
	 * @return array             处理结果
	 */
	public static function setDefaultMobile($userId, $mobileId) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.set_user_default_mobile')
		);

	    $bln = false;
	    DB::beginTransaction();
	    $bln = UserMobile::where('id', $mobileId)
	    					->where('user_id', $userId)
	    					->update(array('is_default' => 1));

	    if ($bln) {
	    	$status = UserMobile::where('user_id', $userId)
	    						->where('id', '<>', $mobileId)
	    						->update(array('is_default' => 0));
	    	if ($status === false) {
	    		$bln = false;
	    	}
	    }

	    if ($bln) {
			DB::commit();
			return $result;
		} else {
			DB::rollback();
			$result['code'] = 10605;
			return $result;
		}
	}

	/**
	 * 常用地址删除
	 * @param integer $userId    会员编号
	 * @param integer $mobileId  电话编号
	 * @return array             处理结果
	 */
	public static function deleteMobile($userId, $mobileId) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.delete_default_user_address')
		);

	    $bln = UserMobile::where('id', $mobileId)
	    					->where('user_id', $userId)
	    					->delete();
	    if ($bln) {
			return $result;
		} else {
			$result['code'] = 10606;
			return $result;
		}
	}

	 /**
     * 获取电话详情
     * @param int $userId 会员编号
     * @param int $mobileId 电话编号
     */
    public static function getById($userId, $mobileId){
        return UserMobile::where('user_id', $userId)
                            ->where('id', $mobileId)
                            ->first()->toArray();
    }
}
