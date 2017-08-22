<?php 
namespace YiZan\Http\Controllers\Wap;
use Redirect, Input, Session;
/**
 * 会员权限验证
 */
class UserAuthController extends BaseController {

	public function __construct() {
		parent::__construct();
		$action_val = CONTROLLER_NAME.'.'.ACTION_NAME;

        if ($this->userId < 1
            && $action_val != 'UserCenter.obtaincoupon'
            && $action_val != 'UserCenter.checkmobile'
            && $action_val != 'UserCenter.docheckmobile'
            && $action_val != 'UserCenter.index'
            && $action_val != 'UserCenter.authorize'
            && $action_val != 'UserCenter.accesstoken'
        ) {
			
			if (Input::ajax()) {//未登录ajax提交返回错误提示
				die(json_encode(array('code'=>'99996','msg'=>'请先登录')));
			} else {
				header('Location:'.u('User/login'));
				exit;	
			}
		}
	}
}
