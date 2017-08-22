<?php 
namespace YiZan\Http\Controllers\Api\Buyer\User;

use YiZan\Http\Controllers\Api\Buyer\UserAuthController;
use YiZan\Services\Buyer\PromotionService;

class PromotionController extends UserAuthController {
	/**
	 * 获取会员的优惠券列表
	 */
	public function lists() {
		$data = PromotionService::getPromotionList(
				$this->userId,
				(int)$this->request('status'),
				max((int)$this->request('page'), 1),
				(int)$this->request('sellerId'),
                (double)round($this->request('money'),2)
			);
		$this->outputData($data);
	}


	/**
	 * 优惠券兑换
	 */
	public function exchange() {
		$result = PromotionService::exchangePromotion(
                    $this->userId,
                    $this->request('sn'),
                    (int)$this->request('type')

                );
		$this->output($result);
	}

	/**
	 * 优惠券兑换
	 */
	public function receive() {
		$result = PromotionService::receivePromotion($this->userId, (int)$this->request('id'));
		$this->output($result);
	}

    /**
     * 获取可用的第一个优惠券
     */
    public function first() {
        $result = PromotionService::getFirst($this->userId);
        $this->outputData($result);
    }

    /**
     * 获取优惠券详情
     */
    public function get() {
        $result = PromotionService::getById($this->userId,(int)$this->request('id'));
        $this->outputData($result);
    }

    /**
     * 发优惠券
     */
    public function send(){
        $result = PromotionService::shareSend($this->userId,(int)$this->request('orderId'),(int)$this->request('activityId'),(int)$this->request('promotionId'));
        $this->outputData($result);
    }

}