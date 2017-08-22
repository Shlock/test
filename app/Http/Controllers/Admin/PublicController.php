<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Http\Controllers\YiZanController;
use View, Route, Input, Lang, Redirect;
/**
 * 后台公共页面控制器
 */
class PublicController extends BaseController {
	/**
	 * 管理员登录
	 */
	public function login() {
		if( Session::get('admin_token') && Session::get('admin_user') ) {
			return Redirect::to('Index/index');
		}
		return $this->display();
	}

	/**
	 * 检查登录提交信息
	 */
	public function dologin() {
		$args = Input::all();
		if (empty($args['name'])) {
			return $this->error(Lang::get('admin.code.11000'), u('Public/login'), $args);
		}

		if (empty($args['pwd'])) {
			return $this->error(Lang::get('admin.code.11001'), u('Public/login'), $args);
		}

		$result = $this->requestApi('admin.user.login',$args);  
		if( $result['code'] > 0 ) {
			return $this->error($result['msg'], u('Public/login'));
		}
		$this->setSecurityToken($result['data']['token']);
		$this->setAdminUser($result['data']['data']);
		
		return $this->success($result['msg'], u('Index/index'), $result['data']);
	}

	/**
	 * 生成验证码
	 */
	public function verify() {
		
	}

	/**
	 * 管理员登出
	 */
	public function logout() {
		Session::put('admin_user', null);
		$this->setSecurityToken(null);
		return Redirect::to('Public/login');
	}
}
