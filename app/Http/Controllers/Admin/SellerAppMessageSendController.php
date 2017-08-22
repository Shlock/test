<?php 
namespace YiZan\Http\Controllers\Admin;
/**
 * 买家APP配置 推送
 */
class SellerAppMessageSendController extends UserAppMessageSendController { 
	protected $type;
 	public function __construct() {
		parent::__construct();
		$this->type = 'seller';
	} 
}
