<?php 
namespace YiZan\Http\Controllers\Api\System\Seller;

use YiZan\Http\Controllers\Api\System\BaseController;
use YiZan\Services\System\SellerWithdrawMoneyService;

/**
 * 服务人员提现
 */
class WithdrawController extends BaseController {
    /**
     * 提现列表
     */
    public function lists() {
        $data = SellerWithdrawMoneyService::getLists(
                $this->request('sellerName'), 
                $this->request('sellerMobile'), 
                $this->request('beginTime'), 
                $this->request('endTime'), 
                (int)$this->request('status'), 
                max((int)$this->request('page'), 1), 
                max((int)$this->request('pageSize'), 20)
            );
        $this->outputData($data);
    }

    /**
     * 提现处理
     *
    public function dispose() {
        $result = SellerWithdrawMoneyService::dispose(
                $this->adminId,
                (int)$this->request('id'), 
                $this->request('content'), 
                (int)$this->request('status')
            );
        $this->output($result);
    }*/
    /**
     * 提现处理社区
     */
    public function dispose() {
        $result = SellerWithdrawMoneyService::dispose(
            $this->adminId,
            (int)$this->request('id'),
            $this->request('content'),
            (int)$this->request('status')
        );
        $this->output($result);
    }
}