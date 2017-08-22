<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\AdminUser;
use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Form, Config;

/**
 * 后台首页
 */
class IndexController extends AuthController {
	/**
	 * 服务器信息
	 */
	public function index() {
		$type = (int)Input::get('type') > 0 ? Input::get('type') : 1;
		$total = $this->requestApi('totalview.total');
		$data = $this->requestApi('order.ordercount.total',array('type'=>$type));
		if($total['code'] == 0)
			View::share('total', $total['data']);
		if($data['code'] == 0)
			View::share('data', $data['data']);
		return $this->display();
	}

	public function test(){
		$goodsNames = [
			'elevit 爱乐维',
			'NORDIC NATURALS 挪威小鱼',
			'ŴaKODO 和光堂',
			'EQUAZEN',
		];

		$rand = rand(0,count($goodsNames) - 1);

		$currentName = $goodsNames[$rand];
		
		echo $currentName;
	}

	/**
	 * 上传
	 */
	public function upload() {
		return $this->display();
	}
	/**
	 * 重新设置密码
	 */
	public function repwd(){
		return $this->display('index','demo');
	}

	public function demo() {
		echo Time::toDate(1447660800);exit;
		return $this->display('index','demo');
	}


}
