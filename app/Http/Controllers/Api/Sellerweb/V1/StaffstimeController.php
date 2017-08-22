<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;
use YiZan\Services\Sellerweb\StaffStimeService;
use YiZan\Utils\Time;

class StaffstimeController extends BaseController {
    public function add() {
        $result = StaffStimeService::insert(
            $this->sellerId,
            $this->request('weeks'),
            $this->request('hours')
        );
        $this->output($result);
    }

    /**
     * 员工服务时间列表
     */
    public function lists() {
        $list = StaffStimeService::getList($this->sellerId);
        $this->outputData($list);
    }

    /**
     * 员工服务时间更新
     */
    public function update() {
        $result = StaffStimeService::update(
            $this->sellerId,
            $this->request('id'),
            $this->request('weeks'),
            $this->request('hours')
        );
        $this->output($result);
    }

    /**
     * 员工服务时间详情
     */
    public function edit() {
        $data = StaffStimeService::detail(
           $this->sellerId,
           $this->request('id')
        );
        $this->outputData($data);
    }

    /**
     * 员工服务时间删除
     */
    public function delete() {
        $result = StaffStimeService::delete(
            $this->sellerId,
            $this->request('id')
        );
        $this->output($result);
    }

}