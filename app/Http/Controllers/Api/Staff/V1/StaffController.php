<?php 
namespace YiZan\Http\Controllers\Api\Staff;
use YiZan\Services\Staff\StaffService;

class StaffController extends BaseController {
	/**
	 * 更新员工信息
	 */
	public function update() {
        
		$result = StaffService::updateInfo(
                    $this->staffId,
                    $this->request('name'),
                    $this->request('avatar')
                );
		$this->output($result);
	}

    /**
     * 更新员工常驻地址
     */
    public function address() {
        $result = StaffService::address(
            $this->staffId,
            trim($this->request('address')),
            $this->request('mapPoint')
        );
        $this->output($result);
    }

    /**
     * 更新服务范围
     */
    public function range() {
        $result = StaffService::range(
            $this->userId,
            $this->request('mapPos')
        );
        $this->output($result);
    }
}