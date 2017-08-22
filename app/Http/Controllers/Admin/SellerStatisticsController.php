<?php 
namespace YiZan\Http\Controllers\Admin; 

//使用的命名空间
use Input,View;

/**
 * 服务人员统计
 */
class SellerStatisticsController extends AuthController {
	

	public function index() {
		$args = array();
		$begin_time = Input::get('beginTime');
		$end_time = Input::get('endTime');
		if ($begin_time != '' && $end_time != '') {
			$args = array(
				'beginTime' => $begin_time,
				'endTime' => $end_time
			);
		}
		$data = $this->requestApi('seller.sellercount.total',$args);
		if($data['code']==0)
			View::share('data', $data['data']);
		return $this->display();
	}

	
}
