<?php namespace YiZan\Services\System;

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
	public static function getSystemList($roomNum, $sellerId, $buildId, $page, $pageSize) {
		$list = PropertyRoom::orderBy('id', 'desc');
		if ($sellerId > 0) {
			$list->where('seller_id', $sellerId);
		}
		if ($buildId > 0) {
			$list->where('build_id', $buildId);
		}
		if (!empty($roomNum)) {
			$list->where('room_num', 'like', '%'.$roomNum.'%');
		}

		$total_count = $list->count();

		$list = $list->with('seller', 'district', 'build')->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
		return ["list" => $list, "totalCount" => $total_count];
	}

	public static function getSystemById($id) {
		$data = PropertyRoom::with('seller', 'district', 'build')->find($id);
		if (!$data) {
			$result['code'] = 80203;
			return $data;
		}

		return $data;
	}

	/*
	* 添加、编辑
	*/
	public static function save($id = 0, $buildId, $sellerId, $roomNum, $owner, $mobile, $propertyFee, $structureArea, $roomArea, $intakeTime, $remark) {
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
            'mobile.required' 	=> 80210,// 请输入手机号码
			'mobile.regex'		=> 30602,// 手机号码格式错误
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
			$room_check = PropertyRoom::where('room_num', $roomNum)->where('build_id', $buildId)->where('seller_id', $sellerId)->first();
			if ($room_check) {
				$result['code'] = 80211;
				return $result;
			}
			$room = new PropertyRoom();
		}
		
		$districtId = District::where('seller_id', $sellerId)->pluck('id');

		$room->build_id			= $buildId;
		$room->seller_id 		= $sellerId;
		$room->district_id 		= (int)$districtId;
		$room->room_num			= $roomNum;
		$room->owner 			= $owner;
		$room->mobile 			= $mobile;
		$room->property_fee 	= (float)$propertyFee;
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
            \YiZan\Models\PropertyFee::where('room_id', $id)->delete();
            
            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
	    return true;
	}
}
