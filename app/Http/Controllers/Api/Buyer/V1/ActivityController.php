<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\ActivityService;
use Lang, Validator;

/**
 * 活动
 */
class ActivityController extends UserAuthController 
{
    /**
     * 活动列表
     */
    public function lists()
    {
        $article = ActivityService::getList(
        	$this->request('type'),
        	$this->request('name'),
        	max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 50)
        	);
        $this->outputData($article['list']);
    }

    /**
     * 创建支付日志
     */
    public function pay(){
        $result = ActivityService::payOrder(
            $this->userId,
            $this->request('activityId'), 
            $this->request('payment')
        );
        $this->output($result);
    }

    /**
     * 获取一张优惠券
     */
    public function getPromotion(){
        $result = ActivityService::getPromotion(
            (int)$this->request('userId'),
            (int)$this->request('activityId')
        );
        $this->output($result);
    }

    /**
     * 检测用户是否注册
     */
    public function checkuser(){
        $data = ActivityService::checkUser(
            $this->request('mobile')
        );
        $this->outputData($data);
    }

    /**
     * 获取活动
     */
    public function getshare(){
        $Activity = ActivityService::getshare((int)$this->request('orderId'),(int)$this->request('activityId'));
        $this->output($Activity);
    }

    /**
     * 获取活动
     */
    public function get(){
        $Activity = ActivityService::getById((int)$this->request('activityId'));
        $this->outputData($Activity == false ? [] : $Activity->toArray());
    }


}