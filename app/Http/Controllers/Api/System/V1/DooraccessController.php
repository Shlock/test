<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\DoorAccessService;
use Lang, Validator;

/**
 * 门禁管理
 */
class DooraccessController extends BaseController {

    /**
     * 门禁列表
     */
    public function lists() {
        $data = DoorAccessService::getLists(
            $this->request('sellerId'),
            $this->request('districtId'),
            $this->request('name'),
            $this->request('pid'),
            (int)$this->request('isTotal'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
        );
        
		$this->outputData($data);
    } 

    /**
     * 添加门禁
     */
    public function save() {
        $result = DoorAccessService::save(
            $this->request("id"),
            (int)$this->request('sellerId'),
            (int)$this->request('districtId'),
            $this->request('name'),
            $this->request('pid'),
            $this->request('buildId'), 
            $this->request('remark'),
            (int)$this->request('type'),
            $this->request('installLockName'), 
            $this->request('installAddress'),
            $this->request('installGps'), 
            $this->request('installWork'),
            $this->request('installTelete'), 
            $this->request('installComm')
        );
        $this->output($result);
    }  

    /**
     * 获取门禁
     */
    public function get() {
        $result = DoorAccessService::getById(intval($this->request('id')));
        $this->outputData($result);
    }

    /**
     * 删除门禁
     */
    public function delete() {
        $result = DoorAccessService::delete(intval($this->request('id')));
        $this->output($result);
    }
    
}