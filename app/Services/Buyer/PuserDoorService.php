<?php namespace YiZan\Services\Buyer;

use YiZan\Utils\Time;
use YiZan\Models\PuserDoor; 
use DB, Exception;

class PuserDoorService extends \YiZan\Services\PuserDoorService {  

	/**
	 * 获取开门钥匙信息
	 */
	public static function getUserDoors($puserId){
		$list = PuserDoor::where('puser_id', $puserId)
						 ->with('door', 'puser')
						 ->get()
						 ->toArray(); 
		return self::parseData($list);
	}

	private static function parseData($data){
		$result = [];
		foreach ($data as $key => $val) { 
			$arr = [];
			$arr['doorid'] = $val['doorId']; 
			$arr['doorname'] = $val['door']['name'];
			$arr['remark'] = $val['remark'];
			$arr['expiretime'] = Time::toDate($val['endTime'], 'Y-m-d');
			$arr['userid'] = $val['puser']['mobile'];
			$arr['keyid'] = $val['lockId'];
			$arr['community'] = $val['community'];
			$arr['appkey'] = $val['appKey'];
			$arr['keyname'] = $val['door']['pid'];
			$result[] = $arr;
		}
		return $result;
	}

	/**
	 * 自定义小区门名称
	 */
	public static function updateUserDoor($puserId, $doorId, $doorname){ 
		// DB::connection()->enableQueryLog();
		$door = PuserDoor::where('puser_id', $puserId)
						 ->where('door_id', $doorId)
						 ->with('door')
						 ->first();
		if($door){
			$door->remark = $doorname;
			$door->save();
		}
		return $door;
	}

}
