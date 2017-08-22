<?php 
namespace YiZan\Http\Controllers\Api\Buyer\Collect;

use YiZan\Http\Controllers\Api\Buyer\UserAuthController;
use YiZan\Services\Buyer\CollectService;
use Lang;

/**
 * 卖家收藏
 */
class SellerController extends UserAuthController {
	/**
	 * 评价
	 */
	public function lists() {
		$data = CollectService::sellerList(
				$this->userId,
				max((int)$this->request('page'), 1)
			);
		$this->outputData($data);
	}

	/**
	 * 添加收藏
	 */
	public function create() {
		$status = CollectService::collectSeller($this->userId, (int)$this->request('sellerId'));
		if (!$status) {
			$this->outputCode(10403);
		}
		$this->outputCode(0, Lang::get('api.success.collect_seller_create'));
	}

	/**
	 * 删除收藏
	 */
	public function delete() {
		$status = CollectService::deleteSeller($this->userId, (int)$this->request('sellerId'));
		if (!$status) {
			$this->outputCode(10404);
		}
		$this->outputCode(0, Lang::get('api.success.collect_seller_delete'));
	}
}