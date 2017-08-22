<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\ActivityService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 活动管理
 */
class ActivityController extends BaseController 
{
    /**
     * 活动列表
     */
    public function lists()
    {
        $data = ActivityService::getList
        (
            $this->request('name'),
            (int)$this->request('status'),
            (int)$this->request('startTime'),
            (int)$this->request('endTime'),
            (int)$this->request('type'),
            $this->request('page'),
            max((int)$this->request('pageSize'), 20)
        );
		$this->outputData($data);
    }
    /**
     * 添加活动
     */
    public function create()
    {
        $result = ActivityService::create
        (
            $this->request('name'),
            $this->request('image'),
            $this->request('startTime'),
            $this->request('endTime'),
            $this->request('content'),
            $this->request('promotion'),
            $this->request('type'),
            intval($this->request('sort')),
            intval($this->request('status'))
        );
        
        $this->output($result);
    }
    /**
     * 获取活动
     */
    public function get()
    {
        $Activity = ActivityService::getById(intval($this->request('id')));
        
        $this->outputData($Activity == false ? [] : $Activity->toArray());
    }

    /**
     * 更新活动
     */
    public function update()
    {
        $result = ActivityService::update
        (
            $this->request('id'),
            $this->request('name'),
            $this->request('image'),
            $this->request('startTime'),
            $this->request('endTime'),
            $this->request('content'),
            $this->request('promotion'),
            $this->request('type'),
            intval($this->request('sort')),
            intval($this->request('status')),
            (int)$this->request('promotionId')
        );
        
        $this->output($result);
    }


    /**
     * 保存注册活动
     */
    public function save()
    {
        $result = ActivityService::saveActivityReg
        (
            (int)$this->request('id'),
            $this->request('name'),
            $this->request('startTime'),
            $this->request('endTime'),
            (int)$this->request('status'),
            (int)$this->request('promotionId'),
            (int)$this->request('num')
        );

        $this->output($result);
    }

    /**
     * 删除活动
     */
    public function delete()
    {
        $result = ActivityService::delete(intval($this->request('id')));
        $this->output($result);
    }

    /**
     * 获取分享活动
     */
    public function activity(){
        $result = ActivityService::getActivity(
            (int)$this->request('id'),
            (int)$this->request('type')
        );
        $this->output($result);
    }

    /**
     * 获取活动的优惠券
     */
    public function getPromotionLists(){
        $result = ActivityService::getPromotionLists();
        $this->output($result);
    }

    /**
     * 更新注册活动
     */
    public function registerUpdate()
    {
        $result = ActivityService::registerUpdate
        (
            $this->request('id'),
            $this->request('name'),
            $this->request('startTime'),
            $this->request('endTime'),
            $this->request('promotion'),
            $this->request('type'),
            intval($this->request('status'))
        );

        $this->output($result);
    }

    /**
     * 更新注册活动
     */
    public function shareUpdate()
    {
        $result = ActivityService::shareUpdate
        (
            $this->request('id'),
            $this->request('name'),
            $this->request('bgimage'),
            $this->request('startTime'),
            $this->request('endTime'),
            (int)$this->request('promotionId'),
            (int)$this->request('num'),
            $this->request('money'),
            (int)$this->request('sharePromotionNum'),
            $this->request('title'),
            $this->request('detail'),
            $this->request('image'),
            $this->request('buttonName'),
            $this->request('buttonUrl'),
            $this->request('brief'),
            $this->request('count'),
            $this->request('type'),
            intval($this->request('status'))
        );

        $this->output($result);
    }

}