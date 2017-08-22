<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb\Seller;

use YiZan\Http\Controllers\Api\Sellerweb\BaseController;
use YiZan\Services\SellerCateService; 
class CateController extends BaseController {

	/**
	 * 服务列表
	 */
	public function lists() { 
		$data = SellerCateService::getSellerCateLists(
            $this->sellerId
		); 
		$this->outputData($data);
	}  

}