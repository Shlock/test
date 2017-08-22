<?php
namespace YiZan\Http\Controllers\Api\Buyer\Restaurant;

use YiZan\Services\Buyer\GoodsService;
use YiZan\Http\Controllers\Api\Buyer\BaseController;

class GoodsController extends BaseController {
	/**
	 * 菜品列表
	 */
	public function lists() {
        $data = GoodsService::getList((int)$this->request('id'), $this->request('type'));
        $this->outputData($data);

	}

}