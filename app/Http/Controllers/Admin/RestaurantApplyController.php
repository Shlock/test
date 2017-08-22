<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Response;

/**
 * 餐厅审核
 */
class RestaurantApplyController extends AuthController {
	/**
	 * 餐厅申请列表
	 */
	public function index() {
		$args = Input::all();
		$result = $this->requestApi('restaurant.applylists',$args);
		if($result['code'] == 0)
			View::share('list',$result['data']['list']);
		return $this->display();
	}

	/**
	 * 餐厅审核查看
	 */
	public function edit() {
		$args = Input::all();
		$result = $this->requestApi('restaurant.lookat',$args);
		if($result['code'] == 0){
			View::share('data',$result['data']);
		}
		//查询所有的服务站
		if($result['source']!=1){
			$seller = $this->requestApi('seller.allseller');
			if($seller['code'] == 0){
				View::share('seller',$seller['data']);
			}
		}
		return $this->display();
	}

	/**
	 * 处理审核
	 */
	public function dispose() {
		$args = Input::all();
		$result = $this->requestApi('restaurant.dispose',$args);
		return Response::json($result);
	}

}
