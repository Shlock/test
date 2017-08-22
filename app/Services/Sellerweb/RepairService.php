<?php namespace YiZan\Services\Sellerweb;

use YiZan\Models\Seller;
use YiZan\Models\PropertyBuilding;
use YiZan\Models\District;
use YiZan\Models\PropertyUser;
use YiZan\Models\PropertyRoom;
use YiZan\Models\User;
use YiZan\Models\Repair;
use YiZan\Models\RepairType;
use YiZan\Services\DoorAccessService;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use Illuminate\Database\Query\Expression;
use DB, Lang, Exception;


class RepairService extends \YiZan\Services\RepairService {
	
	/*
	public static function getLists($sellerId, $name, $build, $roomNum, $page, $pageSize, $status){
		//DB::connection()->enableQueryLog();
		$list = Repair::orderBy('repair.create_time', 'DESC')
						->where('repair.seller_id', $sellerId)
						->where('repair.status', $status);

		if($name == true){
			$list->join('property_user', function($join) use($name){
				$join->on('property_user.id', '=', 'repair.build_id')
					->where('property_user.name', 'like', "%{$name}%");
			});
		}

		if ($build == true ) {
			$list->join('property_building', function($join) use($build){
				$join->on('property_building.id', '=', 'repair.build_id')
					->where('property_building.name', 'like', "%{$build}%");
			});
		}

		if ($roomNum == true ) {
			$list->join('property_room', function($join) use($roomNum){
				$join->on('property_room.id', '=', 'repair.room_id')
					->where('property_room.room_num', 'like', "%{$roomNum}%");
			});
		}

    	$totalCount = $list->count();
 		$list = $list->skip(($page - 1) * $pageSize)
		             ->take($pageSize)
		             ->with('build', 'room', 'puser', 'types')
		             ->get()
		             ->toArray();
		 // print_r($list);
		 // print_r(DB::getQueryLog());exit;
    	return ["list"=>$list, "totalCount"=>$totalCount];
	}
	*/

	public static function get($id){
		$data = Repair::where('id', $id)
					 ->with('build', 'room', 'puser', 'types')
		             ->first();
		// print_r($data->toArray());
		// exit;
		return $data;
	}

/*
	public static function save($id, $sellerId, $status){
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '操作成功'
		];	

		if((int)$sellerId < 1){
			$result['code'] = 80202;
			return $result;
		} 
		$repair = Repair::where('id', $id)->first();
		if (!$repair) {
			$result['code'] = 80215;
			return $result;
		}

		if ($repair->status != 0 && $repair->status == $status) {
			$result['code'] = 80216;
			return $result;
		}

		if ($status == 1) {
			$repair->dispose_time = UTC_TIME;
		} else {
			$repair->finish_time = UTC_TIME;
		}
		$repair->status = $status;
		$repair->save();

		return $result; 
	}
	
	*/

}
