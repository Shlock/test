<?php 
namespace YiZan\Http\Controllers\Api\Buyer\Rate;

use YiZan\Http\Controllers\Api\Buyer\BaseController;
use YiZan\Services\Buyer\OrderRateService;

/**
 * 订单评价
 */
class SellerController extends BaseController {
	/**
	 * 评价
	 */
	public function lists() {
		$data = OrderRateService::sellerRates(
				(int)$this->request('sellerId'),
				$this->request('type'),
				max((int)$this->request('page'), 1)
			);
		$this->outputData($data);
	}
}