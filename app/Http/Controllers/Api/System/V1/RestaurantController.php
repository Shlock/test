<?php 
namespace YiZan\Http\Controllers\Api\System;


use YiZan\Services\System\RestaurantService;
use Lang;

/**
 * 餐厅管理
 */
class RestaurantController extends BaseController 
{
    /**
     * 餐厅列表
     */
    public function lists(){
        $data = RestaurantService::lists(
            $this->request('name'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        $this->outputData($data);
    }
    
    /**
     * 审核列表
     */
    public function applyLists()
    {
        $data = RestaurantService::applyLists(
            $this->request('name'),
            $this->request('tel'),
            $this->request('disposeStatus'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
		$this->outputData($data);
    }

    /**
     * 查看餐厅信息
     */
    public function lookat() {
        $data = RestaurantService::lookat(
            $this->request('id')
        );
        $this->output($data);
    }

    /**
     * 处理审核
     */
    public function dispose() {
        $data = RestaurantService::dispose(
            $this->adminId,
            $this->request('id'),
            $this->request('sellerId'),
            $this->request('disposeStatus'),
            $this->request('disposeResult')
        );
        $this->output($data);
    }

    /**
     * 删除餐厅
     */
    public function delete() {
        $data = RestaurantService::delete(
            $this->request('id')
        );
        $this->output($data);
    }

}