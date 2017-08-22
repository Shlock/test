<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Goods;
use YiZan\Models\Seller;
use View, Input, Lang, Route, Page, Validator, Session, Response;
/**
 * 服务
 */
class GoodsController extends AuthController {
	/**
	 * 服务管理-服务列表
	 */
	public function index() {
		$args = Input::all();
        $args['status'] = [STATUS_DISABLED, STATUS_ENABLED];
                
		$result = $this->requestApi('goods.lists', $args);
		if( $result['code'] == 0 ){
			View::share('list', $result['data']['list']);
		}
		$_cate = $this->getcate();
		$cate2 = [ "id" => '',"pid" => '',"name" => "全部分类","sort" => '',"status" => '',"level" => '',"levelname" => "全部分类","levelrel" => ""];
		array_unshift($_cate, $cate2);
		$cate = [];
		foreach ($_cate as $key => $value) {
			$cate[$value['id']] = $value;
		}
		View::share('cate', $cate);
		View::share('excel',http_build_query($args));
		return $this->display();
	}

	/**
	 * 服务审核
	 */
	public function audit() {
		$post = Input::all();
        
		$args['status'] = [\YiZan\Models\Base::STATUS_NOT_BY, \YiZan\Models\Base::STATUS_AUDITING];
        
        
        switch(Input::get("status"))
        {
            case "1":
                $args['status'] = [\YiZan\Models\Base::STATUS_NOT_BY];
                break;
                
            case "2":
                $args['status'] = [\YiZan\Models\Base::STATUS_AUDITING];
                break;
            
            default:
                $args['status'] = [\YiZan\Models\Base::STATUS_NOT_BY, \YiZan\Models\Base::STATUS_AUDITING];
                break;
        }
            
		!empty($post['name']) 	?  $args['name']  	= strval($post['name']) 	: null;
		!empty($post['page']) 	?  $args['page'] 	= intval($post['page']) 	: $args['page'] = 1;
		$result = $this->requestApi('goods.lists', $args);
		if($result['code']==0)
			View::share("list",$result['data']['list']);
		$_cate = $this->getcate();
		$cate = [];
		foreach ($_cate as $key => $value) {
			$cate[$value['id']] = $value;
		}
		View::share('cate', $cate);
		return $this->display();
	}

	/**
	 * 服务审核进度
	 */
	public function auditplan() {
		$args = Input::all();
		if( isset($args['id']) ) {
			View::share('data',$result['data']);
		}
		return $this->display();
	}

	/**
	 * 添加服务(取消)
	 */
	public function addgoods() {
		return $this->display();
	}

	/**
	 * 服务种类须知
	 */
	public function quickchoose(){
		$type = (int)Input::get('type');
		$args['name'] = Input::get('name'); 
		$args['page'] = (int)Input::get('page') > 1 ? (int)Input::get('page') : 1; 
		$result = $this->requestApi('system.goods.lists',$args); 
		if( $result['code'] == 0 ){
			View::share('list', $result['data']['list']);
		}
		if($type==0){
			$args['type'] = 1;
		} else {
			$args['type'] = 0;
		}
		View::share('args',$args);
		if($type > 0){
			return $this->display('textchoose');
		}
		return $this->display();	
	} 

	/**
	 * 添加新服务
	 */
	public function create() {
		$args['id'] = Input::get('id'); 
		
		$systemGoods = $this->requestApi('system.goods.get',$args); 
		
		$_cate = $this->getcate();
		
		$cate2 = [ "id" => '',"pid" => '',"name" => "选择分类","sort" => '',"status" => '',"level" => '',"levelname" => "选择分类","levelrel" => ""];
		
		array_unshift($_cate, $cate2);
		
		$cate = [];

		foreach ($_cate as $key => $value) {
			$cate[$value['id']] = $value;
		}

		View::share('cate', $cate);  

		if($systemGoods['code'] == 0 && !empty($systemGoods['data'])){ 
			View::share('systemGoods',$systemGoods['data']);
		}

		if($this->seller['type'] == Seller::SERVICE_PERSONAL){
			$views = 'edit';
		} else {
			$views = 'goodsedit';
		}

		return $this->display($views);
	}

	/**
	 * 编辑服务
	 */
	public function edit() {
		$args['goodsId'] = Input::get('id');
		$result = $this->requestApi('goods.get',$args);

		if($result['code']==0) {
            if(isset($result['data']) && $result['data']['sellerId'] == 0){
                View::share('systemGoods', $result['data']);
            }
            View::share('data', $result['data']);
        }
		$_cate = $this->getcate();
		$cate2 = [ "id" => '',"pid" => '',"name" => "选择分类","sort" => '',"status" => '',"level" => '',"levelname" => "选择分类","levelrel" => ""];
		array_unshift($_cate, $cate2);
		$cate = [];
		foreach ($_cate as $key => $value) {
			$cate[$value['id']] = $value;
		}
		View::share('cate', $cate);
		if($this->seller['type'] == Seller::SERVICE_PERSONAL){
			$views = 'edit';
		} else {
			$views = 'goodsedit';
		}
		return $this->display($views);
	}



	//获取分类
	public function getcate() {
		$result = $this->requestApi('goods.cate.lists');
		if($result['code']==0) {
			$this->generateTree(0,$result['data']);
		}
		//生成树形
		$cate = $this->_cates;
		return $cate;
	}

	public function save() {
		$args = Input::all(); 

		if(isset($args['staffIds']) && !empty($args['staffIds'])){
			$args['staffIds'] = explode(',', $args['staffIds']);
		}  

		if(isset($args['duration']) && !empty($args['duration'])){
			$args['duration'] *= 3600;
		}  
		if( $args['id'] > 0 ){
			$result = $this->requestApi('goods.update',$args);
			$msg = Lang::get('seller.code.98003');
		}
		else{
			$result = $this->requestApi('goods.create',$args);
			$msg = Lang::get('seller.code.98001');
		}

		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		if(isset($args['systemGoodsId']) && $args['systemGoodsId'] > 0){
			return $this->success($msg, u('Goods/index'));
		} else {
			return $this->success($msg, u('Goods/auditplan'), $result['data']);
		}
	}

	public function destroy() {
		$args['id'] = Input::get('id');
		$result = $this->requestApi('goods.delete',$args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('seller.code.98005'), u('Goods/index'), $result['data']);
	}

	public function updateStatus() {
		$args = Input::all();
		$result = $this->requestApi('goods.updateStatus',$args);
		return Response::json($result);
	}
}
