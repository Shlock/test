<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\DataBase;
use View, Input, Lang;

/**
 * 系统配置
 */
class DataBaseController extends AuthController {
	public function index(){
		return $this->display();
	}

	

}
