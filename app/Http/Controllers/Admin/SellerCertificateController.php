<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Form,Lang;
/**
 * 服务人员
 */
class SellerCertificateController extends AuthController {
	/*
	 * 资质认证管理
	 */
	public function index() {
		$args = Input::all();		
		!empty($args['type']) ? $args['type'] : $args['type'] = 2;
		//服务人员认证接口
		$result = $this->requestApi('seller.certificate.lists', $args); 
		if( $result['code'] == 0 ) {
			foreach ($result['data']['list'] as $k => $v) {
				$result['data']['list'][$k]['id'] = $v['sellerId'];
			}  
			if( $result['code'] == 0 )
				View::share('list', $result['data']['list']);  
		}

		$nav = Input::get('nav');
		if (!isset($nav)) {
			$nav = 2;
		}
		View::share('nav', $nav);
		return $this->display();
	} 
	/**
	 * 获取认证详情
	 * 根据会员Id获取资质认证详情
	 */
	public function edit() {
		$id = (int)Input::get('id');  
		$args = []; 
		$args['sellerId'] = $id;
		$result = $this->requestApi('seller.certificate.get',$args);  
		if( $result['code'] == 0 )
			View::share('data', $result['data']);
		return $this->display();   
	}	
	/**
	* 
	*   修改资质认证状态信息 
	*	SellerApplyPostRequest $request
	*/
	public function update() {
		$args = Input::all(); 		 
		$args['id'] = $args['sellerId'];
		$data = $this->requestApi('seller.certificate.update',$args);  
		/*返回处理*/
		$url = u('ServiceCertificate/edit');
		if($data['code']==0){ 
			return $this->success($data['msg'], $url, $data['data']);
		}else{ 
			return $this->error($data['msg'], $url, $data['data']);
		}
	}
	
}