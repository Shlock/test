<?php 
namespace YiZan\Http\Controllers\Api\Buyer\Collect;

use YiZan\Http\Controllers\Api\Buyer\UserAuthController;
use YiZan\Services\Buyer\CollectService;
use Lang;

/**
 * 服务收藏
 */
class GoodsController extends UserAuthController {
	/**
	 * 列表
	 */
	public function lists() {
		$data = CollectService::goodsList(
				$this->userId,
				(int)$this->request('type'),
				max((int)$this->request('page'), 1)
			);
		$this->outputData($data);
	}

	/**
	 * 添加收藏
	 */
	public function create() {
		$status = CollectService::collectGoods($this->userId, (int)$this->request('goodsId'));
		if (!$status) {
			$this->outputCode(10401);
		}
		$this->outputCode(0, Lang::get('api.success.collect_goods_create'));
	}

	/**
	 * 删除收藏
	 */
	public function delete() {
		$status = CollectService::deleteGoods($this->userId, (int)$this->request('id'), (int)$this->request('type'));
		if (!$status) {
			$this->outputCode(10401);
		}
		$this->outputCode(0, Lang::get('api.success.collect_goods_delete'));
	}
}