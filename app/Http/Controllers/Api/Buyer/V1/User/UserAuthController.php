<?php 
namespace YiZan\Http\Controllers\Api\Buyer\User;

use YiZan\Http\Controllers\Api\Buyer\BaseController;
use YiZan\Utils\Time;

class UserAuthController extends BaseController {
	/**
	 * 检测会员是否登录
	 */
	public function __construct() {
		parent::__construct();
		if (!$this->user) {
			$this->outputCode(99996);
		}
	}
}