<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Models\StaffLeave;
use YiZan\Services\System\StaffLeaveService;
use Lang, Validator;

/**
 * 员工请假
 */
class StaffleaveController extends BaseController 
{

    /**
     * 员工请假列表
     */
    public function lists() {
        $data = StaffLeaveService::getList(
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20)
        );
        $this->outputData($data);
    }

    /**
     * 删除员工请假记录
     */
    public function delete() {
        $result = StaffLeaveService::delete(
            (int)$this->request('id')
        );
        $this->output($result);
    }

    /**
     * 处理员工请假
     */
    public function dispose() {
        $result = StaffLeaveService::dispose(
            (int)$this->request('id'),
            $this->request('status'),
            trim($this->request('remark')),
            $this->adminId
        );
        $this->output($result);
    }

    /**
     * 请假详情
     */
    public function detail() {
        $data = StaffLeaveService::detail(
            (int)$this->request('id'),
            (int)$this->request('type'),
            (int)$this->request('isOrder'),
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20)
        );
        $this->outputData($data);
    }

    /**
     * 请假期间影响订单所有空闲人员
     */
    public function staff() {
        $data = StaffLeaveService::getStaffList(
            (int)$this->request('id'),
            (array)$this->request('orderIds'),
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20)
        );
        $this->outputData($data);
    }

    /**
     * 新增员工请假
     */
    public function create() {
        $result = StaffLeaveService::create(
            (int)$this->request('staffId'),
            (int)$this->request('beginTime'),
            (int)$this->request('endTime'),
            trim($this->request('remark'))
        );
        $this->output($result);
    }
}