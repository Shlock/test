<?php 
namespace YiZan\Http\Controllers\Admin;  

/**
 * 卖家APP配置
 */
class SellerAppConfigController extends UserAppConfigController { 	 
	protected $groupCode;
 	public function __construct() {
		parent::__construct();
		$this->groupCode = 'seller';
	} 
}
