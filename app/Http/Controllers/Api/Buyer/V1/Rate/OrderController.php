<?php 
namespace YiZan\Http\Controllers\Api\Buyer\Rate;

use YiZan\Http\Controllers\Api\Buyer\UserAuthController;
use YiZan\Services\Buyer\OrderRateService;

/**
 * 订单评价
 */
class OrderController extends UserAuthController {
	/**
	 * 评价
	 */
	public function create() {
		$result = OrderRateService::createRate(
				$this->userId,
				(int)$this->request('orderId'),
                (array)$this->request('images'),
				$this->request('content'),
                (int)$this->request('star'),
                (int)$this->request('isAno')
			);
		$this->output($result);
	}

    /**
     * 评价列表
     */
    public function lists() {
        $data = OrderRateService::getList(
            (int)$this->request('sellerId'),
            (int)$this->request('type'),
            max((int)$this->request('page'),1)
        );
        $this->outputData($data);
    }

    /**
     * 评价统计
     */
    public function statistics() {
        $data = OrderRateService::getCount(
            (int)$this->request('sellerId')
        );
        $this->outputData($data);
    }
}