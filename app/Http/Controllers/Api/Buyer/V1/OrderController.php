<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\OrderService;
use YiZan\Services\SellerStaffService;
use YiZan\Services\GoodsService;
use YiZan\Models\UserCard;
use View;
/**
 * 订单
 */
class OrderController extends UserAuthController {
	 /**
	 * 订单详细
	 */
	public function detail() {
		$data = OrderService::getOrderById($this->userId, (int)$this->request('id'));
		if (!$data) {
			$this->outputCode(60014);
		}
		$this->outputData($data);
	}

	/**
	 * 列表
	 */
	public function lists() {
		$data = OrderService::getList($this->userId, (int)$this->request('status'), max((int)$this->request('page'), 1));
		$this->outputData($data);
	}

	/**
	 * 取消订单
	 */
	public function cancel(){
	    $result = OrderService::cancelOrder($this->userId, $this->request('id'),$this->request('cancelRemark'));
	    $this->output($result);
	}
	
	/**
	 * 订单付款
	 */
	public function pay(){
	    $result = OrderService::payOrder($this->userId, $this->request('id'), $this->request('payment'), $this->request('extend'));
	    $this->output($result);
	}
	/**
	 *货到付款
	 */
	public function delivery(){
	    $result = OrderService::delivery($this->userId, $this->request('orderId'));
	    $this->output($result);
	}
	/**
	 * 订单确认完成
	 */
	public function confirm(){
	    $result = OrderService::confirmOrder($this->userId, $this->request('id'));
	    $this->output($result);
	}

	/**
	 * 会员催单
	 */
	public function urge(){
	    $result = OrderService::urgeOrder($this->userId, $this->request('id'));
	    $this->output($result);
	}
	
	/**
	 * 订单删除
	 */
	public function delete(){
        die;//暂先隐藏此功能
	    $result = OrderService::deleteOrder($this->userId, $this->request('id'));
	    $this->output($result);
	}
	/**
	 * [refund 申请退款]
	 */
	public function refund(){
	    $result = OrderService::refund(
	        $this->userId,
	        $this->request('orderId'),
	        $this->request('refundImages'),
	        $this->request('refundContent')
	    );
	    $this->output($result);
	}
	/**
	 * [refund 服务人员完成状态]
	 */
	public function complete(){
	    $result = OrderService::completeOrder($this->userId, $this->request('orderId'),$this->request('code'));
	    $this->output($result);
	}

	/**
	 * [compute 订单计算]
	 *
	 */
    public function compute(){
        $data = OrderService::orderCompute(
            $this->userId,
            (array)$this->request('cartIds'),
            $this->request('promotionSnId')
        );
        $this->outputData($data);
    }

    /**
     * 下单
     */
    public function create(){
        if($this->request('cardNo')+0 > 0) {
            UserCard::insert(array(
                'user_id' => $this->userId,
                'card_id' => $this->request('cardNo'),
                'money' => 0,
                'ctime' => time(),
                'status' => 0
            ));
        }
        $result = OrderService::create(
            $this->userId,
            (array)$this->request('cartIds'),
            (int)$this->request('addressId'),
            $this->request('giftContent'),
            $this->request('invoiceTitle'),
            $this->request('buyRemark'),
            $this->request('appTime'),
            $this->request('payment'),
            (int)$this->request('promotionSnId')
        );
        $this->output($result);
    }


    /**
     * 退款详情
     */
    public function refundview() {
        $result = OrderService::refundDetail($this->userId,(int)$this->request('orderId'));
        $this->outputData($result);
    }

    /**
     * 退款详情页面
     */
    public function viewrefund() {
        $data = OrderService::refundDetail($this->userId,(int)$this->request('orderId'));
        View::share('data', $data);
        return View::make('api.wap.order.refund');
    }

    /**
     * 不显示优惠信息
     */
    public function notshow(){
        $result = OrderService::notshow($this->userId,(int)$this->request('orderId'));
        $this->outputData($result);
    }
}