<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\SellerComplainService;

class SellercomplainController extends UserAuthController {
	/**
	 * 服务人员举报增加
	 */
	public function create() {
		$result = SellerComplainService::create(
				$this->userId,
				(int)$this->request('sellerId'),
				trim($this->request('content'))
			);
		$this->output($result);
	}

	
}