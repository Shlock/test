<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Form, Lang; 
/**
 * 物业公司审核人员
 */
class PropertyApplyController extends AuthController {	 

	/*
	 * 审核列表 
	 */
	public function index() {  
		$args = Input::all(); 
		if(empty($args['status'])){
			$args['status'] = 2;
		}
		$args['isCheck'] = $args['status'];
		$result = $this->requestApi('seller.propertylists',$args);  
		//print_r($result['data']['list']);
		if ($result['code'] == 0) {
			View::share('list', $result['data']['list']);
		} 
		View::share('status', $args['status']);
		return $this->display();  
	} 

	/**
	 * 审核明细
	 */
	public function detail(){ 
		$args = Input::all();
        $result = $this->requestApi('seller.get', $args);
        if ($seller['code'] == 0) {
            View::share('data', $result['data']);
        }
		return $this->display();
	}

	/**
	 * 处理审核
	 */
	public function dispose(){
		$args = Input::all();  
		$args['field'] = 'is_check'; 
		$result = $this->requestApi('seller.updatepropertystatus',$args); 
		if ($result['code'] == 0) {
			return $this->success($result['msg']);
		} 
		return $this->error($result['msg']);
	}
	 
}
