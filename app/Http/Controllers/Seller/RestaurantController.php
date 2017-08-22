<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Utils\Time;
use View, Input, Lang, Response, Redirect;
/**
 * 餐厅管理
 */
class RestaurantController extends AuthController {
	/**
	 * 餐厅信息列表
	 */
	public function index() {
		$args = Input::all();;
		$result = $this->requestApi('restaurant.lists',$args);
		if($result['code'] == 0)
			View::share('list',$result['data']['list']);
		return $this->display();
	}

	/**
	 * 餐厅菜单
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
	 * 添加餐厅
	 * @return [type] [description]
	 */
	public function create() {
		//获取时分秒
        $time = Time::getHouerMinuteSec(true, true, false);
        View::share('time', $time);

		$data['seller'] = $this->seller;

		View::share('args',$args);
		View::share('data',$data);
		View::share('title','添加餐厅');
		return $this->display("edit");
	}

	/**
	 * 编辑餐厅
	 * @return [type] [description]
	 */
	public function edit() {
		$args = Input::all();
		$result = $this->requestApi('restaurant.lookat',$args);
		if($result['code']==0){
			$result['data']['password'] = null;
			$result['data']['beginTimeHour'] = explode(':', $result['data']['beginTime'])[0];
			$result['data']['beginTimeMinute'] = explode(':', $result['data']['beginTime'])[1];
			$result['data']['endTimeHour'] = explode(':', $result['data']['endTime'])[0];
			$result['data']['endTimeMinute'] = explode(':', $result['data']['endTime'])[1];
			View::share('data',$result['data']);		 	
		} 

		//获取时分秒
        $time = Time::getHouerMinuteSec(true, true, false);
        View::share('time', $time);

        View::share('args',$args);
		View::share('title','编辑餐厅');
		return $this->display();
	}

	/**
	 * 保存餐厅信息
	 */
	public function save() {
		$args = Input::all();
		$args['source'] = 1; //服务站添加
		$args['beginTime'] = $args['beginTimeHour'].':'.$args['beginTimeMinute'];
		$args['endTime'] = $args['endTimeHour'].':'.$args['endTimeMinute'];
		
		$result = $this->requestApi('restaurant.add',$args);
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

	/**
	 * 上下架
	 * @return [type] [description]
	 */
	public function goodsUpDown() {
		$args = Input::all();
		$result = $this->requestApi('goods.updown',$args);
		return Response::json($result);
	}

	/**
	 * 创建菜品
	 */
	public function createGoods() {
		$args = Input::all();
		if($args['restaurantId'] < 1)
			Redirect::to( u('Restaurant/index') )->send();

		//获取餐厅
		$restaurant = $this->requestApi('restaurant.lookat',['id'=>$args['restaurantId']]);
		$data['more']['restaurant'] = $restaurant['data'];

		//获取分类
		$goodstype = $this->requestApi('GoodsType.lists');
		$data['more']['goodstype'] = $goodstype['data']['list'];

		View::share('args',$args);
		View::share('data',$data);
		View::share('title','添加美食');
		return $this->display('editgoods');
	}

	/**
	 * 修改菜品
	 */
	public function editGoods() {
		$args = Input::all();
		if($args['restaurantId'] < 1)
			Redirect::to( u('Restaurant/index') )->send();
		//获取菜品信息
		$data = $this->requestApi('goods.get',$args);

		//获取餐厅
		$restaurant = $this->requestApi('restaurant.lookat',['id'=>$args['restaurantId']]);
		$data['more']['restaurant'] = $restaurant['data'];

		//获取分类
		$goodstype = $this->requestApi('goodstype.lists');
		$data['more']['goodstype'] = $goodstype['data']['list'];

		// dd($data);
		View::share('args',$args);
		View::share('data',$data);
		View::share('title','编辑美食');
		return $this->display();
	}

	/**
	 * 保存菜品
	 */
	public function saveGoods() {
		$args = Input::all();
		$result = $this->requestApi('goods.save',$args);
		return Response::json($result);
	}

	/**
	 * 删除菜品
	 */
	public function destroyGoods() {
		$args = Input::all();
		$result = $this->requestApi('goods.delete',$args);
		return Response::json($result);
	}
}