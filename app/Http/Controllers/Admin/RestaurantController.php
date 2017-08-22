<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Redirect, Response;

/**
 * 资源上传
 */
class RestaurantController extends AuthController {
	/**
	 * 餐厅信息列表
	 */
	public function index() {
		$args = Input::all();
		$result = $this->requestApi('restaurant.lists',$args);
		if($result['code'] == 0)
			View::share('list',$result['data']['list']);
		return $this->display();
	}

	/**
	 * 创建餐厅
	 */
	public function carte() {
		$args = Input::all();
		if($args['restaurantId'] < 1){
			Redirect::to(u('Restaurant/index'))->send();
		}

		$result = $this->requestApi('goods.goodslist',$args);
		if($result['code'] == 0){
			View::share('list',$result['data']['list']);
		}

		$restaurant = $this->requestApi('restaurant.lookat',['id'=>$args['restaurantId']]);
		if($restaurant['code'] == 0) {
			View::share('restaurant',$restaurant['data']);
		}
		return $this->display();
	}

	/**
	 * 更改菜品的参与服务
	 */
	public function joinService() {
		$args = Input::all();
		$result = $this->requestApi('goods.joinService', $args);
		return Response::json($result);
	}

	/**
	 * 删除餐厅
	 */
	public function destroy() {
		$args = Input::all();
		$result = $this->requestApi('restaurant.delete',$args);
		return Response::json($result);
	}
}
