<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\DoorAccessService;
use Input;
/**
 * 门禁
 */
class DooraccessController extends BaseController 
{
	/**
     * 添加门禁
	 */
    public function save() {
        $result = DoorAccessService::save(
            $this->request("id"),
            $this->sellerId,
            $this->request('districtId'),
            $this->request('name'),
            $this->request('pid'),
            $this->request('buildId'),
            (int)$this->request('type'), 
            $this->request('remark'),
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
     * [delete 删除门禁] 
     */
    public function delete(){
    	$result = DoorAccessService::delete($this->sellerId, $this->request('id'));
    	$this->output($result);
    }

    /**
     * [lists 门禁列表] 
     */
    public function lists(){
        $result = DoorAccessService::getLists(
            $this->sellerId, 
            (int)$this->request('isTotal'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20) 
            );
        $this->outputData($result);
    }

    /**
     * [get 获取门禁] 
     */
    public function get(){
        $result = DoorAccessService::getById($this->sellerId, $this->request('id')); 
        $this->outputData($result);
    }

}