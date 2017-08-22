<?php 
namespace YiZan\Http\Controllers\Wap;

use Input, View;
/**
 * 洗车
 */
class CarController extends AuthController {
	public function index() {
		
	}

	public function detail() {
		return $this->display();
	} 
}
