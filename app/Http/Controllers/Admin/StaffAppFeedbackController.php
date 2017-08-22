<?php 
namespace YiZan\Http\Controllers\Admin;
use View, Input, Form,Lang; 
/**
 * 员工APP配置 意见反馈
 */
class StaffAppFeedbackController extends UserAppFeedbackController { 
	protected $type;
 	public function __construct() {
		parent::__construct();
		$this->type = 'staff';
	}
}
