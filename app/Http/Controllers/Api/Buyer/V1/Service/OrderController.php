<?php 
namespace YiZan\Http\Controllers\Api\Buyer\Service;
use YiZan\Services\Buyer\OrderService;
use Input;
/**
 * 订单
 */
class OrderController extends  \YiZan\Http\Controllers\Api\Buyer\UserAuthController {
	
	/**
	 * 创建订单
	 */
	public function create() {
		$result = OrderService::createOrderAll(
				$this->userId,
				$this->request('id'),
				(int)$this->request('mobileId'),
				(int)$this->request('addressId'),
				$this->request('remark')
			);
		$this->output($result);
	}
}