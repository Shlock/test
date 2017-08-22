<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\AdminUser;
use View, Input, Lang, Route, Page, Form, Config, Session, Redirect;

/**
 * 后台首页
 */
class IndexController extends AuthController {
	/**
	 * 服务器信息
	 */
	public function index() {
		if ($this->seller['type'] == 3) { //如果是物业公司，直接跳转物业界面
			return Redirect::to(u('PropertyUser/index'));
		}
		
		//服务人员信息
		$sellerInfo = $this->seller;
		//订单概况
		$ordercount = $this->requestApi('statistics.ordercount');
		//账户余额
		$useraccount = $this->requestApi('useraccount.get');
		//本日营业额
		$today = $this->requestApi('statistics.today');
		if($sellerInfo) 
			View::share('sellerInfo',$sellerInfo);
		if($ordercount['code']==0)
			View::share('ordercount',$ordercount['data']);
		if($useraccount['code']==0) 
			View::share('useraccount',$useraccount['data']['money']);
		if($today['code']==0) 
			View::share('today',$today['data']);
		return $this->display();
	}

	/**
	 * 上传
	 */
	public function upload() {
		dd(Input::all());die;
		return $this->display();
	}
	/**
	 * 重新设置密码
	 */
	public function repwd(){
		return $this->display();
	}

	public function demo() {
		return $this->display();
	}
}
