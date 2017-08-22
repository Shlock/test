<?php namespace YiZan\Services\System;

use YiZan\Models\PropertyBuilding;
use YiZan\Models\Seller;
use YiZan\Models\PropertyRoom;
use YiZan\Models\District;

use YiZan\Utils\String;
use Lang, DB;

class PropertyBuildingService extends \YiZan\Services\PropertyBuildingService {
	/**
	 * 系统楼宇列表
	 */
	public static function getSystemList($name, $sellerId, $page, $pageSize) {
		$list = PropertyBuilding::with('seller', 'district');
		if ($sellerId > 0) {
			$list->where('seller_id', $sellerId);
		}
		if (!empty($name)) {
			$list->where('name', 'like', '%'.$name.'%');
		}

		$total_count = $list->count();
		$list->orderBy('id', 'desc');

		$list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
		return ["list" => $list, "totalCount" => $total_count];
	}

	public static function getSystemById($id) {
		$data = PropertyBuilding::with('seller','district')->find($id);
		if (!$data) {
			$result['code'] = 80203;
			return $data;
		}

		return $data;
	}

	/*
	* 添加、编辑
	*/
	public static function save($id = 0, $name, $sellerId, $remark) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '操作成功'
		);
		if (empty($name)) {
			$result['code'] = 80201;
			return $result;
		}

		if ($sellerId < 1) {
			$result['code'] = 80202;
			return $result;
		}
		if ($id > 0) {
			$build = PropertyBuilding::find($id);
			if (!$build) {
				$result['code'] = 80203;
				return $result;
			}
			$build->id = $id;
		} else {
			$build = new PropertyBuilding();
		}
		$districtId = District::where('seller_id', $sellerId)->pluck('id');

		$build->name 		= $name;
		$build->seller_id 	= $sellerId;
		$build->district_id = (int)$districtId;
		$build->status 		= 1;
		$build->remark 		= $remark;
		$build->save();
		
		return $result;
	}

	/**
	 * 删除
	 * @param  [type] $id     [description]
	 * @return [type]         [description]
	 */
	public static function deleteSystem($id) {
		if (!$id) {
			$result['code'] = 80203;
			return $result;
		}
		//删除，待完善，相关信息
		DB::beginTransaction();
        try {
            PropertyBuilding::destroy($id);
            \YiZan\Models\PropertyRoom::where('build_id', $id)->delete();
            $puserId = \YiZan\Models\PropertyUser::where('build_id', $id)->lists('id');
            \YiZan\Models\PuserDoor::whereIn('puser_id', $puserId)->delete();
            \YiZan\Models\DoorOpenLog::where('build_id', $id)->delete();
            \YiZan\Models\PropertyUser::where('build_id', $id)->delete();
            \YiZan\Models\PropertyFee::where('build_id', $id)->delete();
            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }

	    return true;
	}
}
