<?php 
namespace YiZan\Http\Controllers\Seller; 
 
use Input, View,Time;

/**
 * 商家管理
 */
class BusinessStatisticsController extends AuthController {
	
	/**
	 * 商家营业统计
	 */
	public function index() { 
        $args = Input::all();
        $args['year'] = ($args['year'] > -99) ? $args['year'] : Time::toDate(UTC_TIME, 'Y');
        $args['month'] = ($args['month'] > -99) ? $args['month'] : Time::toDate(UTC_TIME, 'm');
        //获取订单列表中的年份
        $orderyear = $this->requestApi('seller.statistics.year'); 
        View::share('orderyear',$orderyear['data']);
		$list = $this->requestApi('seller.statistics.monthlists', $args); 
		// print_r($list);exit;
		View::share('lists', $list['data']['list']);
		View::share('sum', $list['data']['sum']);
		View::share('args', $args);
		return $this->display();
	}

	/**
	 * 天对账单
	 */
	public function dayAccount(){
        $args = Input::all();  
		$list = $this->requestApi('seller.statistics.daylists', $args); 
		// print_r($list);exit;
		View::share('lists', $list['data']['list']);
		View::share('sum', $list['data']['sum']);
		View::share('args', $args);
		return $this->display();
	}
}
