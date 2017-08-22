<?php 
namespace YiZan\Http\Controllers\Wap;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Response;
/**
 * 位置选取（小区）
 */
class LocationController extends UserAuthController {

	public function __construct() {
		parent::__construct();
		View::share('nav','index');
		View::share('is_show_top',false);
	}
	
	/**
	 * 首页信息 
	 */
	public function index() {
		$args = Input::all();
		$result = $this->requestApi('district.lookstaff',$args);
		View::share('list',$result['list']);
		View::share('searchName',$args['searchName']);
		View::share('goodsId',$args['goodsId']);
		
		Session::put('orderData',null);
		Session::put('orderData.districtId', $args['districtId']);
		Session::put('orderData.address', $args['searchName']);
		Session::put('orderData.goodsId', $args['goodsId']);
		Session::save();
		return $this->display();
	} 
	
	/**
	 * 搜索小区
	 */
	public function searchLocation() {
		$args = Input::all();
		// $args['mapPoint'] = "29.56301, 106.551557";	
		$args['sellerId'] = $this->sellerId;
		if($args['mapPoint']){
			$args['pageSize'] = 50;
		}
		$result = $this->requestApi('district.searchresponsible',$args);
		// dd($result);
		return Response::json($result['list']);
	}

	/**
	 * 获取服务人员时间
	 */
	public function appointday() {
		$args = Input::all();
		$result = $this->requestApi('staff.appointday',$args); 
		if($result['code'] == 0)
			return Response::json($result['data']);
	}

	/**
	 * 常用小区列表
	 */
	public function commonarea() {
		$args = Input::all();
		$result = $this->requestApi('user.commonarea.lists');
		View::share('goodsId',$args['goodsId']);
		View::share('data',$result['data']);
		return $this->display();
	}

	/**
	 * 删除常用小区
	 */
	public function delcommonarea() {
		$result = $this->requestApi('user.commonarea.delete');
		return Response::json($result['data']);
	}

	/**
	 * 添加常用小区
	 */
	public function addcommonarea() {
		$args = Input::all();
		if($args['districtId'] < 1) {
			exit;
		}
		$result = $this->requestApi('user.commonarea.add',$args);
		return Response::json($result['data']);
	}

}
