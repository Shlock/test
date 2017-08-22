<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\SellerWithdrawMoneyService;


class WithdrawController extends BaseController {
    /**
     * 提现列表
     */
    public function lists() {
        $data = SellerWithdrawMoneyService::lists(
                $this->request('name'),
                (int)$this->request('status'),
                (int)$this->request('beginTime'),
                (int)$this->request('endTime'),
                max((int)$this->request('page'), 1), 
                max((int)$this->request('pageSize'), 20)
            );
        $this->outputData($data);
    }
}