<?php 
namespace YiZan\Http\Controllers\Admin;
use View, Input, Form,Lang; 
/**
 * 买家APP配置 意见反馈
 */
class SellerAppFeedbackController extends UserAppFeedbackController { 
	protected $type;
 	public function __construct() {
		parent::__construct();
		$this->type = 'seller';
	}
}
