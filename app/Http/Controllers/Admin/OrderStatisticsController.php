<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\OrderConfig;
use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Form;
/**
 * 订单统计
 */
class OrderStatisticsController extends AuthController {
	public function index(){
		$args = array();
		$begin_time = Input::get('beginTime');
		$end_time = Input::get('endTime');
		if ($begin_time != '' && $end_time != '') {
			$args = array(
				'beginTime' => $begin_time,
				'endTime' => $end_time
			);
		}
		$data = $this->requestApi('order.ordercount.ordernum',$args);
		if($data['code'] == 0){
			View::share('data', $data['data']);
		}else if($data['code'] == 19999){
		    //return $this->error("时间段必须为1-15天");
		}
		return $this->display();
	}
}
