<?php namespace YiZan\Services\Sellerweb;

use YiZan\Models\PropertyBuilding;
use YiZan\Models\Seller;
use YiZan\Models\PropertyRoom;
use YiZan\Models\District;

use YiZan\Utils\String;
use Lang, DB, Validator, Time;

class PropertyRoomService extends \YiZan\Services\PropertyRoomService {
	/**
	 * 房间列表
	 */
	public static function getSystemList($sellerId, $roomNum, $buildId, $owner, $mobile, $page, $pageSize) {
		$list = PropertyRoom::where('seller_id', $sellerId);
		
		if ($buildId) {
			$list->where('build_id', $buildId);
		}

		if (!empty($roomNum)) {
			$list->where('room_num', 'like', '%'.$roomNum.'%');
		}

		if (!empty($owner)) {
			$list->where('owner', 'like', '%'.$owner.'%');
		}

		if (!empty($mobile)) {
			$list->where('mobile', $mobile);
		}

		$total_count = $list->count();
		$list->orderBy('id', 'desc');

		$list = $list->with('seller', 'district', 'build')->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
		return ["list" => $list, "totalCount" => $total_count];
	}

	public static function getSystemById($id) {
		$data = PropertyRoom::with('seller', 'build')->find($id);
		if (!$data) {
			$result['code'] = 80203;
			return $data;
		}

		return $data;
	}

	/*
	* 添加、编辑
	*/
	public static function save($sellerId, $id = 0, $buildId, $roomNum, $owner, $mobile, $propertyFee, $structureArea, $roomArea, $intakeTime, $remark) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '操作成功'
		);

		$rules = array( 
            'roomNum'      => ['required'],
            'owner'        => ['required'],
            'mobile'       => ['required','regex:/^1[0-9]{10}$/'],
            'propertyFee'  => ['required'],
        );
        
        $messages = array
        ( 
            'roomNum.required'	=> 80204,	
            'owner.required'	=> 80206,	
            'mobile.required'	=> 60002,	
            'mobile.regex'		=> 10102,// 手机号码格式错误
            'propertyFee.required'	=> 80217,
        );

        $validator = Validator::make(
            [ 
                'roomNum'   => $roomNum,
                'owner'     => $owner,
                'mobile'    => $mobile,
                'propertyFee' => $propertyFee,
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails()) 
        {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        } 

		if ($buildId < 1) {
			$result['code'] = 80201;
			return $result;
		}
		if ($sellerId < 1) {
			$result['code'] = 80202;
			return $result;
		}
		if ($id > 0) {
			$room = PropertyRoom::find($id);
			if (!$room) {
				$result['code'] = 80203;
				return $result;
			}
		} else {
			$room = new PropertyRoom();
			$room_check = PropertyRoom::where('room_num', $roomNum)->where('build_id', $buildId)->where('seller_id', $sellerId)->first();
			if ($room_check) {
				$result['code'] = 80211;
				return $result;
			}
		}
		$districtId = District::where('seller_id', $sellerId)->pluck('id');

		$room->build_id			= $buildId;
		$room->seller_id 		= $sellerId;
		$room->district_id 		= $districtId;
		$room->room_num			= $roomNum;
		$room->owner 			= $owner;
		$room->mobile 			= $mobile;
		$room->property_fee 	= $propertyFee;
		$room->structure_area	= (float)$structureArea;
		$room->room_area		= (float)$roomArea;
		$room->intake_time		= Time::toTime($intakeTime);
		$room->remark 			= $remark;
		$room->save();
		
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
            PropertyRoom::destroy($id);
            $puserId = \YiZan\Models\PropertyUser::where('room_id', $id)->lists('id');
            \YiZan\Models\PuserDoor::whereIn('puser_id', $puserId)->delete();
            \YiZan\Models\DoorOpenLog::where('room_id', $id)->delete();
            \YiZan\Models\PropertyUser::where('room_id', $id)->delete();
            \YiZan\Models\Repair::where('room_id', $id)->delete();
            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
	    return true;
	}

	//导入
	public static function import($sellerId, $build, $roomNum, $owner, $mobile, $propertyFee, $structureArea, $roomArea, $intakeTime, $remark) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '导入成功'
		);
		$count = count($build);
		if ($count < 1) {
			$result['code'] = 80218;
			return $result;
		}

		DB::beginTransaction();
		try {
			for ($i=0; $i < $count; $i++) { 
				$buildId = PropertyBuilding::where('seller_id', $sellerId)->where('name', strval($build[$i]))->pluck('id');
				if (!$buildId) {
					$result['code'] = 80218;
					return $result;
				}
				$room_check = PropertyRoom::where('room_num', strval($roomNum[$i]))->where('build_id', $buildId)->where('seller_id', $sellerId)->first();
				if ($room_check) {
					$result['code'] = 80218;
					return $result;
				}

				$districtId = District::where('seller_id', $sellerId)->pluck('id');
				$room = new PropertyRoom();
				$room->build_id			= $buildId;
				$room->seller_id 		= $sellerId;
				$room->district_id 		= (int)$districtId;
				$room->room_num			= $roomNum[$i];
				$room->owner 			= $owner[$i];
				$room->mobile 			= $mobile[$i];
				$room->property_fee 	= $propertyFee[$i];
				$room->structure_area	= $structureArea[$i];
				$room->room_area		= $roomArea[$i];
				$room->intake_time		= Time::toTime($intakeTime[$i]);
				$room->remark 			= $remark[$i];
				$room->save();
				
			}
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 80218;
        }
		
		return $result;
	}

}
