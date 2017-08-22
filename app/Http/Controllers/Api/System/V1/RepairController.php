<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\RepairService;
use Input;
/**
 * 报修管理
 */
class RepairController extends BaseController 
{
   	/**
     * 列表
	 */
	public function lists()
    {
        $data = RepairService::getLists(
            (int)$this->request('sellerId'),
            $this->request('name'), 
            $this->request('build'),
            $this->request('roomNum'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20),
            (int)$this->request('status')
        );
        
		$this->outputData($data);
    }

    /**
     * 添加
     */
    public function save()
    {
        $result = RepairService::save(intval($this->request('id')), intval($this->request('sellerId')), intval($this->request('status')));
        
        $this->output($result);
    }

    /**
     * 详情 
     */
    public function get()
    {
        $result = RepairService::getById(intval($this->request('id')));
        
        $this->outputData($result);
    } 

}