<?php namespace YiZan\Services\Buyer;

use YiZan\Models\Buyer\Restaurant;
use YiZan\Models\Buyer\Goods;
use YiZan\Utils\String;
use DB, Lang, Validator;

class RestaurantService extends \YiZan\Services\RestaurantService {

	/**
	 * [lists 获取餐厅列表]
	 * @param  [type] $sort     [排序方式 0：销量倒序 1：好评倒序]
	 * @param  [type] $page     [当前页码]
	 * @param  [type] $pageSize [页码大小]
	 * @return [type]           [description]
	 */
	public static function lists($userId, $sort, $page, $pageSize) {
		$tablePrefix = DB::getTablePrefix();
// DB::connection()->enableQueryLog();
		$list = Restaurant::select('restaurant.*')
				->selectRaw("(select count(1) from {$tablePrefix}goods where {$tablePrefix}restaurant.id = {$tablePrefix}goods.restaurant_id) num")
				->selectRaw("(select count(1) from {$tablePrefix}user_collect_restaurant where {$tablePrefix}restaurant.id = {$tablePrefix}user_collect_restaurant.restaurant_id and {$tablePrefix}user_collect_restaurant.user_id = {$userId}) isCollect")
				->where('restaurant.dispose_status', 1)
				->where('restaurant.status', 1);
				
		if($sort == 0){
			$list->orderBy('restaurant.sale_count', 'desc');
		}
		elseif($sort == 1){
		 	$list->orderBy('restaurant.star', 'desc');
		}else{
			$list->orderBy('restaurant.id', 'desc');
		}

		$list       = $list->skip(($page - 1) * $pageSize)
						   ->take($pageSize)
						   ->with('seller')
						   ->get()
						   ->toArray();
// print_r(DB::getQueryLog());die;
		return $list;
	}

	/**
	 * 查看餐厅详细信息 + 是否收藏
	 * @param  [type] $userId [会员ID]
	 * @param  [type] $id [审核ID]
	 * @return [array]    [数组]
	 */
	public static function lookat($userId, $id) {
		$result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg' => '',
        );
		DB::connection()->enableQueryLog();
        if($id < 1) {
        	$result['code'] = 10000;
        	$result['msg'] = Lang::get('api_system.code.10000');
        	return $result;
        }
		$result['data'] = Restaurant::where('id', $id)
							->with(array('collect' => function($query) use($userId){
							    $query->where('user_id', $userId);
							}))
							->with('seller')
							->first();
		return $result;
	}

}