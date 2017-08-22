<?php 
namespace YiZan\Http\Controllers\Api\Staff\Rate;

use YiZan\Http\Controllers\Api\Staff\BaseController;
use YiZan\Services\OrderRateService;

/**
 * 订单评价
 */
class StaffController extends BaseController {
	/**
	 * 评价
	 */
	public function lists() 
    {
		$data = OrderRateService::staffRates(
				$this->staffId,
				(int)$this->request('goodsId'),
                (int)$this->request('type'),
				max((int)$this->request('page'), 1)
			);
        
		$this->outputData($data);
	}
}