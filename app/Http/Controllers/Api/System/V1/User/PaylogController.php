<?php 
namespace YiZan\Http\Controllers\Api\System\User;

use YiZan\Services\System\UserPayLogService;
use YiZan\Http\Controllers\Api\System\BaseController;

/**
 * 会员支付日志
 */
class PaylogController extends BaseController 
{
    /**
     * 支付日志列表
     */
    public function lists(){
        $data = UserPayLogService::getLists(
                $this->request('name'),
                $this->request('mobile'),
                $this->request('orderSn'),
                $this->request('paySn'),
                $this->request('beginTime'),
                $this->request('endTime'),
                $this->request('payment'),
                $this->request('payStatus'),
                (int)$this->request('payType'),
                max((int)$this->request('page'), 1),
                max((int)$this->request('pageSize'), 20),
                (int)$this->request('isTotal')
            );
        $this->outputData($data);
    }
}