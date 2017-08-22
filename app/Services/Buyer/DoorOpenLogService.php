<?php namespace YiZan\Services\Buyer;

use YiZan\Models\DoorOpenLog; 
use YiZan\Models\Seller; 
use Exception, DB, Lang, Validator, App;

class DoorOpenLogService extends \YiZan\Services\DoorOpenLogService{ 
	
	/**
	 * 记录开门
	 */
	public static function doorOpenRecord($puserId, $errorCode, $districtId, $doorId, $buildId, $roomId){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg' => '成功',
        );
        $sellerId = Seller::where('district_id', $districtId)->pluck('id');
		$record = new DoorOpenLog();
		$record->puser_id 		= $puserId;
		$record->error_code 	= $errorCode;
		$record->seller_id 		= $sellerId;
		$record->district_id 	= $districtId;
		$record->door_id 		= $doorId;
		$record->build_id 		= $buildId;
		$record->room_id 		= $roomId;
		$record->create_time 	= UTC_TIME;
		$record->create_day 	= UTC_DAY;
		$record->save();
		return $result;
	}

}