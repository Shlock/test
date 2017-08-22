<?php 
namespace YiZan\Http\Controllers\Api\Staff;
use YiZan\Services\SellerStaffService;
use Config;

class RateController extends BaseController {
	/**
     * 卖家的评价统计
	 */
	public function statistics() 
    {
		$data = SellerStaffService::getStaff($this->staffId);

		$result = $data ? $data["extend"] : [];
		
		$this->outputData($result); 
	}
}