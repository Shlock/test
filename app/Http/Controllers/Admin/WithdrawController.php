<?php 
namespace YiZan\Http\Controllers\Admin; 

use YiZan\Http\Requests\Admin\WithdrawPostRequest;
use View, Input, Form; 

/**
 * 服务人员提现
 */
class WithdrawController extends AuthController {
	/**
	 * 服务人员提现列表
	*/
	public function index() { 
		$post = Input::all();

		!empty($post['sellerName'])     ?  $args['sellerName']    = strval($post['sellerName'])  : null;
		!empty($post['sellerMobile']) ?  $args['sellerMobile'] = strval($post['sellerMobile']) 	 : null;
		!empty($post['beginTime'])  ?  $args['beginTime']  = intval($post['beginTime']) 		 : null;
		!empty($post['endTime'])  ?  $args['endTime']   = strval($post['endTime']) 				 : null; 
		!empty($post['status'])  ?  $args['status']   = strval($post['status']) 				 : null; 
		!empty($post['page'])   ?  $args['page']     = intval($post['page'])   					 : $args['page'] = 1; 

		$list = $this->requestApi('seller.withdraw.lists',$args);
		View::share('list', $list);
		return $this->display();
	}	
	/**
	 * 更新协议资料
	*/
	public function edit() {
		$data = $this->requestApi('seller.withdraw.dispose', Input::all());
		if( $data['code'] == 0 ) {
			return $this->success($data['msg']);
		}
		else {
			return $this->error($data['msg']);
		}
	}
}
