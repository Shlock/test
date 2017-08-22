<?php namespace YiZan\Services\Buyer;

use YiZan\Utils\Time;
use YiZan\Models\PropertyUser; 
use YiZan\Models\Seller; 
use YiZan\Models\District; 
use DB, Exception, Validator;

class PropertyUserService extends \YiZan\Services\PropertyUserService {  

	/**
	 * 小区身份认证
	 */
	public static function auth($userId, $districtId, $buildingId, $roomId, $userName, $userTel){ 
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg' => "申请认证成功"
        );

        $rules = array(
            'districtId'        => ['required'],
            'buildingId'        => ['required'],
            'roomId'            => ['required'],
            'userName'          => ['required'],  
            'userTel'           => ['required','regex:/^1[0-9]{10}$/'], 
        );

        $messages = array(
            'districtId.required'           => 10608,   //
            'buildingId.requiredrequired'   => 10609,   // 
            'roomId.required'               => 10610,   // 
            'userName.required'             => 10611,   //   
            'userTel.required'              => 10612,   //  
            'userTel.regex'                 => 10613,   //  
        );

        $validator = Validator::make([
                'districtId'    => $districtId,
                'buildingId'    => $buildingId,
                'roomId'        => $roomId,  
                'userName'      => $userName, 
                'userTel'       => $userTel, 
            ], $rules, $messages); 

        //验证信息
        if ($validator->fails()) {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        } 

        $info = PropertyUser::where('user_id', $userId)
                            ->where('district_id', $districtId) 
                            ->first();

        if($info && $info->status == 1){
            $result['code'] = 10607;
            return $result;
        }
        if (!$info) {
            $result['code'] = 10616;
            return $result;
        }
        $district = District::find($districtId);
        if (!$district->seller_id) {
            $result['code'] = 77006;
            return $result;
        }

        $info->seller_id        = $district->seller_id;
        $info->build_id         = $buildingId;
        $info->room_id          = $roomId;
        $info->name             = $userName;
        $info->mobile           = $userTel;
        $info->status           = 0;
        $info->access_status    = 0; 
        $info->save();

        return $result;
    } 

	/**
	 * 获取会员小区认证信息
	 */
	public static function getByUserId($userId, $districtId = 0){
        $data = PropertyUser::where('user_id', $userId);
        if($districtId > 0){
            $data->where('district_id', $districtId);
        }
        $data = $data->with('district', 'build', 'room')
                     ->first();
		return $data ? $data->toArray() : null;
	}

	/**
	 * 小区门禁申请
	 */
	public static function applyDoorAccess($userId, $districtId){
		$result = array(
            'code'  => 0,
            'data'  => null,
            'msg' => "申请认证门禁成功"
        );

		$data = PropertyUser::where('user_id', $userId);

        if($districtId == true){
            $data->where('district_id', $districtId);
        }

        $data = $data->with('district', 'build', 'room')
                     ->first();

		if(!$data){
			$result['code'] = 10614;
			return $result;
		}
		$data->access_status = 1;
		$data->save();
		$result['data'] = $data;
		return $result;
	}

}
