<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\GoodsComplainService;

class GoodscomplainController extends UserAuthController {
	/**
	 * 服务举报增加
	 */
	public function create() {
		$result = GoodsComplainService::create(
				$this->userId,
				(int)$this->request('goodsId'),
				trim($this->request('content'))
			);
		$this->output($result);
	}

	
}