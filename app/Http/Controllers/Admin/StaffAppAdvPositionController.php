<?php 
namespace YiZan\Http\Controllers\Admin;  
/**
*广告位
*/
class StaffAppAdvPositionController extends UserAppAdvPositionController {  
	protected $clietnType;
 	public function __construct() {
		parent::__construct();
		$this->clietnType = 'staff';
	}
}
