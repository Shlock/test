<?php 
namespace YiZan\Http\Controllers\Admin;

use Input,View;
/**
* 会员配置
**/
class UserConfigController extends AuthController {
	/**
	 * 编辑配置
	 */
	public function index() {
		$data = Input::get("data"); 		
		$list = $this->requestApi('user.updateagreement',$data);
		View::share('list', $list); 
		return $this->display();
	}
}
