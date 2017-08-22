<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Models\Staff\SellerStaff;
use YiZan\Services\SellerStaffService;
use YiZan\Services\GoodsService;
use DB;
/**
 * 服务人员
 */
class StaffController extends BaseController {
	/**
	 * 列表
	 */
	public function lists() {
		$data = SellerStaffService::getList(
				$this->city,
				max((int)$this->request('page'), 1),
				(int)$this->request('order'),
				(int)$this->request('sort'),
				$this->request('keywords'),
				$this->request('appointTime'),
				$this->request('mapPoint'),
				$this->request('appointMapPoint'),
				(int)$this->request('goodsId'),
				(int)$this->request('sellerId')
			);
		$this->outputData($data);
	}


    /**
     * Summary of staffgoods
     */
    public function staffgoods()
    {
        $staff = SellerStaffService::getStaff(
				(int)$this->request('staffId'), 
				$this->userId
			);
		if (!$staff) {
			$this->outputCode(30002);
		}
        
        $goods = GoodsService::getById((int)$this->request('goodsId'), $this->userId);
        
		if ($goods && 
            $goods->status == STATUS_ENABLED && 
            $goods->sale_status == STATUS_ENABLED) 
        {
			$this->outputData(["staff"=>$staff, "goods"=>$goods]);
		}
        
		$this->outputCode(40002);
    }
	/**
	 * 详细
	 */
	public function detail() {
		$data = SellerStaffService::getStaff(
				(int)$this->request('staffId'), 
				$this->userId
			);
		if (!$data) {
			$this->outputCode(30002);
		}
		$this->outputData($data);
	}

    /**
     * 洗车服务人员列表
     */
    public function carlists() 
    {
        $data = SellerStaffService::getCarList(
            (int)$this->request('districtId'),
            max((int)$this->request('page'),1)
        );
        $this->outputData($data);

    }

    /**
     * 洗车服务人员可预约时间
     */
    public function appointday() {
        $data = SellerStaffService::getAppointDay((int)$this->request('staffId'));
        $this->outputData($data);
    }
}