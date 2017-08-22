<?php 
namespace YiZan\Http\Controllers\Admin; 

use Input,View,From,Lang;

/**
 * 卖家APP配置
 */
class StaffAppConfigController extends UserAppConfigController {
	protected $groupCode;
	protected $type;
 	public function __construct() {
		parent::__construct();
		$this->groupCode = 'staff';
		$this->type = 'staff';
	}
}
