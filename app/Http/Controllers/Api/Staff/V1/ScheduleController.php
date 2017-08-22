<?php 
namespace YiZan\Http\Controllers\Api\Staff;
use YiZan\Services\Staff\ScheduleService;

use Config;

class ScheduleController extends BaseController {
	/**
	 * 	日程列表
	 */
	public function lists() {
		$list = ScheduleService::getFourDayList($this->staffId);
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
					$this->staffId
				);
        
        //$this->output($result);
        // 更新后全返列表数据
		$this->lists();
	}
}