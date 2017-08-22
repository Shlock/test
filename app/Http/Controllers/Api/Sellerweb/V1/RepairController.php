<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\RepairService;
use Lang, Validator;

/**
 * 报修
 */
class RepairController extends BaseController 
{
    /**
     * 报修列表
     */
    public function lists()
    {
        $data = RepairService::getLists(
            $this->sellerId,
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
     * 处理报修
     */
    public function save()
    {
        $result = RepairService::save(
            (int)$this->request('id'),
            $this->sellerId,
            (int)$this->request('status')
        );
        
        $this->output($result);
    }


    /**
     * 删除报修
     */
    public function delete()
    {
        $result = RepairService::delete(intval($this->request('id')));
        
        $this->output($result);
    }



    public function get()
    {
        $result = RepairService::get(intval($this->request('id')));
        $this->outputData($result);
    }


}

