<?php 
namespace YiZan\Http\Controllers\Api\System;
use YiZan\Services\System\StaffStimeService;
use YiZan\Utils\Time;

class StaffstimeController extends BaseController {
    /**
     *  员工服务时间设置
     */
    public function add() {
        $result = StaffStimeService::insert(
            (int)$this->request('staffId'),
            $this->request('weeks'),
            $this->request('hours')
        );
        $this->output($result);
    }

    /**
     * 员工服务时间列表
     */
    public function lists() {
        $list = StaffStimeService::getList((int)$this->request('id'));
        $this->outputData($list);
    }

    /**
     * 员工服务时间更新
     */
    public function update() {
        $result = StaffStimeService::update(
            (int)$this->request('staffId'),
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
           (int)$this->request('sellerId'),
           $this->request('id')
        );
        $this->outputData($data);
    }

    /**
     * 员工服务时间删除
     */
    public function delete() {
        $result = StaffStimeService::delete(
            (int)$this->request('sellerId'),
            $this->request('id')
        );
        $this->output($result);
    }

}