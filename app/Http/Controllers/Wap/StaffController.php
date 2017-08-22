<?php 
namespace YiZan\Http\Controllers\Wap;

use View, Input, Lang, Route, Page,Redirect;
/**
 * 服务人员
 */
class StaffController extends BaseController {

	//
	public function __construct() {
		parent::__construct();
		View::share('nav','index');
	}  

	/**
	 * 服务人员列表
	 */
	public function index(){
		$args = $page_args = $option = Input::all();
		View::share('option', $option);
		//如果是从预约界面过来则需提取预约参数 
		$type = (int)Input::get('type');
		if($type == 1){
			$reservation_data = $this->getReservationData();
			$args['appointTime'] = $reservation_data['date'];
			$args['appointMapPoint'] = $reservation_data['address']['mapPointStr'];
			$args['duration'] = $reservation_data['timelen'] * 3600;
			View::share('reservation_data',$reservation_data);
		}

		$goodsId = (int)Input::get('goodsId');
		View::share('goodsId', $goodsId);
		if ($goodsId > 0) {
			View::share('nav_back_url', u('Goods/detail', ['goodsId' => $goodsId]));
		}

		//默认距离正序
		if(!isset($args['order']) || (int)$args['order'] < 1){
			$page_args['order'] = $args['order'] = 1;
			$page_args['sort']	= $args['sort'] = 1;
		}
        
		$staff_data = $this->requestApi('staff.lists',$args);
		if($staff_data['code'] == 0 ) {
			View::share('data',$staff_data['data']);
		}

		if (Input::ajax()) {
			return $this->display('staff_item');
		} else {
			View::share('show_top',"showstaff");
			unset($page_args['page']);

			$new_sort = $page_args['sort'] == 1 ? 0 : 1;

			//距离
			$order_args1 				= $page_args;
			$order_args1['order']		= 1;
			$order_args1['selected'] 	= $page_args['order'] == $order_args1['order'];
			//如果不为选中，默认从近到远
			$order_args1['sort']	 	= $order_args1['selected'] ? $new_sort : 1;
			View::share('order_args1',$order_args1);
			
			//人气
			$order_args2 				= $page_args;
			$order_args2['order']		= 2;
			$order_args2['selected'] 	= $page_args['order'] == $order_args2['order'];
			//如果不为选中，默认倒序
			$order_args2['sort']	 	= $order_args2['selected'] ? $new_sort : 1;
			View::share('order_args2',$order_args2);

			//好评
			$order_args3 				= $page_args;
			$order_args3['order']		= 3;
			$order_args3['selected'] 	= $page_args['order'] == $order_args3['order'];
			//如果不为选中，默认倒序
			$order_args3['sort']	 	= $order_args3['selected'] ? $new_sort : 1;
			View::share('order_args3',$order_args3);

			return $this->display();
		}
	} 

	/**
	 * 搜索服务人员 
	 */
	public function search(){
       	$keywords = Input::get('keywords');
		$searchs = array();
		if (Session::get('searchs')) {
			$searchs = Session::get('searchs');
		}
		if (!empty($keywords) && !in_array($keywords, $searchs)) {
			array_push($searchs, $keywords);
			Session::set('searchs', $searchs);
			Session::save();
		}
		$history_search = Session::get('searchs');
		View::share('data',$history_search); 
		if (Input::get('type')) {
			//return true;
		} else {
			return $this->display();
		}
	}

	/**
	 * 清除搜索历史记录
	 */
	public function clearsearch(){
		Session::set('searchs', NULL);
		Session::save();
	}
	/**
	 * 服务人员明细
	 */
	public function detail(){
		$option = Input::all();  
		$synthesisOption = $popularityOption = $priceOption = $option; 
		$staff_data = $this->requestApi('staff.detail',$option);
		//var_dump($staff_data);
		if(empty($staff_data['data'])){
			//return Redirect::to('staff/index');
		} else {
			$goods_data = $this->requestApi('goods.lists',$option);	 
			if($goods_data['code'] == 0)	   
				View::share('data',$goods_data['data']);
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
				//服务人员明细
				View::share('staff_data',$staff_data['data']); 
				//服务分类
				View::share('synthesisOption',$synthesisOption); 
				View::share('popularityOption',$popularityOption);
				View::share('priceOption',$priceOption);   
				View::share('option',$option); 
        		View::share('show_top',"showstaffd"); 
				return $this->display();
			}
		} 
	}
}
