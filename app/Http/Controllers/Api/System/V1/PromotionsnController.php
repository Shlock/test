<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\PromotionSnService;
use YiZan\Utils\Time;

/**
 * 优惠券发放管理
 */
class PromotionsnController extends BaseController 
{
    /**
     * 发放列表
     */
    public function lists() {
        $data = PromotionSnService::getLists(
                trim($this->request('sn')),
                (int)$this->request('promotionId'), 
                (int)$this->request('status'),
                trim($this->request('actName')),
                trim($this->request('mobile')),
                (int)Time::toTime($this->request('beginTime')),
                (int)Time::toTIme($this->request('endTime')),
                (int)$this->request('actType'),
                max((int)$this->request('page'), 1), 
                max((int)$this->request('pageSize'), 20)
            );
        $this->outputData($data);
    }

    public function userlists() {
        $data = PromotionSnService::getUserLists(
                (int)$this->request('userId'), 
                (int)$this->request('sellerId')
            );
        $this->outputData($data);
    }

    /**
     * 删除发放
     */
    public function delete() {
        $result = PromotionSnService::deletePromotionSn($this->request('id'));
        $this->output($result);
    }
}