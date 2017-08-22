<?php 
namespace YiZan\Http\Controllers\Admin; 

use YiZan\Http\Requests\Admin\WithdrawPostRequest;
use View, Input, Form, Response; 

/**
 * 申请协议
 */
class UpdateAgreEmentController extends AuthController {
	/**
	 * 获取协议
	*/
	public function index() { 
		$post = Input::all();  
		$list = $this->requestApi('seller.get');
		View::share('list', $list);
		return $this->display();
	}	
	/**
	 * 更新协议资料
	*/
	public function edit(WithdrawPostRequest $request) {
		$args = Input::all();   
		$data = $this->requestApi('seller.updateagreement',$args);
		return Response::json($data);
	}
}
