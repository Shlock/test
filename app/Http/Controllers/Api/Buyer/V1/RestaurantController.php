<?php 
namespace YiZan\Http\Controllers\Api\Buyer;


use YiZan\Services\Buyer\RestaurantService;
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
            (int)$this->userId,
            (int)$this->request('sort'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        $this->outputData($data);
    }

    /**
     * 获取餐厅信息 和搜藏信息
     */
    public function get() {
        $data = RestaurantService::lookat(
            (int)$this->userId,
            $this->request('restaurantId')
        );
        $this->output($data);
    }

}