<?php 
namespace YiZan\Http\Controllers\Staff;

use YiZan\Http\Controllers\YiZanController;
use YiZan\Models\Sellerweb\Seller;
use View, Route, Input, Lang, Redirect;
/**
 * 后台公共页面控制器
 */
class PublicController extends BaseController {
	/**
	 * 管理员登录
	 */
	public function login() {
		if( Session::get('seller_token') && Session::get('seller') ) {
			return Redirect::to('Index/index');
		}
		return $this->display();
	}
	/**
	 * 卖家注册页面
	 */
	public function register() { 
		$args = Input::all();
		$args['groupCode'] = 'seller_reg';
		$result = $this->requestApi('system.config.get',$args);   
		if($result['code']==0) {
			View::share('config',$result['data']);  
		}
		return $this->display();
	}

	/**
	 * 获取开通城市
	 * @return [type] [description]
	 */
	public function getOpenCitys(){
		$region_result = $this->requestApi('city.lists'); 
		echo json_encode($region_result['data']);
	}

	/**
	* 	提交注册信息
	*/
	public function doregister() {
		$args = Input::all();
		$result = $this->requestApi('user.reg',$args);   
		die(json_encode($result));		
	}	
	/*
	* 	提交注册信息
	*/
	public function stypeGoods(){
		$result = $this->requestApi('goods.cate.lists');  
		$result = $this->requestApi('user.login',$args); 
		die(json_encode($result['data']));
	}
	/**
	 * 检查登录提交信息
	 */
	public function dologin() {
		$args = Input::all();
		if(empty($args['mobile'])) $this->error(Lang::get('seller.code.11000'), u('Public/login'), $args);
		if(empty($args['pwd'])) $this->error(Lang::get('seller.code.11001'), u('Public/login'), $args);

		$result = $this->requestApi('user.login',$args); 
		if( $result['code'] > 0 ) {
			$this->error($result['msg'], u('Public/login'));
		}
		$this->setSecurityToken($result['token']);
		$this->setSeller($result['data']);
		$this->success($result['msg'], u('Index/index'), $result['data']);
	}

	/**
	 * 生成验证码
	 */
	public function verify() {
		$args = Input::all();
		$result = $this->requestApi('user.'.$args['vertype'],$args); 
		die(json_encode($result));
	}
	
	/**
	 * 成功
	 */
	public function _success() {
		return $this->display();
	}

	/**
	 * 失败
	 */
	public function _error() {
		return $this->display();
	}

	public function forgetpwd() {
		return $this->display();
	}

	public function checkpwd() {
		$args = Input::all();
		$args['sellerId'] = 0;
		$result = $this->requestApi('user.changepwd',$args);
		die(json_encode($result));
	}

	public function checkpwds() {
		return $this->display();
	}

	/**
	 * 管理员登出
	 */
	public function logout() {
		$this->requestApi('user.logout');

		Session::put('seller', null);
		$this->setSecurityToken(null);

		return Redirect::to('Public/login');
	}
}
