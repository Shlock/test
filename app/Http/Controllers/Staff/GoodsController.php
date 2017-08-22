<?php 
namespace YiZan\Http\Controllers\Staff;

use View, Input, Lang, Route, Page,Redirect;
/**
 * 服务
 */
class GoodsController extends BaseController {

	//
	public function __construct() {
		parent::__construct();
		View::share('nav','index');
	}  

	/**
	 * 服务列表
	 */
	public function index(){ 
		$option = Input::all(); 
		$args = $synthesisOption = $popularityOption = $priceOption = $callbackOption = $option;
		//echo Session::get('reservation_callback_url');
		$reservation_data = Session::get('reservation_data');  
		//var_dump($reservation_data);
		View::share('reservation_data',$reservation_data);
		//如果是从预约界面过来则需提取预约参数
		if((int)Input::get('type') == 1){
			//$reservation_data = Session::get('reservation_data');  
			if(isset($reservation_data['cateid']) && !empty($reservation_data['cateid'])){
				$args['categoryId'] = $reservation_data['cateid'];
			}
			if(isset($reservation_data['date']) && !empty($reservation_data['date'])){
				$args['appointTime'] = $reservation_data['date'];
			}
			if(isset($reservation_data['address']['mapPointStr']) && !empty($reservation_data['address']['mapPointStr'])){
				$args['mapPoint'] = $reservation_data['address']['mapPointStr'];
			}
		}  
		$cateid = (int)Input::get('categoryId') ? (int)Input::get('categoryId') : 0;
		$cate_data = $this->requestApi('goods.goodCateList2',array('cateId'=>$cateid));  
		//var_dump($cate_data['data']);
		if($cate_data['code'] == 0) {
			View::share('cate_data',$cate_data['data']);
		}
		if (!(int)Input::get('cateId')) {
			if (!empty($cate_data['data'])) {
				$args['categoryId'] = $cate_data['data'][0]['id'];
			}
		} else {
			$args['categoryId'] = (int)Input::get('cateId');
		}
		//var_dump($args);
		$goods_data = $this->requestApi('goods.lists',$args);
		// var_dump($goods_data);
		if($goods_data['code'] == 0) {
			View::share('data',$goods_data['data']);
		}
		$callbackOption['page'] = 1;
		Session::set('reservation_callback_url',u('Goods/index',$callbackOption));
		if (Input::ajax()) {
			return $this->display('goods_item');
		} else {
			if(empty($option['order']) || (int)$option['order'] < 1){
				$option['order'] = 1;
				$option['sort'] = 0;
			}
			//排序方式
			$synthesisOption['order'] = 1;
			$popularityOption['order'] = 2;
			$priceOption['order'] = 3;
			if(isset($option['order']) && $option['order'] > 0){ 
				//
				if($option['order'] == 1 && $option['sort'] == 1){
					$synthesisOption['sort'] = 0;  
				} else {
					$synthesisOption['sort'] = 1;
				}
				
				if($option['order'] == 2 && $option['sort'] == 1){
					$popularityOption['sort'] = 0;  
				} else {
					$popularityOption['sort'] = 1;
				}

				if($option['order'] == 3 && $option['sort'] == 1){
					$priceOption['sort'] = 0;  
				} else {
					$priceOption['sort'] = 1;
				}	 
			} else {
				$option['order'] = 1;
				$synthesisOption['sort'] = $popularityOption['sort'] = $priceOption['sort'] = 0;
			}
			View::share('synthesisOption',$synthesisOption); 
			View::share('popularityOption',$popularityOption);
			View::share('priceOption',$priceOption);   
			View::share('option',$option);  
        	View::share('show_top',"showgoods");
			return $this->display();
		}
	} 

	/**
	 * 搜索服务 
	 */
	public function search(){
		$option['keywords'] = Input::get('keywords'); 
		$goods_data = $this->requestApi('goods.lists',$option);
		if($goods_data['code'] == 0)
			View::share('data',$goods_data['data']);
		if (Input::ajax()) {
			return $this->display('goods_item');
		} else {
			View::share('option',$option);
			return $this->display();
		}
	}

	/**
	 * 服务明细
	 */
	public function detail(){
		$option = Input::all(); 
		$staffId = $option['staffId'] = !isset($option['staffId']) ? 0 : (int)$option['staffId'];
		$goods_data = $this->requestApi('goods.detail',$option);
		$staff_data = $this->requestApi('staff.detail',array('staffId'=>$staffId));
		$add_data = $this->requestApi('user.address.lists');
		if(empty($goods_data['data'])){
			return Redirect::to('Goods/index');
		} else {
			View::share('goods_data',$goods_data['data']); 
			View::share('staff_data',$staff_data['data']); 
			View::share('add_data',$add_data['data']); 
			$reservation_date = $this->requestApi('staff.appointday',$option);
			$reservation_data = Session::get('reservation_data');
			Session::set('reservation_callback_url',u('Goods/detail',array('goodsId'=>$goods_data['data']['id'])));
			if($reservation_date['code'] == 0)
				View::share('reservation_date',$reservation_date['data']);
			View::share('reservation_data',$reservation_data);
			View::share('userId',$this->userId);
			View::share('staffId',$option['staffId']);
			// dd($reservation_date);
			return $this->display();
		}
	}

	/**
	 * 闪存订单数据
	 */
	public function saveOrderData() {
		$data = Input::get('data');
		$address = json_decode($data['address'],true);
		$mpx = strval($address['mapPoint']['x']);
		$mpy = strval($address['mapPoint']['y']);
		$address['mapPointStr'] = $mpx.','.$mpy;
		unset($address['id']);
		unset($address['isDefault']);

		$date = strtotime( \YiZan\Utils\Time::toDate(\YiZan\Utils\Time::getTime(), "Y")."-".$data['date'] );
		//预约时间加上一天小于当前时间 则跨年
		if( ( $date + 3600 * 24 ) < UTC_TIME ) {
			$year = \YiZan\Utils\Time::toDate(\YiZan\Utils\Time::getTime(), "Y") + 1;
		}else {
			$year = \YiZan\Utils\Time::toDate(\YiZan\Utils\Time::getTime(), "Y");
		}
		$date = $year."-".$data['date'];

		$reservation_data = Session::get('reservation_data');
		$reservation_data['address'] = $address;
		$reservation_data['date'] = $date;
		$reservation_data['timelen'] = $data['timelen'];
		$reservation_data['isToStore'] = intval($data['isToStore']);
		$reservation_data['sellerId'] = $data['sellerId'];
		$reservation_data['staff'] = $data['staffId'];

		Session::set('reservation_data',$reservation_data);
		Session::save();
		// print_r(Session::get('reservation_data'));die;
		echo 1;
	}

}
