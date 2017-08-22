<?php namespace YiZan\Services\Buyer;

use YiZan\Models\District; 
use YiZan\Models\PropertyBuilding; 
use YiZan\Models\PropertyRoom;
use YiZan\Models\PropertyUser; 
use YiZan\Models\Seller;  
use Exception, DB, Lang, Validator, App;

class DistrictService extends \YiZan\Services\DistrictService{

	/**
	 * 开通区/县
	 */
	public static function getOpenAreaLists($cityId){
		$data = District::where('city_id', $cityId)
						->with('area')
						->get()
						->toArray();
		$list = [];
		foreach ($data as $key => $val) {
			$arr = [];
			$arr['id'] = $val['area']['id'];
			$arr['name'] = $val['area']['name'];
			$list[] = $arr;
		}
		return $list;
	}

	/**
	 * 获取开通小区
	 */
	public static function getOpenVillageLists($areaId){
		$list = District::orderBy('id', 'DESC')
						->join('seller', function($join){
							$join->on('district.id', '=', 'seller.district_id');
						})
						->select('district.*');
		if($areaId > 0){
			$list->where('area_id', $areaId);
		}  
		$list = $list->get()
					 ->toArray(); 
		return $list;
	}

	/**
	 * 获取小区的楼栋
	 */
	public static function getBuildingLists($villagesid){
		$list = PropertyBuilding::where('district_id', $villagesid)
								->get()
								->toArray();
		return $list;
	}

	/**
	 * 获取楼栋的房间
	 */
	public static function getRoomLists($buildingid){
		$list = PropertyRoom::where('build_id', $buildingid)
							->get()
							->toArray();
		return $list;
	}

	/**
	 * 搜索小区
	 */
	public static function getSearchVillages($keywords){
		$list = District::where('district.name', 'like', "%{$keywords}%")
						->select('district.*')
						// ->join('seller', function($join){
						// 	$join->on('district.seller_id', '=', 'seller.id');
						// })
						->with('seller', 'province', 'city')
						->get()
						->toArray();
		return $list;
	}

	/**
	 * 获取最近小区列表
	 */
	public static function getNearestLists($location){ 
        $tablePrefix = DB::getTablePrefix();
        $location = str_replace(',', ' ', $location);
		$list = District::select('district.*')
						// ->join('seller', function($join){
						// 	$join->on('district.seller_id', '=', 'seller.id');
						// })
						->addSelect(DB::raw("ST_Distance({$tablePrefix}district.map_point,GeomFromText('POINT({$location})')) AS map_distance"))
						->having('map_distance', '<=', 0.03)
						->with('seller', 'province', 'city')
                        ->orderBy('map_distance', 'DESC')
						->get()
						->toArray();   
		foreach ($list as $key => $value) {
			$list[$key]['location'] = $value['province']['name'] . $value['city']['name'];
			unset($list[$key]['province']);
			unset($list[$key]['city']);
		} 
		return $list;
	}

	/**
	 * 我的小区列表
	 */
	public static function getMyLists($userId){
		$list = District::orderBy('district.id', 'DESC')
						->select('district.*')
						->join('property_user', function($join) use($userId) {
							$join->on('district.id', '=', 'property_user.district_id')
								 ->where('property_user.user_id', '=', $userId);
						})
						->with('seller')
						->get()
						->toArray();
		foreach ($list as $key => $value) {
			if ($value['seller']['status'] == 1 && $value['sellerId'] > 0) {
				$list[$key]['isEnter'] = 1;
			} else {
				$list[$key]['isEnter'] = 0;
			}
			$list[$key]['isUser'] = 1;
		}

		// print_r($list);
		// exit;
		return $list;
	}

	public static function createDistrict($userId, $districtId){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => '添加成功'
        );

        if (!$userId) {
        	$result['code'] = 99996;
            return $result;
        }

        $district = District::find($districtId);
        if (!$district) {
        	$result['code'] = 60313;
            return $result;
        }

        $dis_check = PropertyUser::where('district_id', $districtId)->where('user_id', $userId)->first();
        if ($dis_check) {
        	$result['code'] = 10615;
            return $result;
        }
        PropertyUser::where('user_id', $userId)->update(['is_default'=>0]);
		$puser = new PropertyUser();

        $puser->seller_id 			= $district->seller_id;
        $puser->district_id 		= $district->id;
        $puser->user_id 			= $userId;
        $puser->create_time 		= UTC_TIME;
        $puser->is_default 			= 1;
        $puser->save();

        return $result;
	}


	public static function getDistrict($userId, $districtId){
		//DB::connection()->enableQueryLog();
		$tablePrefix = DB::getTablePrefix();
		if ($districtId > 0) {
			$data = PropertyUser::where('property_user.user_id', $userId)
							->select('property_user.*')
							->join('district', function($join) use($districtId) {
								$join->on('district.id', '=', 'property_user.district_id')
									->where('district.id', '=', $districtId);
							})
							->with('seller', 'district', 'build', 'room')
							->first();

		} else {
			$data_def = PropertyUser::where('user_id', $userId)
								->where('seller_id', '>', 0)
								->where('district_id', '>', 0)
								->with('seller', 'district', 'build', 'room')
								->first();
			if ($data_def) {
				$data = $data_def;
			} else {
				$data = PropertyUser::where('user_id', $userId)
							->where('is_default', 1)
							->with('seller', 'district', 'build', 'room')
							->first();
			}
			
		}
		if ($data) {
			$data->district->sellerName = $data->district->seller_id > 0 ? $data->seller->name : null;
			$data->room->intake_time = yzday($data->room->intake_time);
			$data->countDistrict = PropertyUser::where('user_id', $userId)->count();
			if ($data->seller_id < 1) { //是否开通物业
				$data->isProperty = 1;
			}
			if (($data->seller_id > 0 && $data->room_id < 1 && $data->build_id < 1) || $data->status == -1) {
				$data->isVerify = 1;//是否申请验证
			}
			if ($data->status == 0 && $data->seller_id > 0 && $data->build_id > 0) {
				$data->isCheck = 1;//是否通过验证
			}
		}
		//print_r(DB::getQueryLog());exit;
		
		return $data;
	}

	/**
	 * 删除
	 */
	public static function delete($id){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => ''
        );
        
        if (!$id) {
            $result['code'] = 60313;
            return $result;
        }
        //删除，相关信息, 待完善
        DB::beginTransaction();
        try {
            $puserId = \YiZan\Models\PropertyUser::where('district_id', $id)->pluck('id');
            \YiZan\Models\PropertyUser::where('id', $puserId)->delete();
            \YiZan\Models\PropertyFee::where('puser_id', $puserId)->delete();
            \YiZan\Models\DoorOpenLog::where('puser_id', $puserId)->delete();
            \YiZan\Models\Repair::where('puser_id', $puserId)->delete();

            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
	}
	

}