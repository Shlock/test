<?php 
namespace YiZan\Http\Controllers\Admin; 

//使用的命名空间
use Input,View;

/**
 * 会员管理
 */
class UserStatisticsController extends AuthController {
	/**
	 * 会员概览信息统计
	*/
	public function index() {
		$list = $this->requestApi('user.lists');
		View::share('list', $list);
		return $this->display();
	}
}
