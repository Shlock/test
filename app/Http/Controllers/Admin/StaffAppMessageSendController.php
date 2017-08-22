<?php 
namespace YiZan\Http\Controllers\Admin;
 
/**
 * 员工APP配置 推送
 */
class StaffAppMessageSendController extends UserAppMessageSendController { 
	protected $type;
 	public function __construct() {
		parent::__construct();
		$this->type = 'staff';
	}
}
