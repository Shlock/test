<?php 
namespace YiZan\Http\Controllers\Staff;

use Redirect;
/**
 * 首页
 */
class IndexController extends AuthController {
	/**
	 * 首页信息 
	 */
	public function index() {
		return Redirect::to(u('Mine/index'));
	}


}
