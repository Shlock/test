<?php namespace YiZan\Services\System;

use YiZan\Models\Seller;
use YiZan\Models\PropertyBuilding;
use YiZan\Models\District;
use YiZan\Models\PropertyUser;
use YiZan\Models\PropertyRoom;
use YiZan\Models\User;
use YiZan\Models\DoorAccess;
use YiZan\Models\PuserDoor;
use YiZan\Models\DoorOpenLog;
use YiZan\Services\DoorAccessService;
use YiZan\Utils\Time;
use YiZan\Utils\String;

use Exception, Lang, Validator, DB, Config;

class PropertyUserService extends \YiZan\Services\PropertyUserService {
	
	public static function getLists($sellerId, $name, $build, $roomNum, $page, $pageSize){
		$list = PropertyUser::orderBy('property_user.id', 'DESC')
							->where('property_user.seller_id', $sellerId)
							->where('property_user.build_id', '>', 0)
							->where('property_user.room_id', '>', 0)
							->where('property_user.status', 1);
		
		if($name == true){
			$list->where('property_user.name', 'like', '%'.$name.'%');
		}

		if ($build == true ) {
			$list->join('property_building', function($join) use($build){
				$join->on('property_building.id', '=', 'property_user.build_id')
					->where('property_building.name', 'like', "%{$build}%");
			});
		}

		if ($roomNum == true ) {
			$list->join('property_room', function($join) use($roomNum){
				$join->on('property_room.id', '=', 'property_user.room_id')
					->where('property_room.room_num', 'like', "%{$roomNum}%");
			});
		}

		// if($status > 0){
		// 	$list->where('status', $status - 2);
		// }
		
    	$totalCount = $list->count();
 		
 		$list = $list->skip(($page - 1) * $pageSize)
		             ->take($pageSize)
		             ->with('district', 'seller', 'build', 'room')
		             ->get()
		             ->toArray();
		      //  print_r($list);
    	return ["list"=>$list, "totalCount"=>$totalCount];
	}

	/*
	* 获取可用门禁列表
	*/
	public static function getdoorsLists($sellerId){
		$list = DoorAccess::orderBy('id', 'DESC')
					 ->where('seller_id', $sellerId)
		             ->get()
		             ->toArray();

		return $list;
	}

	/*
	* 获取业主门禁列表
	*/
	public static function getTotalLists($puserId, $sellerId, $page, $pageSize){
		DB::connection()->enableQueryLog();
		$list = PuserDoor::where('puser_door.puser_id', $puserId)
					->join('door_access', function($join) use($sellerId) {
						$join->on('door_access.id', '=','puser_door.door_id')
							 ->where('door_access.seller_id', '=', $sellerId);
					});

		$totalCount = $list->count();
		$list = $list->select('puser_door.*')
					 ->skip(($page - 1) * $pageSize)
		             ->take($pageSize)
		             ->with('puser', 'door')
		             ->get()
		             ->toArray();

		// print_r($list);
		//      print_r(DB::getQueryLog());exit;
		return ["list"=>$list, "totalCount"=>$totalCount];
	}

	public static function get($id){
		$data = PuserDoor::where('id', $id)
					 ->with('puser', 'door')
		             ->first();
		return $data;
	}

	public static function save($id, $puserId, $doorId, $sellerId, $endTime){
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '操作成功'
		];	
		
		$endTime = Time::toTime($endTime);
		if($endTime < 1){
			$result['code'] = 80205;
			return $result;
		}
		if((int)$puserId < 1){
			$result['code'] = 80206;
			return $result;
		}
		if((int)$sellerId < 1){
			$result['code'] = 80202;
			return $result;
		}
		$door_check = PuserDoor::where('door_id', $doorId)->where('puser_id', $puserId)->where('id', '!=', $id)->first();
		if ($door_check) {
			$result['code'] = 80207;
			return $result;
		}

		$door = PuserDoor::find($id);
		if(!$door){
			$door = new PuserDoor();
		}

		DB::beginTransaction();
		try {
			$door->puser_id 	= $puserId;
			$door->door_id		= $doorId;
			$door->end_time		= $endTime;
			$door->save();
			$isOpenProperty = Config::get('app.is_open_property');
			//如果系统开启了物业功能的话 访问妙兜接口
			if($isOpenProperty){
				$puser = PropertyUser::where('id', $puserId)->first(); 
				$pdoor = DoorAccess::find($doorId);
				$doors = DoorAccessService::keyApply($pdoor->pid, $puser->mobile, $endTime, '', '', '', '', ''); 
				//print_r($doors['code']);
				if ($doors['code'] == '0') {
					$dooraccess = $doors['msg'][0];
					//print_r($doors);
					$door_data['community'] = $dooraccess['community'];
					$door_data['app_key'] = $dooraccess['app_key'];
					$door_data['lock_id'] = $dooraccess['lock_id'];
					PuserDoor::where('id', $door->id)->update($door_data);
					DB::commit();
				} else {
					DB::rollback();
					$result['code'] = 30034;
					return $result;
				} 
			} else {
				DB::commit();
			}
		} catch (Exception $e) {
			$result['code'] = 99999;
		}
		return $result;
	}


    /**
     * 删除门禁
     * @param int $id;  服务编号
     * @return [type] [description]
     */
    public static function deleteDoor($id){
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];

        $door = PuserDoor::find($id);
        if (!$door) {
        	$result['code'] = 80208;
            return $result;
        }

        DoorOpenLog::where('door_id', $door->id)->where('puser_id', $door->puser_id)->delete();
		PuserDoor::where('id',$id)->delete();
        // print_r(DB::getQueryLog());exit;
        return $result;
    }

	public static function delete($id){
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '删除成功'
		];

		$puser = PropertyUser::find($id);
        if (!$puser) {
        	$result['code'] = 80209;
            return $result;
        }
        PuserDoor::where('puser_id', $id)->delete();
        DoorOpenLog::where('puser_id', $id)->delete();
		PropertyUser::where('id', $id)->delete();

		return $result;
	}

}
