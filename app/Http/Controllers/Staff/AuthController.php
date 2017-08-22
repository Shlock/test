<?php 
namespace YiZan\Http\Controllers\Staff;

use Redirect, Input, Session;
/**
 * 员工登录验证基础控制器
 */
class AuthController extends BaseController {

	public function __construct() {
		parent::__construct();
		// $action_val = CONTROLLER_NAME.'.'.ACTION_NAME;
		if($this->staffId < 1) {
			if (Input::ajax()) {//未登录ajax提交返回错误提示
				die(json_encode(array('code'=>'99996','msg'=>'请先登录')));
			} else {
				header('Location:'.u('Staff/login'));
				exit;	
			}
		}
	}
}
