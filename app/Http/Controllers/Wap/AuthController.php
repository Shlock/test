<?php 
namespace YiZan\Http\Controllers\Wap;

/**
 * 会员登录验证基础控制器
 */
class AuthController extends BaseController {
	public function __construct() {
		parent::__construct();

		if ($this->userId < 1) {
			return $this->error('请先登录后再进行此操作', u('User/login'));
		}
	}
}
