<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\ShoppingService;

/**
 * 购物车
 */
class ShoppingController extends BaseController {

    /**
     * 加入购物车
     */
	public function save() {
        $data = ShoppingService::save(
        	(int)$this->userId,
        	(int)$this->request('goodsId'),
        	(int)$this->request('normsId'),
        	(int)$this->request('num') ,
            $this->request('serviceTime')
        );
        $this->output($data);
    }

    /**
     * 清空购物车
     */
    public function delete() {
        $data = ShoppingService::delete(
            (int)$this->userId,
            (int)$this->request('id')
        );
        $this->outputData($data);
    }

    /**
     * 获取购物车
     */
    public function lists() {
        $data = ShoppingService::lists(
            (int)$this->userId
        );
        $this->outputData($data);
    }

    /**
     * 修改购物车
     */
    public function update() {
        $data = ShoppingService::updateCart(
            (int)$this->request('id'),
            (int)$this->request('num')
            );
        $this->outputData($data);
    }

    /**
     * 根据购物车编号获取信息
     */
    public function getCartList(){
        $data = ShoppingService::getCartList(
            $this->userId,
            (array)$this->request('ids')
        );
        $this->output($data);
    }
    
}