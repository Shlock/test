<?php 
namespace YiZan\Http\Controllers\Admin; 
 
use YiZan\Http\Requests\Admin\SellerCreditRankPostRequest;
use View, Input, Form,Lang,Validator; 

/**
 * 荣誉等级
 */
class SellerCreditRankController extends AuthController {
	/**
	 * 等级列表
	*/
	public function index() {
		$list = $this->requestApi('seller.creditrank.lists'); 
		if( $list['code'] == 0 ) {
			View::share('list', $list['data']); 
		}
		return $this->display();
	}
	/**
	 * 获取详情
	*/
	public function create() { 
		return $this->display('edit'); 
	}
	/**
	 * 获取详情
	*/
	public function edit() {
		$args = Input::all();  
		if(!empty($args['id']) ){
			$data = $this->requestApi('seller.creditrank.get',$args);	 
			View::share('data', $data['data']);	
		} 
		return $this->display();
	}
	/**
	 * 获取详情
	 * SellerCreditRankPostRequest $request
	*/
	public function update(SellerCreditRankPostRequest $request) { 
		$args = Input::all(); 
		if (empty($args['id'])){
			$data = $this->requestApi('seller.creditrank.create',$args);
			if( $data['code'] == 0 ) {
				return $this->success($data['msg']?$data['msg']:$data['msg']=Lang::get('admin.code.98001'),u('SellerCreditRank/create'));
			}
			else {
				return $this->error($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98002'),'',$args);
			}	
		}else{
			$data = $this->requestApi('seller.creditrank.update',$args);
			if( $data['code'] == 0 ) {
				return $this->success($data['msg']?$data['msg']:$data['msg']=Lang::get('admin.code.98003'),u('SellerCreditRank/edit'), $data['data']);
			}
			else {
				return $this->error($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98004'));
			}			
		}	  
	}  
	/*
	* 删除等级
	*/
	public function destroy(){ 
		$id = (int)Input::get('id');   
		$args = array();
		if (empty($id)) {
			return $this->error(Lang::get('admin.noId'),u('SellerCreditRank/index'));
		}
		$args['id']  = $id;
		$data = $this->requestApi('seller.creditrank.delete',$args); 
		if( $data['code'] > 0 ) {
			return $this->error($data['msg'], url('SellerCreditRank/index'));
		}
		return $this->success($data['msg'], url('SellerCreditRank/index'), $data['data']);
	}	
}
