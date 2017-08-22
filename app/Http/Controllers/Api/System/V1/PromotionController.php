<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\PromotionService;
use YiZan\Utils\Time;

/**
 * 优惠券管理，洗车券
 */
class PromotionController extends BaseController {
    /**
     * 优惠券列表
     */
    public function lists(){
        $data = PromotionService::getLists(
                trim($this->request('name')),
                trim($this->request('sellerName')),
                (int)Time::toTime($this->request('beginTime')),
                (int)Time::toTime($this->request('endTime')),
                (int)$this->request('useType'),
                (double)$this->request('money'),
                max((int)$this->request('page'), 1),
                max((int)$this->request('pageSize'), 20),
                (int)Time::toTime($this->request('startTime')),
                (int)Time::toTime($this->request('endTime2'))
            );
        $this->outputData($data);
    }

    /**
     * 获取优惠券
     */
    public function get() {
        $data = PromotionService::getPromotion((int)$this->request('id'));
        $this->outputData($data);
    }

    /**
     * 添加优惠券
     */
    public function save() {
        $result = PromotionService::savePromotion(
                (int)$this->request('id'),
                trim($this->request('name')),
                (double)round($this->request('money'),1),
                max((int)$this->request('type'),1),
                (int)Time::toTime($this->request('beginTime')),
                (int)Time::toTime($this->request('endTime')),
                (int)$this->request('expireDay'),
                (array)$this->request('unableDate'),
                (int)$this->request('useType'),
                $this->request('sellerCateIds'),
                (int)$this->request('sellerId'),
                (double)round($this->request('limitMoney'),2),
                trim($this->request('brief'))
            );
        $this->output($result);
    }


    /**
     * 发放优惠券
     */
    public function send() {
        $result = PromotionService::send(
                (int)$this->request('promotionId'),
                trim($this->request('prefix')),
                (int)$this->request('number'),
                $this->request('userIds')
            );
        $this->output($result);
    }
    
    /**
     * 更新优惠券状态
     */
    public function updatestatus() {
        PromotionService::updateStatus(
                (int)$this->request('id'),
                (int)$this->request('status')
            );
        $this->outputCode(0);
    }

    /**
     * 删除优惠券
     */
    public function delete() {
        $status = PromotionService::deletePromotion((int)$this->request('id'));
        $this->output($status);
    }

    /**
     * 添加优惠券
     */
    public function addcarticket() {
        $result = PromotionService::savecarticket(
                (int)$this->request('id',0),
                (int)$this->request('sellerId'),
                $this->request('name'), 
                $this->request('type'), 
                (double)$this->request('data'),
                $this->request('conditionType'),
                (float)$this->request('sellPrice'),
                (int)$this->request('expireDay'),
                $this->request('beginTime'),
                $this->request('endTime'),
                (int)$this->request('status'),
                (int)$this->request('sort'),
                (int)$this->request('timetype'),
                (int)$this->request('overlay'),
                (int)$this->request('cate'),
                (int)$this->request('cateId2')
            );
        $this->output($result);
    }

    /**
     * 获取优惠券列表
     */
    public function getPromotionregisterUpdate(){
        $result = PromotionService::getPromotionLists();

        $this->outputData($result);
    }

}