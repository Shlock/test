<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\GoodsService;
use Lang, Validator;

/**
 * 菜品管理
 */
class GoodsController extends BaseController 
{
    /**
     * 菜品列表
     */
    public function lists() {
        $data = GoodsService::getSystemList(
            $this->request('sellerId'),
            $this->request('type'),
            $this->request('name'),
            (int)$this->request('cateId'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
        );
        
		$this->outputData($data);
    }

    /**
     * 更新菜品
     */
    public function update() {
        
        $result = GoodsService::systemUpdate
        (
            intval($this->request('id')),
            intval($this->request('sellerId')),
            $this->seller->type,
            intval($this->request('type')),  
            $this->request('staffIds'),
            $this->request('name'),
            doubleval($this->request('price')), 
            intval($this->request('cateId')), 
            $this->request('brief'),
            $this->request('images'),
            intval($this->request('duration')),
            $this->request('unit'), 
            intval($this->request('stock')),
            intval($this->request('buyLimit')),
            $this->request('norms'),
            intval($this->request('status')),
            intval($this->request('sort')) 
        );
        $this->output($result);
    }

    /**
     * 添加菜品
     */
    public function create() {
        $result = GoodsService::save(
            0,
            (int)$this->request('restaurantId'),
            (int)$this->request('typeId'),
            $this->request('name'),
            $this->request('image'),
            $this->request('joinService'),
            $this->request('price'),
            $this->request('oldPrice'),
            $this->request('status'),
            $this->request('sort')
        );
        $this->output($result);
    }

    /**
     * 菜品列表
     */
    public function goodslist() {
        $data = GoodsService::goodslist(
            (int)$this->request('restaurantId'),
            $this->request('name'),
            $this->request('status'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
        );
        
        $this->outputData($data);
    }

    /**
     * 菜品搜索
     */
    public function search() {
        $data = GoodsService::searchGoods($this->request('name'), (int)$this->request('sellerId'));
        $this->outputData($data);
    }

    /**
     * 获取菜品
     */
    public function get() {
        $goods = GoodsService::getSystemGoodsById(intval($this->request('id')));
        $this->outputData($goods == false ? [] : $goods->toArray());
    }

    /**
     * 删除菜品
     */
    public function delete() {
        $result = GoodsService::deleteSystem(intval($this->request('id')));
        $this->output($result);
    }

    /**
     * 审核菜品
     */
    public function auditGoods() {
        $result = GoodsService::auditGoods(
                (int)$this->request('id'),
                (int)$this->request('status'),
                (int)$this->request('isSystem'),
                $this->request('disposeResult', ''),
                $this->request('cityPrices', []),
                $this->adminId
            );
        $this->output($result);
    }

    /**
     * 更改菜品的参与菜品
     */
    public function joinService() {
        $result = GoodsService::joinService(
                (int)$this->request('id'),
                (int)$this->request('joinService')
            );
        $this->output($result);
    }

    /**
     * 菜品审核
     */
    public function dispose() {
        $result = GoodsService::dispose(
            (int)$this->request('id'),
            $this->request('status'),
            $this->request('remark')
        );
        $this->output($result);
    }

    /**
     * 服务列表
     */
    public function servicelists() {
        $data = GoodsService::getServiceList(
            (int)$this->request('type'),
            trim($this->request('name')),
            max((int)$this->request('page'), 1),
            (int)$this->request('pageSize', 20)
        );

        $this->outputData($data);
    }

    /**
     * 增加服务
     */
    public function createservice() {
        $result = GoodsService::saveService(
            0,
            (int)$this->request('type'),
            trim($this->request('name')),
            $this->request('image'),
            $this->request('brief'),
            (int)$this->request('sort')
        );
        $this->output($result);
    }


    /**
     * 更新服务
     */
    public function updateservice() {
        $result = GoodsService::saveService(
            (int)$this->request('id'),
            (int)$this->request('type'),
            trim($this->request('name')),
            $this->request('image'),
            $this->request('brief'),
            (int)$this->request('sort')
        );
        $this->output($result);
    }

    /**
     * 删除服务
     */
    public function deleteservice() {
        $result = GoodsService::deleteService($this->request('id'));
        $this->output($result);
    }
}