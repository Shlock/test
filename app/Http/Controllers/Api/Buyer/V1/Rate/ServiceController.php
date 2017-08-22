<?php 
namespace YiZan\Http\Controllers\Api\Buyer\Rate;

use YiZan\Http\Controllers\Api\Buyer\BaseController;
use YiZan\Services\Buyer\OrderRateService;

/**
 * 订单评价
 */
class ServiceController extends BaseController {
	/**
	 * 评价
	 */
	public function lists() {
		$data = OrderRateService::getList(
				(int)$this->request('id'),
				max((int)$this->request('page'), 1)
			);
		$this->outputData($data);
	}
}