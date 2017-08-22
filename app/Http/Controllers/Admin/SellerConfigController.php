<?php 
namespace YiZan\Http\Controllers\Admin; 

use Input,View;
/**
 *服务人员配置参数
 */
class SellerConfigController extends AuthController {  
	public function index() { 
		return $this->display('SellerConfig','edit'); 
	} 
	/**
	 * 配置参数
	*/
	public function edit() { 
		$data = array();
		if(!empty($id)) $data = $this->requestApi('seller.get');
		View::share('list', $list);
		return $this->display(); 
	}	
	/**
	* 
	*   修改服务人员信息 
	*/
	public function update() {
		/*
		* 未操作
		*/
	} 
}
