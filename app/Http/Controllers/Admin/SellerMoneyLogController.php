<?php 
namespace YiZan\Http\Controllers\Admin; 

use View, Input, Form; 

/**
 * 服务人员资金流水
 */
class SellerMoneyLogController extends AuthController {
	/**
	 * 服务人员资金流水 列表
	*/
	public function index() {
		$post = Input::all();  
		$args = array(); 		
		!empty($post['sellerName'])     ?  $args['sellerName']    = $post['sellerName']  : null;
		!empty($post['sellerMobile']) ?  $args['sellerMobile'] = $post['sellerMobile'] : null;
		!empty($post['beginTime'])  ?  $args['beginTime']  = $post['beginTime']		 : null;
		!empty($post['endTime'])  ?  $args['endTime']   = $post['endTime'] 		   : null; 
		!empty($post['page'])   ?  $args['page']     = intval($post['page'])   	: $args['page'] = 1; 
		$result = $this->requestApi('seller.moneylog.lists',$args);	 
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']); 
		}
		return $this->display();
	}	 
}
