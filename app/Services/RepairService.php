<?php namespace YiZan\Services;

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
use DB, Lang, Exception, Validator;

class RepairService extends BaseService {
	/*
	* 后台报修列表
	*/
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

	public static function getById($id){
		$data = Repair::where('id', $id)
					 ->with('build', 'room', 'puser', 'types')
		             ->first();
		// print_r($data->toArray());
		// exit;
		return $data;
	}

	/*
	* 处理报修
	*/
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

	public static function getRepairLists($userId, $districtId, $page){
		//DB::connection()->enableQueryLog();
		$list = Repair::orderBy('create_time', 'DESC')
						->where('district_id', $districtId)
						->where('user_id', $userId)
						->skip(($page - 1) * 20)
			            ->take(20)
			            ->with('build', 'room', 'puser', 'types')
			            ->get()
			            ->toArray();
		foreach ($list as $key => $value) {
			$list[$key]['images'] = $value['images'] ? explode(',', $value['images']) : null;
			$list[$key]['repairType'] = $value['types']['name'];
			$list[$key]['createTime'] = yztime($value['createTime']);
		}
		  // print_r($list);
		  // print_r(DB::getQueryLog());exit;
    	return $list;
	}

	public static function get($id, $districtId){
		$data = Repair::where('id', $id)
					 ->where('district_id', $districtId)
					 ->with('build', 'room', 'puser', 'types', 'district')
		             ->first();
		$data = $data ? $data->toArray() : NULL;
		$data['images'] = $data['images'] ? explode(',', $data['images']) : null;
		$data['repairType'] = $data['types']['name'];
		$data['createTime'] = yztime($data['createTime']);
		// print_r($data);
		// exit;
		return $data;
	}

	public static function getRepairTypeLists() {
        $list = RepairType::orderBy('id', 'desc')->get()->toArray();
        
        return $list;
	}

	public static function createRepair($userId, $districtId, $typeId, $images, $content) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '提交成功'
		);
		if ($districtId < 1) {
			$result['code'] = 21026;
			return $data;
		}
        if ($typeId < 1) {
        	$result['code'] = 50107;
			return $data;
        }

		$puser = PropertyUser::where('user_id', $userId)->where('district_id', $districtId)->first();
		if (!$puser) {
			$result['code'] = 80219;
			return $data;
		}

		if (count($images) > 0) {
            foreach ($images as $key => $image) {
                $images[$key] = self::moveSellerImage($puser->seller_id, $image);
                if (!$images[$key]) {
                    $result['code'] = 50004;
                    return $result;
                }
            }
            $images = implode(',', $images);
        } else {
            $images = '';
        }

		$repair = new Repair();
		
		$repair->build_id			= $puser->build_id;
		$repair->seller_id 			= $puser->seller_id;
		$repair->room_id			= $puser->room_id;
		$repair->district_id 		= $districtId;
		$repair->puser_id			= $puser->id;
		$repair->user_id 			= $userId;
		$repair->type 				= $typeId;
		$repair->content 			= $content;
		$repair->images 			= $images;
		$repair->status 			= 0;
		$repair->create_time		= UTC_TIME;
		$repair->save();
		
		return $result;
	}
}
