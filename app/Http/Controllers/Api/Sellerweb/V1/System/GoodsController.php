<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb\System;

use YiZan\Services\SystemGoodsService;
use YiZan\Http\Controllers\Api\Sellerweb\BaseController;
use Lang;

/**
 * 服务管理
 */
class GoodsController extends BaseController {
    /**
     * 通用服务列表
     */
    public function lists() {
        $data = SystemGoodsService::getListForSellerAdd( 
            $this->sellerId,
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20),  
            $this->request('name'),
            $this->request('cateId'),
            $this->request('status')
        );  
		$this->outputData($data);
    }

    /**
     * 添加服务
     */
    public function create(){
        $result = SystemGoodsService::saveGoods(
            0,
            $this->request('name'),
            (int)$this->request('priceType'), 
            (double)$this->request('price'), 
            (double)$this->request('marketPrice'),
            (int)$this->request('cateId'), 
            $this->request('brief'),
            $this->request('images', []),
            (int)$this->request('duration'),
            (int)$this->request('sort', 100),
            $this->request('cityPrices', [])
        );
        $this->output($result);
    }
    /**
     * 获取通用服务
     */
    public function get() {
        $data = SystemGoodsService::getById((int)$this->request('id')); 
        $result = $data ? $data->toArray() : []; 
        $this->outputData($result);
    }

    /**
     * 更新服务
     */
    public function update() {
        $result = SystemGoodsService::saveGoods(
            (int)$this->request('id'),
            $this->request('name'),
            (int)$this->request('priceType'), 
            (double)$this->request('price'), 
            (double)$this->request('marketPrice'),
            (int)$this->request('cateId'), 
            $this->request('brief'),
            $this->request('images', []),
            (int)$this->request('duration'),
            (int)$this->request('sort', 100),
            $this->request('cityPrices', [])
        );
        $this->output($result);
    }

    /**
     * 删除服务
     */
    public function delete() {
        $result = SystemGoodsService::deleteGoods($this->request('id'));
        $this->output($result);
    }

    /**
     * 更新服务状态
     */
    public function updateStatus() {
        $result = SystemGoodsService::updateStatus(
                (int)$this->request('id'),
                abs((int)$this->request('status'))
            );
        $this->output($result);
    }
}