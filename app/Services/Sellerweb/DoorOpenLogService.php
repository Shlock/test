<?php namespace YiZan\Services\Sellerweb;

use YiZan\Models\DoorOpenLog; 
use Exception, DB, Lang, Validator, App;

class DoorOpenLogService extends \YiZan\Services\DoorOpenLogService{ 
	
	/**
	 * 小区开门列表
	 */
	public static function getLists($sellerId, $doorName, $userName, $beginTime, $endTime, $build, $roomNum, $isTotal, $page, $pageSize){
		$list = DoorOpenLog::where('door_open_log.seller_id', $sellerId) 
						   ->orderBy('door_open_log.id', 'DESC');

		if($doorName == true){
			$list->join('DoorAccess', function($join) use($doorName){
				$join->on('door_open_log.door_id', '=', 'door_access.id')
					 ->where('door_access.name', 'like', $doorName);
			});
		}

		if($userName == true){
			$list->join('PropertyUser', function($join) use($userName){
				$join->on('door_open_log.puser_id', '=', 'property_user.id')
					 ->where('property_user.name', 'like', '%'.$userName.'%');
			});
		}

		if($beginTime == true){
			$list->where('door_open_log.create_time', '>', $beginTime);
		} 

		if($endTime == true){
			$list->where('door_open_log.create_time', '>', $endTime);
		} 

		if($build == true){
			$list->join('PropertyBuilding', function($join) use($build){
				$join->on('door_open_log.build_id', '=', 'property_building.id')
					 ->where('property_building.name', 'like', '%'.$build.'%');
			});
		}

		if($roomNum == true){
			$list->join('PropertyRoom', function($join) use($roomNum){
				$join->on('door_open_log.room_id', '=', 'property_room.id')
					 ->where('property_room.room_num', 'like', '%'.$roomNum.'%');
			});
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