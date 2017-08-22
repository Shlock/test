<?php 
namespace YiZan\Http\Controllers\Staff;
use Input, View, Redirect, Request,Time;
/**
 * 用户订单控制器
 */
class OrderController extends AuthController {
	protected $_config = ''; //基础配置信息

	public function __construct() {
		parent::__construct();
		View::share('nav','order');
		$this->_config = Session::get('site_config');
		View::share('config',$this->_config);
	}
	/**
	 * 订单列表页
	 */
	public function index() {
		$status = Input::all();
		$result = $this->requestApi('order.lists',$status);
		View::share('list', $result['data']);
		View::share('status', $status['status']);
		return $this->display();
	} 
	/**
	 * 完成订单
	 */
	public function complete() {			
		$args = Input::All();
		$result = $this->requestApi('order.complete',$args);
		echo json_encode($result);die;
	}

	/**
	 * 订单详情
	 */
	public function detail() {
		$id = (int)Input::get('id');
		$result = $this->requestApi('order.detail',array('id' => $id));
		View::share('data',$result['data']);
		return $this->display();	
	}
}