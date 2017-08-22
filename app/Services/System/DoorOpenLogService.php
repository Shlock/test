<?php namespace YiZan\Services\System;

use YiZan\Models\DoorOpenLog; 
use Exception, DB, Lang, Validator, App;

class DoorOpenLogService extends \YiZan\Services\DoorOpenLogService{ 
	
	/**
	 * 小区开门列表
	 */
	public static function getLists($sellerId, $districtId, $doorName, $userName, $beginTime, $endTime, $isTotal, $page, $pageSize){
		$list = DoorOpenLog::where('seller_id', $sellerId)
						   ->where('district_id', $districtId)
						   ->orderBy('id', 'DESC');

		if($doorName == true){
			$list->join('DoorAccess', function($join) use($doorName){
				$join->on('door_id', '=', 'door_access.id')
					 ->where('door_access.name', 'like', $doorName);
			});
		}

		if($userName == true){
			$list->join('PropertyUser', function($join) use($userName){
				$join->on('puser_id', '=', 'property_user.id')
					 ->where('property_user.name', 'like', $userName);
			});
		}

		if($beginTime == true){
			$list->where('create_time', '>', $beginTime);
		} 

		if($endTime == true){
			$list->where('create_time', '>', $endTime);
		} 

		if($isTotal == true){ 
			$list = $list->with('door', 'puser', 'build', 'room')
						 ->get()
						 ->toArray();
			return $list;
		} else {
			$totalCount = $list->count();

			$list = $list->skip(($page - 1) * $pageSize)
						 ->take($pageSize)
						 ->with('door', 'puser', 'build', 'room')
						 ->get()
						 ->toArray();
			return ["list" => $list, "totalCount" => $totalCount];
		} 
		
	}

}