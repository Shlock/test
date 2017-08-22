<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\TotalViewService;

/**
 * 订单统计
 */
class TotalviewController extends BaseController 
{
    /**
     * 概况
     */
    public function total()
    {   

        $data = TotalViewService::total();
        
		$this->outputData($data);
    }
    
}