<?php 
namespace YiZan\Http\Controllers\Api\Buyer\Rate;

use YiZan\Http\Controllers\Api\Buyer\BaseController;
use YiZan\Services\Buyer\OrderRateService;

/**
 * 订单评价
 */
class StaffController extends BaseController {
	/**
	 * 评价
	 */
	public function lists() {
		$data = OrderRateService::staffRates(
				(int)$this->request('staffId'),
				(int)$this->request('goodsId'),
				$this->request('type'),
				max((int)$this->request('page'), 1)
			);
		$this->outputData($data);
	}
}