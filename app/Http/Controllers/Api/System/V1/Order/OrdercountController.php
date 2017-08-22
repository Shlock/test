<?php 
namespace YiZan\Http\Controllers\Api\System\Order;

use YiZan\Services\System\OrderCountService;
use YiZan\Http\Controllers\Api\System\BaseController;

/**
 * 订单统计
 */
class OrdercountController extends BaseController 
{
    /**
     * 概况
     */
    public function total()
    {   

        $data = OrderCountService::total((int)$this->request('type'));
		$this->outputData($data);
    }
    

    /**
     * [orderNum 订单数量统计]
     */
    public function ordernum() {
        $data = OrderCountService::getOrderNumTotal
        (
            $this->request('beginTime'),
            $this->request('endTime')
        ); 
        $this->output($data);
    }
}