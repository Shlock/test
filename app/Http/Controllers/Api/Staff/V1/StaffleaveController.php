<?php 
namespace YiZan\Http\Controllers\Api\Staff;

use YiZan\Services\Staff\StaffLeaveService;
use Lang, Validator;

/**
 * 员工请假
 */
class StaffleaveController extends BaseController 
{
    /**
     * 创建员工请假
     */
    public function create() {
        $result = StaffLeaveService::create(
            $this->staffId,
            (int)$this->request('beginTime'),
            (int)$this->request('endTime'),
            trim($this->request('remark'))
        );

        $this->output($result);
    }

    /**
     * 员工请假列表
     */
    public function lists() {
        $data = StaffLeaveService::getList(
            $this->staffId,
            max((int)$this->request('page'), 1)
        );
        $this->outputData($data);
    }

    /**
     * 删除员工请假记录
     */
    public function delete() {
        $result = StaffLeaveService::delete(
            $this->request('ids'),
            $this->staffId
        );
        $this->output($result);
    }

    /**
     * 请假详情
     */
    public function detail() {
        $data = StaffLeaveService::detail(
            $this->staffId,
            (int)$this->request('id')
        );
        $this->outputData($data);
    }
}