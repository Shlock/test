<?php 
namespace YiZan\Http\Controllers\Api\Buyer\Collect;

use YiZan\Http\Controllers\Api\Buyer\UserAuthController;
use YiZan\Services\Buyer\CollectService;
use Lang;

/**
 * 餐厅收藏
 */
class RestaurantController extends UserAuthController {

    /**
     * 收藏列表
     */
    public function lists() {
        $data  = CollectService::restaurantList($this->userId, max($this->request('page'), 1));
        $this->outputData($data);
    }

	/**
	 * 添加收藏
	 */
	public function create() {
		$result = CollectService::collectRestaurant($this->userId, (int)$this->request('id'));
		$this->output($result);
	}

	/**
	 * 删除收藏
	 */
	public function delete() {
        $result = CollectService::deleteRestaurant($this->userId, (int)$this->request('id'));
        $this->output($result);
	}
}