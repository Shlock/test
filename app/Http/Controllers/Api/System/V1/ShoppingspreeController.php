<?php
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\ShoppingSpreeService;
/**
 * 抢购活动
 */
class ShoppingspreeController extends BaseController{
    /**
     * 抢购活动设置
     */
    public function create(){
        $data = ShoppingSpreeService::shoppingSave(
            0,
            $this->request('name'),
            $this->request('startTime'),
            $this->request('endTime'),
            $this->request('image'),
            $this->request('type'),
            $this->request('sort'),
            $this->request('status')
        );
        
        $this->outputData($data);
    }
    
    public function update(){
        $data = ShoppingSpreeService::shoppingSave(
            $this->request('id'),
            $this->request('name'),
            $this->request('startTime'),
            $this->request('endTime'),
            $this->request('image'),
            $this->request('type'),
            $this->request('sort'),
            $this->request('status')
        );
    
        $this->outputData($data);
    }
    
    /**
     * 获取抢购活动设置
     */
    public function get(){
        $data = ShoppingSpreeService::getShoppingConfig(
            $this->request('id'),
            $this->request('type')
        );
        $data = $data ? $data->toArray() : array();
        $this->outputData($data);
    }
    /**
     * 设置抢购活动的状态
     */
    public function setStatus(){
        $data = ShoppingSpreeService::setStatus(
            $this->request('id',1),
            $this->request('status')
        );
        $this->outputData($data);
    }
    
    public function setPrice(){
        $data = ShoppingSpreeService::setPrice(
            $this->request('id'),
            $this->request('activity_id'),
            $this->request('price')
        );
        $this->outputData($data);
    }
    /**
     * 选择服务
     */
    public function setservice(){
        $data = ShoppingSpreeService::setService(
            $this->request('goods_id'),
            $this->request('activity_id'),
            $this->request('type')
        );
        $this->outputData($data);
    }
}
