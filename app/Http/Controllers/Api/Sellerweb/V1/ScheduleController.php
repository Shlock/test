<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;
use YiZan\Services\Sellerweb\ScheduleService;

use Config;

class ScheduleController extends BaseController {
	/**
	 * 	日程列表
	 */
	public function lists() {
		$list = ScheduleService::getFourDayList($this->sellerId);
		$this->outputData($list);
	}


	/**
	 * [update 更新状态]
	 */
	public function update() 
    {
		$result = ScheduleService::updateStatus(
					$this->request('hours'),
					$this->request('status'),
					$this->sellerId
				);
        
        //$this->output($result);
        // 更新后全返列表数据
		$this->lists();
	}

    /**
     * 	员工日程列表
     */
    public function staffLists() {
        $list = ScheduleService::getStaffDayList(
            (int)$this->request('staffId'),
            $this->request('date')
        );
        $this->outputData($list);
    }

    /**
     * [updateStaff 更新员工预约状态]
     */
    public function updateStaff() {
        $result = ScheduleService::updateStaffStatus(
            $this->request('hours'),
            $this->request('status'),
            (int)$this->request('staffId')
        );
        $this->output($result);
    }
}