<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\StaffLeaveService;
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
            $this->sellerId,
            max((int)$this->request('page'), 1)
        );
        $this->outputData($data);
    }

    /**
     * 删除员工请假记录
     */
    public function delete() {
        $result = StaffLeaveService::delete(
            $this->request('id'),
            $this->sellerId
        );
        $this->output($result);
    }

    /**
     * 处理员工请假
     */
    public function dispose() {
        $result = StaffLeaveService::dispose(
            $this->request('id'),
            $this->request('agree'),
            $this->sellerId
        );
        $this->output($result);
    }
}