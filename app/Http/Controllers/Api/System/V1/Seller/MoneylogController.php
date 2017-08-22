<?php 
namespace YiZan\Http\Controllers\Api\System\Seller;

use YiZan\Http\Controllers\Api\System\BaseController;
use YiZan\Services\System\SellerMoneyLogService;

/**
 * 服务人员资金流水
 */
class MoneylogController extends BaseController {
    
    /**
     * 资金流水列表
     */
    public function lists(){
        $data = SellerMoneyLogService::getLists(
                $this->request('sellerName'), 
                $this->request('sellerMobile'), 
                $this->request('beginTime'), 
                $this->request('endTime'), 
                max((int)$this->request('page'), 1), 
                max((int)$this->request('pageSize'), 20)
            );
        $this->outputData($data);
    }
}