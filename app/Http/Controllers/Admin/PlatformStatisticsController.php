<?php 
namespace YiZan\Http\Controllers\Admin; 
 
use Input, View,Time;

/**
 * 平台统计
 */
class PlatformStatisticsController extends AuthController {
	
	/**
	 * 平台营业统计
	 */
	public function index() {
        $args = Input::all(); 
        $args['nav'] = intval($args['nav']);
        $args['year'] = ($args['year'] > 0) ? $args['year'] : Time::toDate(UTC_TIME, 'Y');
        $args['month'] = ($args['month'] > 0) ? $args['month'] : Time::toDate(UTC_TIME, 'm');
        //获取订单列表中的年份
        $orderyear = $this->requestApi('seller.statistics.year');  
        View::share('orderyear',$orderyear['data']);
		$list = $this->requestApi('seller.statistics.platform', $args); 
		// print_r($list);exit;
		View::share('lists', $list['data']['list']);
		View::share('sum', $list['data']['sum']);
		View::share('args', $args);
		return $this->display();
	} 

}
