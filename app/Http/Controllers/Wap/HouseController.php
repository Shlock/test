<?php 
namespace YiZan\Http\Controllers\Wap;

/**
 * 家政(与Run合并)
 */
class HouseController extends AuthController {
	public function index() {
		return $this->display();
	}
}
