<?php 
namespace YiZan\Http\Controllers\Wap;
use YiZan\Models\Goods;

use View, Input, Lang, Route, Page, Redirect, Response;
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

		$adv_result = $this->requestApi('config.seller');

		View::share('adv', $adv_result['data']);
		//获取商家详情
		$seller_result = $this->requestApi('seller.detail', $option);
 
		View::share('seller', $seller_result['data']);

		if($option['type'] == 2){
			$cate_result = $this->requestApi('service.lists', $option); 
			$model = 'serviceindex';
		} else { 
			$cate_result = $this->requestApi('goods.lists', $option);
			$model = 'goodsindex';
		} 
		// if(empty($cate_result['data']) && $option['type'] == 1){
		// 	return Redirect::to(u('Goods/index',['type'=>2,'id'=>$option['id']]));
		// 	exit;
		// }
		View::share('cate', $cate_result['data']);
		$result_cart = $this->requestApi('shopping.lists');  
		$cart['totalPrice'] = 0;
		$cart['totalAmount'] = 0; 
		foreach ($result_cart['data'] as $key1 => $value1) {
			foreach ($value1['goods'] as $key2 => $value2) {
				$cart['totalAmount'] += $value2['num'];
				$cart['totalPrice'] += $value2['price'] * $value2['num'];
			}
			if($value1['id'] == $option['id']){
				$cart['data'] = $value1;
			}
		}
		View::share('cart', $cart);
		$article_result = $this->requestApi('article.lists', ['sellerId'=>$option['id']]); 
		View::share('articles', $article_result['data']);
		// print_r($article_result['data']);
		$urltype = Input::get('urltype');
		if (empty($urltype)) {
			$nav_back_url = u('Seller/detail',['id'=>$option['id']]);
			View::share('nav_back_url', $nav_back_url);
		}
		
		return $this->display($model);
	} 

	public function sellerdetail(){ 
		$option = Input::all(); 
		//获取商家详情
		$seller_result = $this->requestApi('seller.detail', $option); 
		View::share('seller', $seller_result['data']);
		//获取公告
		$article_result = $this->requestApi('article.lists', ['sellerId'=>$option['id']]); 
		View::share('articles', $article_result['data']); 
		return $this->display();
	} 

	/**
	 * 搜索服务 
	 */
	public function search(){
		//热门标签
		$hot_tags = $this->requestApi('goods.tag.gethottags');
		if ($hot_tags['code'] == 0) {
			View::share('hot_tags',$hot_tags['data']);
		}
		$keywords = Input::get('keywords');
		$searchs = array();
		if (Session::get('goods_searchs')) {
			$searchs = Session::get('goods_searchs');
		}
		if (!empty($keywords) && !in_array($keywords, $searchs)) {
			array_push($searchs, $keywords);
			Session::set('goods_searchs', $searchs);
			Session::save();
		}
		$history_search = Session::get('goods_searchs');
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
		Session::set('goods_searchs', NULL);
		Session::save();
	}

	/**
	 * 服务列表 汽车、其他
	 */
	public function goodsList() {
		$args = Input::all();
		$data['id'] = $args['arg'];
		$data['page'] = $args['page'] > 0 ? $args['page'] : 1;

		//获取服务详细
		$result = $this->requestApi('service.lists',$data);
		if($result['code'] == 0){
			View::share('lists', $result['data']);
		}

		View::share('args', $args);
		if(Input::ajax()){
			return $this->display('goodslist_item');
		}else{
			return $this->display();
		}
		
	}

	/**
	 * 服务明细
	 */
	public function detail(){
		$option = Input::all();
		//获取商品/服务详情数据
		$goods_result = $this->requestApi('goods.detail',$option);
		View::share('data', $goods_result['data']);
		//获取购物车数据
		$result_cart = $this->requestApi('shopping.lists'); 
		$cart['totalPrice'] = 0;
		$cart['totalAmount'] = 0; 
		foreach ($result_cart['data'] as $key1 => $value1) {
			foreach ($value1['goods'] as $key2 => $value2) {
				$cart['totalAmount'] += $value2['num'];
				$cart['totalPrice'] += $value2['price'] * $value2['num'];
			}
			if($value1['id'] == $goods_result['data']['sellerId']){ 
				foreach ($value1['goods'] as $key3 => $value3) { 
					if($value3['goodsId'] == $option['goodsId']) {
						$cart['data'] = $value3;
					}
				}
			}
		}
        View::share('seller', $goods_result['data']['seller']);
		View::share('cart', $cart); 
		if($goods_result['data']['type'] == Goods::SELLER_SERVICE) {
			return $this->display('servicedetail');
		} else {
			return $this->display('goodsdetail');
		}
	}

	/**
	 * 服务简介
	 */
	public function brief(){
		$id = (int)Input::get('id'); 
		$goods_data = $this->requestApi('goods.detail',array('goodsId'=>$id));
		if(empty($goods_data['data'])){
			return Redirect::to('Goods/index');
		} else {
			View::share('top_title',$goods_data['data']['name']);
			View::share('goods_data',$goods_data['data']); 
			return $this->display();
		}
	} 
	
	/**
	 * 商品服务简介
	 */
	public function appbrief()
    {
		$result = $this->requestApi('goods.detail', Input::all());
		
		if($result['code'] == 0)
        {
            echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><style>body{padding:0; margin:0;} img{width:100%;}</style>" . $result['data']["brief"];
        }
	    
        exit;
	}

	/**
	 * 服务时间
	 */
	public function appointday(){
		$args = Input::all();
		$args['duration'] = isset($args['timelen']) ? $args['timelen'] * SERVICE_TIME_LEN : 0;
		$result = $this->requestApi('goods.appointday', $args);
		if ($result['code'] > 0) {
			return $this->error($result['msg']);
		}
		$html = Response::view($this->getDisplayPath('goods', 'date_frame'), ['reservation_date' => $result['data']])->getContent();

		
		return $this->success('', '', $html);
	}

	/**
	 * 服务评价
	 */
	public function discuss() {
		$args = Input::all();
		$result = $this->requestApi('rate.service.lists',$args);

		if($result['code'] == 0)
			View::share('list',$result['data']);

		View::share('args', $args);
		if(Input::ajax()){
			return $this->display('discuss_item');
		}else{
			return $this->display();
		}
		
	}

	/**
	 * 获取购物车
	 */
	public function getCart(){
		if(!Session::get('user')){
			$cart = ['code'=>-1,'data'=>''];
		} else {
			$cart = $this->requestApi('shopping.lists');
		}
		return Response::json($cart);
	}

	/**
	 * 删除购物车
	 */
	public function cartDelete(){
		$result_cart = $this->requestApi('shopping.delete', Input::all());  
		if($result_cart['code'] > 0){
			return $this->error('删除失败');
		} elseif((int)Input::get('id') == 0) {
			return $this->success('购物车已清空');
		} else {
            return $this->success('已删除');
        }
	}

	/**
	 * 保存商品至购物车
	 */
	public function saveCart(){
		if(!Session::get('user')){
			$result_cart = ['code'=>-1,'data'=>''];
		} else {
			$result_cart = $this->requestApi('shopping.save', Input::all()); 
			$list = $result_cart['data'];
			unset($result_cart['data']);
			$result_cart['data']['totalPrice'] = 0;
			$result_cart['data']['totalAmount'] = 0;  
			$result_cart['data']['list'] = $list;
			foreach ($list as $key1 => $value1) { 
				foreach ($value1['goods'] as $key2 => $value2) {
					$result_cart['data']['totalAmount'] += $value2['num'];
					$result_cart['data']['totalPrice'] += $value2['price'] * $value2['num'];
				} 
			}  
		}
		return Response::json($result_cart);
	}

	/**
	 * 收藏
	 */
	public function collect(){
		$args = Input::all();
		$args['type'] = 1;
		$result_collect = $this->requestApi('collect.create',$args); 
	}

    /**
     * 评论
     */
    public function comment() {
        $args = [
            'sellerId' => (int)Input::get('id'),
            'type' => (int)Input::get('type'),
            'page' => max((int)Input::get('page'),1)
        ];
        $count = $this->requestApi('rate.order.statistics',['sellerId' => $args['sellerId']]);
        $list = $this->requestApi('rate.order.lists',$args);
        $article_result = $this->requestApi('article.lists', ['sellerId'=>$args['sellerId']]);
        View::share('articles', $article_result['data']);
        View::share('count', $count['data']);
        View::share('list', $list['data']);
        View::share('args', $args);
        if (Input::ajax()) {
            return $this->display('comment_item');
        } else {
            return $this->display();
        }

    }
}
