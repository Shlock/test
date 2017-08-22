<?php 
namespace YiZan\Http\Controllers\Api\System\Order;

use YiZan\Services\System\OrderComplainService;
use YiZan\Http\Controllers\Api\System\BaseController;

class ComplainController extends BaseController {
	/**
	 * 订单举报列表
	 */
	public function lists() {  
		$data = OrderComplainService::getSystemLists(
            $this->request('sn'),
            $this->request('beginTime'),
            $this->request('endTime'),
            intval($this->request('status')),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)	
		);
		$this->outputData($data);
	}

    /**
     * 举报处理
     */
    public function dispose()
    {
        $result = OrderComplainService::dispose(intval($this->request('id')), (int)$this->request('status'), $this->request('content'), $this->adminId);
        
        $this->output($result);
    }
	
}