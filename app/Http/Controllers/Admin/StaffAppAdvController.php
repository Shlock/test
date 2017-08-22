<?php 
namespace YiZan\Http\Controllers\Admin;  

/**
*广告管理
*/
class StaffAppAdvController extends UserAppAdvController {  
	protected $clietnType;
 	public function __construct() {
		parent::__construct();
		$this->clietnType = 'staff';
	}
}
