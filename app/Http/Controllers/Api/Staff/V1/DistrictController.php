<?php 
namespace YiZan\Http\Controllers\Api\Staff;

use YiZan\Services\Buyer\SellerStaffDistrictService; 
use Lang, Validator;

class DistrictController extends BaseController {
    
    /**
	 * 员工添加商圈
	 */
	public function create() 
    {
        $result = SellerStaffDistrictService::save($this->staffId, $this->request('districtId')); 
		$this->output($result);
	}

    /**
     * [delete 删除商圈] 
     */
    public function delete(){
        $result = SellerStaffDistrictService::delete($this->staffId, $this->request('districtId')); 
        $this->output($result);
    }

}