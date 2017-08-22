<?php 
namespace YiZan\Http\Controllers\Api\System\Seller;

use YiZan\Services\System\SellerPayLogService;
use YiZan\Http\Controllers\Api\System\BaseController;

/**
 * 商家支付日志
 */
class PaylogController extends BaseController 
{
    /**
     * 商家支付列表
     */
    public function lists(){
        $data = SellerPayLogService::getLists(
                $this->request('userName'), 
                $this->request('userMobile'), 
                $this->request('beginTime'), 
                $this->request('endTime'), 
                $this->request('payment'), 
                max((int)$this->request('page'), 1), 
                max((int)$this->request('pageSize'), 20)
            );
        $this->outputData($data);
    }
}