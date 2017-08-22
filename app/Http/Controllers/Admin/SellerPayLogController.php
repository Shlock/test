<?php 
namespace YiZan\Http\Controllers\Admin; 

use Input,View;

/**
 * 商家支付日志
 */
class SellerPayLogController extends AuthController {  

	/**
	 * 商家日志列表
	 */
	public function index() { 
		$args = Input::all();
		$result = $this->requestApi('seller.paylog.lists',$args);  
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']); 
		}
		return $this->display();
	} 
}
