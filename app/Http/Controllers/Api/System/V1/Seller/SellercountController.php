<?php 
namespace YiZan\Http\Controllers\Api\System\Seller;

use YiZan\Services\System\SellerCountService;
use YiZan\Http\Controllers\Api\System\BaseController;

/**
 * 服务人员统计
 */
class SellercountController extends BaseController 
{
    /**
     * 概况
     */
    public function total()
    {   

        $data = SellerCountService::total
        (
           $this->request('beginTime'),
           $this->request('endTime')
        );
		$this->output($data);
    }
    

    
}