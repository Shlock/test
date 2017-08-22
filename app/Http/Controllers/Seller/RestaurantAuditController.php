<?php 
namespace YiZan\Http\Controllers\Seller;

use View, Input, Lang, Response;
/**
 * 餐厅审核
 */
class RestaurantAuditController extends AuthController {
	/**
	 * 餐厅审核列表
	 */
	public function index() {
		$args = Input::all();
		$result = $this->requestApi('restaurant.auditlists',$args);
		if($result['code']==0){
			View::share('list',$result['data']['list']);		 	
		}  
		View::share('args',$args);
		return $this->display();
	}

	/**
	 * 查看
	 */
	public function check() {
		$args = Input::all();
		$result = $this->requestApi('restaurant.lookat',$args);
		if($result['code']==0){
			View::share('data',$result['data']);		 	
		}  
		return $this->display();
	}

	/**
	 * 重新编辑
	 */
	public function edit() {
		$args = Input::all();
		$result = $this->requestApi('restaurant.lookat',$args);
		if($result['code']==0){
			$result['data']['password'] = null;
			View::share('data',$result['data']);		 	
		} 
		return $this->display();
	}

	/**
	 * 保存重新编辑的内容
	 */
	public function save() {
		$args = Input::all();
		$result = $this->requestApi('restaurant.save',$args);
		return Response::json($result);
	}

}