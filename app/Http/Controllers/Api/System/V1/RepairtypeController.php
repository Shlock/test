<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\RepairTypeService;
use Input;
/**
 * 报修类型管理
 */
class RepairtypeController extends BaseController 
{
   	/**
     * 列表
	 */
	public function lists() {
        $data = RepairTypeService::getList(
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		$this->outputData($data);
    }

    /**
     * 保存 
     */
    public function save() {
        $result = RepairTypeService::save((int)$this->request('id'), $this->request('name'), intval($this->request('sort')));
        
        $this->output($result);
    }

    /**
     * 详情 
     */
    public function get() {
        $result = RepairTypeService::getById(intval($this->request('id')));
        
        $this->outputData($result);
    }

    /**
     * 删除 
     */
    public function delete() {
        $result = RepairTypeService::delete(intval($this->request('id')));
        
        $this->output($result);
    }
 
}