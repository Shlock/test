<?php
namespace YiZan\Services\Buyer;

use YiZan\Models\HotWords; 
use YiZan\Models\Region; 
use YiZan\Utils\Time;
use YiZan\Utils\String;
use DB, Exception,Validator;

/**
 * 热搜关键词
 */
class HotWordsService extends \YiZan\Services\HotWordsService {
 	
 	/**
 	 * 热搜关键词列表
 	 *
 	 */
 	public static function getLists($cityId, $pageSize){ 
 		// DB::connection()->enableQueryLog(); 
 		$list = HotWords::orderBy('id', 'DESC');
 		if($cityId > 0){
 			$list->where('city_id', '=', $cityId);
 		} 
 		$list = $list->skip(0)
 					 ->take($pageSize)
 					 ->with('province','city','area')
 					 ->get();
 		// print_r(DB::getQueryLog());exit;
 		return $list;
 	}
 
}
