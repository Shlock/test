<?php 
namespace YiZan\Http\Controllers\Admin; 

use YiZan\Utils\Time;
use Input, View, Route, Redirect;

/**
 * 业绩统计
 */
class PerformanceController extends AuthController {
	
	//业绩排行榜
	public function index() {
		$args = array();
		$begin_time = Input::get('beginTime');
		$end_time = Input::get('endTime');
		if ($begin_time != '' && $end_time != '') {
			$args = array(
				'beginDay' => Time::toTime($begin_time),
				'endDay' => Time::toTime($end_time)
			);
		}
		$data = $this->requestApi('admin.statistics.getPerformanceRanking',$args);	
		View::share('list', $data['data']);
		return $this->display();
	}

	//抽成排行榜
	public function bonus() {
		$args = array();
		$begin_time = Input::get('beginTime');
		$end_time = Input::get('endTime');
		if ($begin_time != '' && $end_time != '') {
			$args = array(
				'beginDay' => Time::toTime($begin_time),
				'endDay' => Time::toTime($end_time)
			);
		}
		$data = $this->requestApi('admin.statistics.getBonusRanking',$args);	
		View::share('list', $data['data']);
		return $this->display();
	}

	//卖家业绩查看
	public function sellerperformance() {
		$args = array();
		$seller = trim(Input::get('seller'));
		$begin_time = Input::get('beginTime');
		$end_time = Input::get('endTime');
		if ($begin_time != '' && $end_time != '') {
			$args = array(
				'beginDay' => Time::toTime($begin_time),
				'endDay' => Time::toTime($end_time)
			);
		}
		if ($seller != '') {
			$args['seller'] = $seller;
		}
		$data = $this->requestApi('admin.statistics.getSellerAchievement',$args);
		View::share('data', $data['data']);
		return $this->display();
	}

	//员工业绩
	public function staffperformance() {
		$args = array();
		$staff = trim(Input::get('staff')); //id,姓名,手机号
		$begin_time = Input::get('beginTime');
		$end_time = Input::get('endTime');
		if ($begin_time != '' && $end_time != '')
			$args = [
				'beginDay' => Time::toTime($begin_time),
				'endDay' => Time::toTime($end_time)
			];
		if ($staff != '')
			$args['staff'] = $staff;
		$data = $this->requestApi('admin.statistics.getSellerStaffAchievement',$args);
		View::share('data', $data['data']);
		return $this->display();
	}
	
}
