<?php namespace YiZan\Services\System;

use YiZan\Models\System\Restaurant;
use YiZan\Models\System\Goods;
use YiZan\Utils\String;
use DB, Lang, Validator;

class RestaurantService extends \YiZan\Services\RestaurantService {

	/**
	 * 获取餐厅列表
	 * @param  [type] $name     [餐厅名称]
	 * @param  [type] $page     [分页]
	 * @param  [type] $pageSize [分页参数]
	 * @return [type]           [返回数组]
	 */
	public static function lists($name, $page, $pageSize) {
		$tablePrefix = DB::getTablePrefix();

		$list = Restaurant::select('restaurant.*')
				->selectRaw("(select count(1) from {$tablePrefix}goods where {$tablePrefix}restaurant.id = {$tablePrefix}goods.restaurant_id) num")
				->where('restaurant.dispose_status',1)
				->orderBy('restaurant.id', 'desc');
		if(!empty($name)){
			$list->where('name', 'like', '%'.$name.'%');
		}
		$totalCount = $list->count();
		$list       = $list->skip(($page - 1) * $pageSize)
						   ->take($pageSize)
						   ->with('seller')
						   ->get()
						   ->toArray();

		return ["list" => $list, "totalCount" => $totalCount];
	}

	/**
	 * 餐厅审核列表
	 * @param  [type] $name [机构名称]
	 * @param  [type] $tel      [机构电话]
	 * @param  [type] $disposeStatus      [状态]
	 * @param  [type] $page        [分页]
	 * @param  [type] $pageSize    [分页参数]
	 * @return [type]              [返回数组]
	 */
	public static function applyLists($name, $tel, $disposeStatus, $page, $pageSize) 
    {
		$list = Restaurant::orderBy('id', 'desc');
		//等于0查询所有
		if($disposeStatus > 0){
			$list->where('dispose_status',$disposeStatus - 2);
		}
		if(!empty($name)){
			$list->where('name', 'like', '%'.$name.'%');
		}
		if(!empty($tel)){
			$list->where('tel', 'like', '%'.$tel.'%');
		}

		$totalCount = $list->count();
		$list       = $list->skip(($page - 1) * $pageSize)
						   ->take($pageSize)
						   ->with('adminuser')
						   ->get()
						   ->toArray();
		return ["list" => $list, "totalCount" => $totalCount];
	}

	/**
	 * 查看餐厅详细信息
	 * @param  [type] $id [审核ID]
	 * @return [array]    [数组]
	 */
	public static function lookat($id) {
		$result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg' => '',
        );

        if($id < 1) {
        	$result['code'] = 10000;
        	$result['msg'] = Lang::get('api_system.code.10000');
        	return $result;
        }
		$result['data'] = Restaurant::where('id', $id)
							->with('seller')
							->first();
		return $result;
	}

	/**
	 * 处理餐厅申请
	 * @param  [type] $adminId         [处理人员ID]
	 * @param  [type] $id              [申请ID]
	 * @param  [type] $ellerId		   [分配的餐厅id]
	 * @param  [type] $disposeStatus          [description]
	 * @param  [type] $disposeResult   [description]
	 * @return [type]                  [description]
	 */
	public static function dispose($adminId, $id, $sellerId, $disposeStatus, $disposeResult) {
		$result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.update_info'),
		);

		$rules = array(
			'id'         => ['numeric'],
			'sellerId'   => ['numeric'],
			'disposeStatus'     => ['required'],
		);

		$messages = array
        (
            'id.numeric'	    => 10000,
            'sellerId.numeric'	=> 10110,
            'disposeStatus.required'	=> 40204,
        );

        //驳回必填
        if($disposeStatus==-1){
        	$rules['disposeResult'] = ['required'];
        	$messages['disposeResult.required'] = 10111;
	    }

		$validator = Validator::make(
            [
				'id'      => $id,
				'sellerId'      => $sellerId,
				'disposeStatus'      => $disposeStatus,
				'disposeResult' => $disposeResult
			], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }

	    $update = [
	    	'seller_id'       => $sellerId,
            'dispose_status'       => $disposeStatus,
            'dispose_result' => $disposeResult,
            'dispose_admin_id' => $adminId,
            'dispose_time' => UTC_TIME
	    ];
	    
        Restaurant::where("id", $id)->update($update);
        
        return $result;
	}

	/**
	 * 删除餐厅
	 * @param  [type] $sellerId [服务站ID]
	 * @param  [type] $id       [餐厅ID]
	 * @return [type]           [返回数组]
	 */
	public static function delete($id) {
		$result =	[
				'code'	=> 0,
				'data'	=> null,
				'msg'	=> Lang::get('api_system.success.delete')
			];

		$res = Restaurant::where('id', $id)->delete();

  		if(!$res){
  			$result['code'] = 20108;
  			$result['code'] = Lang::get('api_system.code.20108');
  		}
        
		return $result;
	}
}