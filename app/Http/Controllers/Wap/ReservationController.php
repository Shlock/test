<?php 
namespace YiZan\Http\Controllers\Wap;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page,Redirect,Response;
/**
 * 预约
 */
class ReservationController extends BaseController {

	//
	public function __construct() {
		parent::__construct();
		View::share('nav','index');
	} 
	
	/**
	 * 预约
	 */
	public function index(){ 
		//分类列表
		$cate_data = $this->requestApi('goods.goodCateList');  
		if($cate_data['code'] == 0) {
			View::share('cate_data', $cate_data['data']);
		}

		$reservation_data = $this->getReservationData();
		//如果有分类编号，则保存分类编号信息
		if ((int)Input::get('cateId') > 0) {
			$reservation_data['cateid'] = (int)Input::get('cateId');
			$this->saveReservationData($reservation_data);
		}

		Session::put('reservation_callback_url',u('Reservation/index'));
		View::share('reservation_data',$reservation_data); 

		View::share('nav_back_url', u('Index/index'));
		return $this->display();
	}

	/**
	 * 缓存预约信息
	 */
	public function info(){
		$option = Input::all(); 
		$reservation_data = $this->getReservationData();
		if(isset($option['address']) && isset($option['mapPoint'])){
			$address = []; 
			$mapPoint_str = $option['mapPoint'];
			$mpstr_arr = explode(',', $mapPoint_str);
			if(count($mpstr_arr) == 2){
				$mp_arr['x'] = $mpstr_arr[0];
				$mp_arr['y'] = $mpstr_arr[1];
				$address['mapPoint'] = $mp_arr;
			} 
			$address['mapPointStr'] = $mapPoint_str;
			$address['address'] 	= $option['address'];
			$reservation_data['address'] = $address;
		}
		if(isset($option['cateid'])){
			$reservation_data['cateid'] = $option['cateid'];
		}
		if(isset($option['tel'])){
			$reservation_data['tel'] = $option['tel'];
		}
		if (isset($option['timelen'])) {
			$reservation_data['timelen'] = (int)$option['timelen'];
		}
		if(isset($option['date'])){
			$reservation_data['date'] = $option['date'];
		}
		if(isset($option['goods'])){
			$reservation_data['goods'] = $option['goods'];
		}
		if(isset($option['staff'])){
			$reservation_data['staff'] = $option['staff'];
		}

		$this->saveReservationData($reservation_data);
		return $this->success();
	}

	/**
	 * 检测分配订单服务人员
	 */
	public function checkOrder(){
		//获取服务人员
		$reservation_data = $this->getReservationData();
        $args['goodsId'] = (int)Input::get('goodsId');
        $args['staffId'] = (int)Input::get('staffId');
        $args['duration'] = $reservation_data['timelen'] * SERVICE_TIME_LEN;
        $args['appointTime'] = urldecode($reservation_data['date']);
        $args['mapPoint'] = $reservation_data['address']['mapPointStr'];
        
		$staff_info = $this->requestApi('order.check', $args);
		return Response::json($staff_info);
	}

	/**
	 * 确认订单信息
	 */
	public function orderInfo(){
		if ($this->userId < 1) {
			return Redirect::to('User/login');
		}

		$goods_info = $this->requestApi('goods.detail', array('goodsId'=>(int)Input::get('goodsId')));
		if($goods_info['code'] > 0 ){
			return $this->error($goods_info['msg'], u('Index/index'));
		}

		//设置服务信息
		View::share('goods_info',$goods_info['data']);

		$reservation_data = $this->getReservationData();
        
        //获取服务人员
        $args['goodsId'] = (int)Input::get('goodsId');
        $args['staffId'] = (int)Input::get('staffId');
        $args['duration'] = $reservation_data['timelen'] * SERVICE_TIME_LEN;
        $args['appointTime'] = urldecode($reservation_data['date']);
        $args['mapPoint'] = $reservation_data['address']['mapPointStr'];
		$staff_info = $this->requestApi('order.check', $args);
		if ($staff_info['code'] == 0) {
			View::share('staff_info',$staff_info['data']); 
		} else {
			return $this->error($staff_info['msg']);
		}

		//设置预约信息
		View::share('reservation_data',$reservation_data); 
		$option_promotion = array(
			'status' => '0',
			'sellerId' => $goods_info['data']['sellerId'],
		);
		//设置优惠券信息
		$promotion_data = $this->requestApi('user.promotion.lists',array('goodsId'=>(int)Input::get('goodsId')));
		if ($promotion_data['code'] == 0) {
			View::share('promotion_data',$promotion_data['data']); 
		}
		View::share('user',$this->user);
		return $this->display();
	}

}
