<?php 
namespace YiZan\Http\Controllers\Admin; 

use Input,View,Lang,Response;
/**
 * 支付配置
 */
class PaymentController extends AuthController { 
	/**
	 * 支付方式列表
	 */
	public function index() { 
		//支付列表接口
		$result = $this->requestApi('payment.lists'); 
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']); 
		}
		return $this->display();
	} 
	public function edit() {
		$args = Input::all(); 
		$result = $this->requestApi('payment.update',$args); 
		View::share('data', $result['data']); 
		return $this->display();
	} 
	/**
	 * 更新支付方式
	 */
	public function update() {
		$data = Input::all(); 
		$args = array(); 
		!empty($data['code'])   ? $args['code']   = trim($data['code'])     : null;
		!empty($data['config']) ? $args['config'] = $data['config']		    : null;
		!empty($data['status']) ? $args['status'] = intval($data['status']) : null; 
		$result = $this->requestApi('payment.update',$args);  
		/*返回处理*/ 
		if($result['code']==0){
			return $this->success($result['msg']?$result['msg']:$result['msg'] = Lang::get('admin.code.98003'), u('Payment/index'), $result['data']);
		}else{ 
			return $this->error($result['msg']?$daresultta['msg']:$result['msg'] = Lang::get('admin.code.98004'), u('Payment/index'));
		}  
	}
	public function updateStatus() {
	    $post = Input::all();
	    $args = [
	        'status' => $post['val'],
	        'code' => $post['code']
	    ];
	    $result = $this->requestApi('Payment.updateStatus',$args);
	    return Response::json($result);
	}
}
