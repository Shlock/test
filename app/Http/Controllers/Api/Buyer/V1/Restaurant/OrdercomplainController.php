<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\OrderComplainService;

class OrdercomplainController extends UserAuthController {

	/**
	 * 订单举报增加
	 */
	public function create() {
		$result = OrderComplainService::create(
				$this->userId,
				(int)$this->request('orderId'),
				trim($this->request('content')),
				$this->request('images')
			);
		$this->output($result);
	}    

	/**
	 * [get 获取订单举报信息] 
	 */
	// public function get(){
	// 	$result = OrderComplainService::get(
	// 			$this->userId,
	// 			(int)$this->request('complainId') 
	// 		);
	// 	$this->outputData($result);
	// }

	/**
	 * [lists 订单举报列表] 
	 */
	public function lists() {
		$result = OrderComplainService::getLists(
				$this->userId ,
				max((int)$this->request('page'),1)
			);
		$this->outputData($result);
	}
	
}