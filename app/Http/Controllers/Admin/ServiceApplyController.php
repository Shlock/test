<?php 
namespace YiZan\Http\Controllers\Admin;
use Input;
/**
 * 机构服务管理
 */
class ServiceApplyController extends SellerApplyController {
	protected $typeSer;
 	public function __construct() {
		parent::__construct();
		$this->typeSer = 2;
	}
}
