<?php namespace YiZan\Services\System;

use YiZan\Models\DoorAccess; 
use YiZan\Models\District; 
use YiZan\Models\PuserDoor; 
use Exception, DB, Lang, Validator, App;

class DoorAccessService extends \YiZan\Services\DoorAccessService{

	/**
	 * 小区列表
	 */
	public static function getLists($sellerId, $districtId, $name, $pid, $isTotal, $page, $pageSize){
		$list = DoorAccess::where('seller_id', $sellerId)
						  ->where('district_id', $districtId)
						  ->orderBy('id', 'DESC');

		if($name == true){
			$list->where('name', $name);
		}

		if($pid == true){
			$list->where('pid', $pid);
		} 

 		// DB::connection()->enableQueryLog();
		if($isTotal == true){
			$list = $list->with('build')
						 ->get()
						 ->toArray();
		// print_r(DB::getQueryLog()); exit;
			return $list;
		} else {
			$totalCount = $list->count();
			$list = $list->skip(($page - 1) * $pageSize)
						 ->take($pageSize)
						 ->with('build')
						 ->get()
						 ->toArray();
			return ["list" => $list, "totalCount" => $totalCount];
		}
	} 

	/**
	 * 保存
	 */
	public static function save($id, $sellerId, $districtId, $name, $pid, $buildId, $remark, $type, 
		$installLockName = '', $installAddress = '', $installGps = '', $installWork = '', $installTelete = '', $installComm = ''){
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );

        if(empty($pid)){
        	$result['code'] = 30030;
        	return $result;
        }

        if(empty($installWork)){
        	$result['code'] = 30031;
        	return $result;
        } 

        if(empty($installTelete)){
        	$result['code'] = 30032;
        	return $result;
        }

		if($id > 0){
			$door = DoorAccess::find($id);
			if(empty($door)){
				$result['code'] = 21026;
				return $result;
			}
		} else {
			$door = new DoorAccess();

		} 

		$door->seller_id 			= $sellerId;
		$door->district_id 			= $districtId;
		$door->name 				= $name;
		$door->pid 					= $pid; 
		$door->build_id 			= $buildId; 
		$door->remark 				= $remark;
		$door->type 				= $type;
		$door->install_address 		= $installAddress;
		$door->install_gps 			= $installGps;
		$door->install_work 		= $installWork;
		$door->install_telete 		= $installTelete;
		$door->install_comm 		= $installComm;
		$district = District::find($districtId);
        try{
        	$rs = parent::deviceActivation($district->departid, $pid, $name, $installAddress, $installGps, $installWork, $installTelete, $installComm);
        	if($rs['status'] == 'success'){ 
        		$door->save();
        	} else {
        		$result['code'] = 30033;
        		return $result;
        	}
        } catch(Exception $e){ 
        	$result['code'] = 21030;
        	return $result;
        }
        return $result;
	}

	/**
	 * 删除
	 */
	public static function delete($id){
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );
        DoorAccess::where('id', $id)
        		  ->delete();
        PuserDoor::where('door_id', $id)
        		 ->delete();
        return $result;
	}

	/**
	 * 获取
	 */
	public static function getById($id){
		return DoorAccess::find($id);
	}
	
}