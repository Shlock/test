<?php 
namespace YiZan\Http\Controllers\Api\System\User;

use YiZan\Http\Controllers\Api\System\BaseController;
use YiZan\Services\System\PromotionService;

class PromotionController extends BaseController {
	/**
     * 获取会员可用优惠券
     */
	public function lists() {
		$data = PromotionService::getPromotionList(
				(int)$this->request('id'),
				$this->request('sn'),
				$this->request('conditionType'),
				$this->request('type'),
				(int)$this->request('status', 0),
				(int)$this->request('sellerId', 0),
				max((int)$this->request('page'), 1)
			);
		$this->outputData($data);
	}
}