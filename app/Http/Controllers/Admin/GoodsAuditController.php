<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Lang, Route, Page, Validator, Session, DB;
/**
 * 服务审核
 */
class GoodsAuditController extends AuthController {
	/**
	 * 服务审核-审核列表
	 */
	public function index() {
		$post = Input::all();
		!empty($post['name']) 		 ?  $args['name']        = strval($post['name']) : null;
		!empty($post['sellerName']) ?  $args['sellerName'] = strval($post['sellerName']) : null;
		!empty($post['cateId'])    ?  $args['cateId']    = intval($post['cateId']) : null;
		!empty($post['page']) 	  ?  $args['page'] 	   = intval($post['page']) : $args['page'] = 1;
		$args['status'] = 1; //待审核
		$result = $this->requestApi('goods.lists', $args);
		if( $result['code'] == 0 ){
			View::share('list', $result['data']['list']);
		}
		$_cate = $this->getcate();
		$cate2 = [ 
			"id" => '',
			"pid" => '',
			"name" => "全部分类",
			"sort" => '',
			"status" => '',
			"level" => '',
			"levelname" => "全部分类",
			"levelrel" => ""
		];
		array_unshift($_cate, $cate2);
		$cate = [];
		foreach ($_cate as $key => $value) {
			$cate[$value['id']] = $value;
		}
		View::share('cate', $cate);
		return $this->display();
	}

	/**
	 * 服务审核详情
	 */
	public function detail() {
		$args = Input::all();
		if( !empty($args['id']) ) {
			$args['id'] = $args['id'];
			$result = $this->requestApi('goods.get',$args);
			View::share('data', $result['data']);
		}
		$city = $this->requestApi('city.lists');
		if( $city['code'] == 0 ) {
			View::share('city', $city['data']);
		}
		$_cate = $this->getcate();
		$cate = [];
		foreach ($_cate as $key => $value) {
			$cate[$value['id']] = $value;
		}
		View::share('cate', $cate);
		return $this->display();
	}

	/**
	 * 处理
	 */
	public function dispose() {
		$args = Input::all();
		if( $args['id'] == '' ) {
			return $this->error( Lang::get('admin.noId') );
		}
		//通过
		if( $args['type'] === 'Y' ) {
			$args['status'] = 1;
			$result = $this->requestApi('goods.auditGoods',$args);
			if($result['code']>0){
				return $this->error( $result['msg'] );
			}
			return $this->success( Lang::get('admin.code.21018') , u('GoodsAudit/index'), $result);
		}
		//拒绝
		else if(  $args['type'] === 'N'  ) {
			if(  trim($args['disposeResult']) == ''  ){
				return $this->error( Lang::get('admin.code.21017') );
			}
			$args['status'] = -1;
			$result = $this->requestApi('goods.auditGoods',$args);
			if($result['code']>0){
				return $this->error( $result['msg'] );
			}
			return $this->success( Lang::get('admin.code.21019') , u('GoodsAudit/index'), $result);
		}
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


}
