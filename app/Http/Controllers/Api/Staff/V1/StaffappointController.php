<?php 
namespace YiZan\Http\Controllers\Api\Staff;
use YiZan\Services\Staff\StaffAppointService;
use YiZan\Utils\Time;

class StaffappointController extends BaseController {
    /**
     * 	员工日程列表
     */
    public function daylist() {
        $list = StaffAppointService::getStaffDayList(
            $this->staffId,
            $this->request('date')
        );
        $this->outputData($list);
    }

    /**
     * [updateStaff 更新员工预约状态]
     */
    public function update() {
        $result = StaffAppointService::updateStaffStatus(
            $this->staffId,
            $this->request('hours'),
            $this->request('status')
        );

        $this->output($result);
    }
}