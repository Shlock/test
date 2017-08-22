<?php 
namespace YiZan\Http\Controllers\Wap;

use View, Input, Lang, Route, Page,Redirect,Response;
/**
 * 服务人员
 */
class SellerController extends BaseController {

	//
	public function __construct() {
		parent::__construct();
		View::share('nav','index');
	}  

	/**
	 * 商家列表
	 */
	public function index(){
		$option = Input::all(); 

		//print_r($option);
		
		$args = $option;
		if ((int)$option['id'] < 1) {
			$args['id'] = 0;
			$args['type'] = (int)$option['type'];
		}
		if(empty($option['sort']) || (int)$option['sort'] < 1){
			$args['sort'] = 3;//排序方式，默认综合排序
		}

		if ($args['id'] > 0) {
			$seller_cate = $this->requestApi('seller.getcate',array('id'=>$args['id']));
			View::share('cate',$seller_cate['data']);
			$args['type'] = $seller_cate['data']['type'];
		}

		$defaultAddress = Session::get("defaultAddress");
		if ($defaultAddress) {
			$args['mapPoint'] = $defaultAddress['mapPointStr'];
		}
		
		//获取商家所有分类
		$seller_cates = $this->requestApi('seller.catelists', array('type'=>$args['type']));

		//print_r($seller_cates);
		if($seller_cates['code'] == 0) {
			View::share('seller_cates',$seller_cates['data']);
		}
		
		$seller_data = $this->requestApi('seller.lists',$args);
		if($seller_data['code'] == 0) {
			View::share('data',$seller_data['data']);
		}
		
		if (Input::ajax()) {
			return $this->display('seller_item');
		} else {
			View::share('args',$args); 
			return $this->display();
		}
	} 

