<?php 
namespace YiZan\Http\Controllers\Api\Buyer\Restaurant;

use YiZan\Services\Buyer\OrderService;

/**
 * 订单
 */
class OrderController extends  \YiZan\Http\Controllers\Api\Buyer\UserAuthController {
	
	/**
	 * 创建外卖订单
	 */
	public function create() {
		$result = OrderService::createOrder(
				$this->userId,
				(array)$this->request('goods'),
				$this->request('mobileId'),
				(int)$this->request('addressId'),
				$this->request('remark'),
				(int)$this->request('type')
			);
		$this->output($result);
	}

}