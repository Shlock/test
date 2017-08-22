<?php namespace Sky\Http\Controllers\Www;
 
use Lang, Input, Redirect, Session, View;

/**
 * 会员中心控制器
 */
class MessageController extends AuthController {
	public function __construct() {
		parent::__construct(); 
	}

	/**
	 * [index 会员消息列表] 
	 */
	public function index(){
		$result = $this->requestApi('user.notice.lists',Input::all());  
		$this->share('list', $result['data']['lists']); 
		return $this->display();
	}

	/**
	 * [delete 会员消息删除] 
	 */
	public function delete(){
		$result = $this->requestApi('user.notice.delete',Input::all());  
		if($result['code'] == 0){
			return $this->success();
		}
		return $this->error('删除失败');
	}

	/**
	 * [detail 会员消息明细] 
	 */
	public function detail(){
		$result = $this->requestApi('user.notice.get',Input::all());  
		if($result['code'] == 0){
			$this->share('data', $result['data']); 
		}
		return $this->display();
	}

}