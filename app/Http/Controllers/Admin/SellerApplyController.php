<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Form, Lang; 
/**
 * 服务人员
 */
class SellerApplyController extends AuthController {	 
	/*
	 * 身份证管理 
	 */
	public function index() {  
		$args = Input::all();
        
        if(isset($args["isCheck"]) == false) $args["isCheck"] = 2;
        
		$result = $this->requestApi('seller.authlists',$args);  
		//print_r($result['data']['list']);
		if ($result['code'] == 0) {
			View::share('list', $result['data']['list']);
		}
		View::share('search_args', $args);
		return $this->display();  
	} 
	/**
	 * 获取身份详情
	 * 根据会员Id获取身份详情
	 */
	public function edit() {
		$id = (int)Input::get('id');  
		$args = [];
		if(!empty($id)){
			$args['id'] = $id;
			$result = $this->requestApi('seller.get',$args); 
			//print_r($result);
			if( $result['code'] == 0 ){  
				View::share('data',$result['data']); 
			}
		}
		return $this->display();  
	}	
	/** 
	*修改身份认证状态信息 
	*SellerApplyPostRequest $request
	*/
	public function update() {
		$args = Input::all();    
		$data = $this->requestApi('seller.updatecheck',$args); 
		/*返回处理*/
		$url = u('SellerApply/index');	
		if($data['code']==0){ 
			return $this->success($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98008'), $url, $data['data']);
		}else{ 
			return $this->error($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98009'), $url);
		}
	} 

	public function destroy() {
		$args = Input::all();      
		$data = $this->requestApi('seller.delete',['id' => $args['id']]); 
		/*返回处理*/
		$url = u('SellerApply/index');	
		if($data['code']==0){ 
			return $this->success($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98008'), $url, $data['data']);
		}else{ 
			return $this->error($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98009'), $url);
		}
	} 
}