	public function appindex(){ 
		$option = Input::all(); 
		$args = $option;
		if ((int)$option['id'] < 1) {
			$args['id'] = 0;
			$args['type'] = (int)$option['type'];
		}
		if(empty($option['sort']) || (int)$option['sort'] < 1){
			$args['sort'] = 0;//排序方式，默认综合排序
		}

		if ($args['id'] > 0) {
			$seller_cate = $this->requestApi('seller.getcate',array('id'=>$args['id']));
			View::share('cate',$seller_cate['data']);
			$args['type'] = $seller_cate['data']['type'];
		}
		
		//获取商家所有分类
		$seller_cates = $this->requestApi('seller.catelists', array('type'=>$args['type']));

		//print_r($seller_cates);
		if($seller_cates['code'] == 0) {
			View::share('seller_cates',$seller_cates['data']);
		}
		//need to do get mapPoint 经纬度
		//print_r($seller_cate);
		//var_dump($args);
		$seller_data = $this->requestApi('seller.lists',$args);
		//print_r($seller_data);
		if($seller_data['code'] == 0) {
			View::share('data',$seller_data['data']);
		}
		
		if (Input::ajax()) {
			return Response::json($seller_data);
		} else {
			View::share('args',$args); 
			return $this->display();
		}
	} 
	/**
	 * 搜索服务人员 
	 */
	public function search(){
		$keyword = Input::get('keyword');
		$searchs = array();
		if (Session::get('searchs')) {
			$searchs = Session::get('searchs');
		}
		if (!empty($keyword) && !in_array($keyword, $searchs)) {
			array_push($searchs, $keyword);
			Session::set('searchs', $searchs);
			Session::save();
		}
		$history_search = Session::get('searchs');
		//var_dump($history_search);
		View::share('history_search',$history_search); 
		$hot_data = $this->requestApi('seller.lists',['sort'=>1]);
		if($hot_data['code'] == 0)	  
			View::share('hot_data',$hot_data['data']); 

		$option = Input::all();
		$seller_data = $this->requestApi('seller.lists',$option);
		if($seller_data['code'] == 0)	  
			View::share('data',$seller_data['data']); 
		if (Input::get('keyword')) {
			return $this->display('searchresult');
		} else {
			View::share('option',$option); 
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
	 * 机构详情
	 */
	public function detail(){
		$option = Input::all();  
	
		$seller_data = $this->requestApi('seller.detail',$option);
		$seller_data = $seller_data['data'];
		//print_r($seller_data);
		if ($seller_data['code'] == 0) {
			View::share('seller_data',$seller_data);
		}
		// if($seller_data['countService'] > 0 && $seller_data['countGoods'] < 1){
		// 	return Redirect::to(u('Goods/index',['id'=>$seller_data['id'],'type'=>2]));
		// } else if($seller_data['countService'] < 1 && $seller_data['countGoods'] > 0) {
		// 	return Redirect::to(u('Goods/index',['id'=>$seller_data['id'],'type'=>1]));
		// }

		//获取公告
		$article_result = $this->requestApi('article.lists', ['sellerId'=>$option['id']]);
		View::share('articles', $article_result['data']); 
		//print_r($article_result);
		View::share('option',$option); 
		View::share('collect','yes'); 				
		return $this->display();

	}

    public function reg() {
        View::share('title', '- 商家开户');
    	$cate_result = $this->requestApi('seller.catelists'); 
    	View::share('cate', $cate_result['data']);  
    	View::share('option', Session::get('reg_info')); 
    	$cate_option = Session::get('cate_info');
    	View::share('cate_option', $cate_option); 
    	$choose_cate = '';
    	$i = 0;
    	foreach ($cate_result['data'] as $key => $value) { 
    		foreach ($cate_option as $id) { 
    			if($value['id'] == $id){
    				$choose_cate .= ' '.$value['name'];
    				$i++;
    			}
    		} 
    		if ($value['childs']) {
    			foreach ($value['childs'] as $k => $v) {
	    			if (in_array($v['id'], $cate_option)) {
		    			$choose_cate .= ' '.$v['name'];
	    				$i++;
		    		}
	    		}
    		}
			if($i >= 2){
				$choose_cate .= "等";
				break;
			}
    	}  
    	View::share('cate_str', $choose_cate); 
    	View::share('login_user', Session::get('user'));
        return $this->display();
    }

	/**
	 * 全部分类
	 */
	public function cates(){
		$cates = $this->requestApi('seller.catelists');
		if ($cates['code'] == 0) {
			View::share('cates',$cates['data']);
		}

		return $this->display();

	}

    public function cate(){ 
    	$cate_result = $this->requestApi('seller.catelists');  
    	View::share('title', '- 经营范围');
    	View::share('cate', $cate_result['data']);
    	View::share('current', Session::get('cate_info'));
    	return $this->display();
    }

    public function saveCate(){
    	$args = Input::all();
    	Session::put('cate_info', $args['cateIds']);
    	Session::save();
    	return $this->success('成功');
    }

    public function saveRegData(){
    	$args = Input::all();
    	Session::put('reg_info', $args);
    	Session::save(); 
    	return $this->success('成功');
    }

    public function doreg(){ 
    	$args = Input::all();
    	$cate = Session::get('cate_info');
    	$args['cateIds'] = is_array($cate) ? $cate : [$cate];
    	// var_dump($args);
    	// exit;
    	$reg_result = $this->requestApi('seller.reg', $args);
    	if($reg_result['code'] > 0){
    		return $this->error($reg_result['msg']);
    	} else {
    		Session::set('reg_info', NULL);
			Session::save();
    		return $this->success($reg_result['msg']);
    	}
    }

    public function app(){
    	$agent = strtolower($_SERVER['HTTP_USER_AGENT']);   
	    $iphone = (strpos($agent, 'iphone')) ? true : false;   
	    $ipad = (strpos($agent, 'ipad')) ? true : false;   
	    $android = (strpos($agent, 'android')) ? true : false;   
	    $config = $this->getConfig(); 
	    if($iphone || $ipad) {
	    	if(strpos($agent, 'micromessenger')){ 
	    		return $this->display();
	    	} else { 
	    		Redirect::to($config['staff_app_down_url'])->send();
	    	} 
	    } 
	    if($android) { 
	    	Redirect::to($config['staff_android_app_down_url'])->send();
	    }
    }

    /**
     * 地图页面
     */
    public function map(){
    	$data = Session::get('reg_info'); 
    	//var_dump($data); 
    	View::share('data', $data);
    	return $this->display();
    }

    /**
     * 保存地图数据
     */
    public function mapSave(){
    	$data = Session::get('reg_info');
    	$data['mapPosStr'] = Input::get('mapPos');
    	$data['mapPoint'] = Input::get('mapPoint'); 
    	Session::put('reg_info', $data);
    	Session::save();
    	return $this->success('成功');
    }

}
