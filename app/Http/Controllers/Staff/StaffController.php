<?php 
namespace YiZan\Http\Controllers\Staff;
use Input, Lang, View, Redirect, Response;
/**
 * 员工控制器
 */
class StaffController extends BaseController {
	// public function __construct() {
	// 	parent::__construct();
	// 	if($this->staffId > 0) {
	// 		header('Location:'.u('Mine/index'));
	// 		exit;
	// 	}
	// }
	// 
	/**
	 * 员工登录
	 */
	public function login() {
		$return_url = Session::get('return_url');
		View::share('return_url', !empty($return_url) ? $return_url : u('Mine/index'));
        if ($this->tpl != 'staff.run') {
            View::share('is_show_top',false);
        }
        View::share('top_title','登录');
		return $this->display();
	}

	/**
	 * 执行登录
	 */
	public function dologin() {
        $result = $this->requestApi('user.login',Input::all());
		if ($result['code'] == 0) {
			$this->setStaff($result['data']);
			$this->setSecurityToken($result['token']);
			Session::set('return_url','');
			Session::save();
		}
		
		//die(json_encode($result));
		return Response::json($result);
	}

	/**
	 * 员工登出
	 */
	public function logout() {
		$this->staff = '';
		$this->staffId = 0;
		Session::set('staff','');
		$this->setSecurityToken(null);
		return Redirect::to(u('Staff/login'));
	} 

	/**
	 * [reg 用户注册]
	 */
	public function reg() {
		return $this->display();
	}

	/**
	 * [doreg 执行注册]
	 */
	public function doreg() {
		$data = Input::all();
		$data['type'] = 'reg';
		$result = $this->requestApi('user.reg',$data);
		if($result['code'] == 0){
			$this->setUser($result['data']['user']);
			$this->setSecurityToken($result['data']['token']);
		}
		die(json_encode($result));
	}

	/**
	 * [reg 修改密码]
	 */
	public function repwd() {
		return $this->display();
	}

	/**
	 * [doreg 执行修改密码]
	 */
	public function dorepwd() {
		$data = Input::all();
		$data['type'] = 'repwd';
		$result = $this->requestApi('user.repwd',$data);
		if($result['code'] == 0){
			$this->setStaff($result['data']['user']);
			$this->setSecurityToken($result['data']['token']);
		}
		die(json_encode($result));
	}

	/**
	 * 生成验证码
	 */
	public function verify() {
		$mobile = Input::get('mobile');
		$result = $this->requestApi('user.mobileverify',array('mobile'=>$mobile));
		die(json_encode($result));
	}
    /**
     * 生成验证码
     */
	public function voiceverify() {
		$mobile = Input::get('mobile');
		$result = $this->requestApi('user.mobileverify',array('mobile'=>$mobile, "voice"=>true));
		die(json_encode($result));
	}

}
