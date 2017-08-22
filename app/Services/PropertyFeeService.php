<?php namespace YiZan\Services;

use YiZan\Models\PropertyBuilding;
use YiZan\Models\Seller;
use YiZan\Models\PropertyFee;
use YiZan\Models\PropertyUser;
use YiZan\Models\PropertyRoom;
use YiZan\Models\District;

use YiZan\Utils\String;
use Lang, DB, Validator, Time;

class PropertyFeeService extends BaseService {
	
	public static function getLists($sellerId, $name, $build, $roomNum, $beginTime, $endTime, $page, $pageSize, $status) {
		$list = PropertyFee::where('property_fee.seller_id', $sellerId);
		
		if ($name == true) {
			$list->where('property_fee.name', 'like', '%'.$name.'%');
		}

		if ($build == true ) {
			$list->join('property_building', function($join) use($build){
				$join->on('property_building.id', '=', 'property_fee.build_id')
					->where('property_building.name', 'like', "%{$build}%");
			});
		}

		if ($roomNum == true ) {
			$list->join('property_room', function($join) use($roomNum){
				$join->on('property_room.id', '=', 'property_fee.room_id')
					->where('property_room.room_num', 'like', "%{$roomNum}%");
			});
		}

		$beginTime = Time::toTime($beginTime);
		$endTime = Time::toTime($endTime);
		if ($beginTime > 0) {
			$list->where('property_fee.payable_time', '>=', $beginTime);
		}
		if ($endTime > 0) {
			$list->where('property_fee.payable_time', '<=', $endTime);
		}

		if ($status > 0) {
			$list->where('property_fee.status', $status - 1);
		}

		$total_count = $list->count();
		$list->orderBy('property_fee.id', 'desc');

		$list = $list->with('seller', 'build', 'room')->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
		return ["list" => $list, "totalCount" => $total_count];
	}



	public static function getById($id) {
		$data = PropertyFee::with('seller', 'build', 'room')->find($id);
		if (!$data) {
			$result['code'] = 80203;
			return $data;
		}

		return $data;
	}

	/*
	* 添加、编辑
	*/
	public static function save($sellerId, $id = 0, $buildId, $roomId, $name, $payableFee, $payableTime) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '操作成功'
		);

		$rules = array( 
            'buildId'      	=> ['required'],
            'roomId'        => ['required'],
            'name'       	=> ['required'],
            'payableFee'  	=> ['required'],
            'payableTime'  	=> ['required'],
        );
        
        $messages = array
        ( 	
        	'buildId.required'		=> 80201,	
            'roomId.required'		=> 80204,	
            'name.required'			=> 80206,	
            'payableFee.required'	=> 80221,	
            'payableTime.required'	=> 80222,
        );

        $validator = Validator::make(
            [ 
                'buildId'   => $buildId,
                'roomId'     => $roomId,
                'name'    => $name,
                'payableFee' => $payableFee,
                'payableTime' => $payableTime,
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
		$build_room = PropertyRoom::where('build_id', $buildId)->where('seller_id', $sellerId)->where('id', $roomId)->first();
		if (!$build_room) {
			$result['code'] = 80219;
			return $data;
		}
		if ($id > 0) {
			$fee = PropertyFee::find($id);
			if (!$fee) {
				$result['code'] = 80223;
				return $result;
			}
		} else {
			$fee = new PropertyFee();
		}
		
		$districtId = District::where('seller_id', $sellerId)->pluck('id');

		$fee->build_id			= $buildId;
		$fee->district_id 		= $districtId;
		$fee->seller_id 		= $sellerId;
		$fee->room_id			= $roomId;
		$fee->name 				= $name;
		$fee->payable_fee		= $payableFee;
		$fee->payable_time 		= Time::toTime($payableTime);
		$fee->status 			= 0; //未缴费
		$fee->save();
		
		return $result;
	}

	/**
	 * 删除
	 * @param  [type] $id     [description]
	 * @return [type]         [description]
	 */
	public static function delete($id) {
		if (!$id) {
			$result['code'] = 80203;
			return $result;
		}
		//删除，待完善，相关信息
		DB::beginTransaction();
        try {
            PropertyFee::destroy($id);
            
            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
	    return true;
	}

	public static function savePayFee($sellerId, $id, $payFee, $payTime) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '操作成功'
		);

		if ($sellerId < 1) {
			$result['code'] = 80202;
			return $result;
		}

		if (empty($payFee)) {
			$result['code'] = 80221;
			return $result;
		}

		$payTime = Time::toTime($payTime);
		if (!$payTime) {
			$result['code'] = 80222;
			return $result;
		}
		$fee = PropertyFee::find($id);
		if (!$fee) {
			$result['code'] = 80223;
			return $result;
		}
		
		$fee->pay_fee		= $payFee;
		$fee->pay_time 		= $payTime;
		$fee->status 		= 1;  //已缴费
		$fee->save();
		
		return $result;
	}

	//导入
	public static function import($sellerId, $build, $roomNum, $name, $payableFee, $payableTime, $payFee, $payTime) {
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
				$roomId = PropertyRoom::where('room_num', strval($roomNum[$i]))->where('build_id', $buildId)->where('seller_id', $sellerId)->pluck('id');
				if (!$roomId) {
					$result['code'] = 80218;
					return $result;
				}
				$districtId = District::where('seller_id', $sellerId)->pluck('id');
				$fee = new PropertyFee();
				$fee->build_id			= $buildId;
				$fee->district_id 		= $districtId;
				$fee->seller_id 		= $sellerId;
				$fee->room_id			= $roomId;
				$fee->name 				= $name[$i];
				$fee->payable_fee 		= floatval($payableFee[$i]);
				$fee->payable_time 		= Time::toTime($payableTime[$i]);
				$fee->pay_fee 			= floatval($payFee[$i]);
				$fee->pay_time 			= Time::toTime($payTime[$i]);
				$fee->status 			= 1;
				$fee->save();
				
			}
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 80218;
        }
		
		return $result;
	}

}
