<?php 
namespace YiZan\Http\Controllers\Api\Buyer\Seller;

use YiZan\Http\Controllers\Api\Buyer\BaseController;
use YiZan\Services\PromotionService;

class PromotionController extends BaseController {
	/**
     * 获取会员的优惠券列表
     */
	public function lists() {
		$data = PromotionService::getSellerPromotionList(
				(int)$this->request('sellerId'),
				$this->userId, 
				max((int)$this->request('page'), 1),
				20
			);
		$this->outputData($data);
	}
}