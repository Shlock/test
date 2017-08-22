<?php 
namespace YiZan\Http\Controllers\Wap;

use View, Input, Redirect, Response;
/**
 * 外卖
 * 1: 即时送餐 
 * 2：预约午餐
 * 3：同时参加
 */
class TakeOutController extends UserAuthController {
	public function __construct() {
		parent::__construct();
		View::share('nav','index');
	}

	//预约午餐，即时送餐 餐厅列表
	public function index() {
		$args = Input::all();

		//默认显示预约午餐界面
		$args['type'] = isset($args['type']) ? $args['type'] : 2; 

		//获取餐厅
		$result = $this->requestApi('restaurant.lists',$args);

		if($result['code'] == 0)
			View::share('list', $result['data']);

		View::share('args', $args);
		if(!Input::ajax()){
			//获取餐厅配置信息
			$result_init = $this->requestApi('app.init',$args);
			if($result_init['code'] == 0)
				View::share('init', $result_init['data']);

			return $this->display();
		}else{
			return $this->display('item');
		}
		
	}

	//菜品
	public function goods() {
		$args = Input::all();
		if($args['restaurantId'] < 1 || !isset($args['type']))
			return Redirect::to('TakeOut/index',$args);

		//获取菜品列表
		$goods = $this->requestApi('restaurant.goods.lists',['id'=>$args['restaurantId'],'type'=>$args['type']]);
		//获取餐厅
		$restaurant = $this->requestApi('restaurant.get',$args);
		//获取餐厅配置信息
		$result_init = $this->requestApi('app.init',$args);
		//获取购物车信息
		$cart = $this->requestApi('shopping.getCart');

		View::share('data', $goods['data']);
		View::share('restaurant', $restaurant['data']);
		View::share('init', $result_init['data']);
		View::share('cart', $cart['data']);
		View::share('args', $args);
		return $this->display();
	}

	//菜品详细
	public function goodsdetail() {
		$args = Input::all();

		if(!Input::ajax()){
			//服务详细
			$detail = $this->requestApi('service.detail',$args);
			if($detail['code'] == 0)
				View::share('detail', $detail['data']);
		}

		//评价
		$discuss = $this->requestApi('rate.service.lists',$args);
		if($discuss['code'] == 0)
			View::share('discuss', $discuss['data']);

		View::share('args', $args);

		if(!Input::ajax()){
			return $this->display();
		}else{
			return $this->display('discuss_item');
		}
		
	}

	//购物车
	public function shoppingCart() {
		$post = Input::all();
		$data['goods'] = (array)json_decode($post['goodsInfo']);
		$data['goods']['num']  = $post['num'];
		$data['goods']['type'] = $post['type'];
		$result = $this->requestApi('shopping.cart',$data['goods']);
		return Response::json($result['data']);
	}

	//清空购物车
	public function clearShoppingCart() {
		$result = $this->requestApi('shopping.clearCart');
		return Response::json($result['data']);
	}
}
