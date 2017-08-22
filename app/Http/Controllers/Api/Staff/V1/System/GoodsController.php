<?php 
namespace YiZan\Http\Controllers\Api\Staff\System;

use YiZan\Services\SystemGoodsService;
use YiZan\Http\Controllers\Api\Staff\BaseController;

/**
 * 服务管理
 */
class GoodsController extends BaseController {
    /**
     * 服务列表
     */
    public function lists() {
        $data = SystemGoodsService::getList(
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20),
            $this->request('name'),
            (int)$this->request('cateId'),
            1
        );
		$this->outputData($data);
    }
}