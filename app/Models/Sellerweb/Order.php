<?php namespace YiZan\Models\Sellerweb;

class Order extends \YiZan\Models\Order 
{
	protected $visible = ['id', 'sn', 'OrderGoods', 'user', 'order_type','user_id', 'goods_duration','appoint_time', 'promotion', 'name', 'mobile',
		'address', 'pay_fee', 'pay_type', 'discount_fee', 'total_fee', 'buy_remark', 'is_rate','status',  'orderStatusStr', 
		'pay_status','pay_time','confirm_time', 'create_time','service_start_time','service_finish_time', 'appoint_day','appoint_hour','goods_name','userPayLog','staff','reservation_code','is_to_store',
        "isCanDelete", "isCanRate", "isCanComplain", "isCanCancel", "isCanRefund", "isCanPay", "isCanContact", "isCanConfirm", "statusFlowImage", "statusNameDate","app_time","invoice_remark", "gift_remark",
        'seller_confirm_time', 'refund_time', 'deposit_refund_time', 'buyer_cancel_time','service_fee','restaurant',"isReceivability",'isRefuseToOrderRestaurant','service_content','statusNameDateRestaurant',
        'service_content','isCanRefundSeller','seller_staff_id','cancel_remark', 'isCanAccept','promotion_sn_id','drawn_fee','seller_id','discount_fee','seller_fee','freight','goods_fee','isCashOnDelivery', 'isCanStartService', 'isCanFinish', 'year_name', 'totalPayfee', 'totalNum', 'totalDrawnfee', 'totalOnline','totalCash','totalDiscountFee','totalSellerFee','orderStatus'];

    protected $appends = array('mapPoint', 'orderStatusStr', "isCanDelete", "isCanRate", "isCanComplain", "isCanCancel", "isCanRefund", "isCanPay", "isCanContact", "isCanConfirm", "statusFlowImage", "statusNameDate","isReceivability",'isCanRefundSeller', 'isCanAccept','isCashOnDelivery', 'isCanStartService', 'isCanFinish', 'orderStatus');
    
    public function goods(){
        return $this->belongsTo('YiZan\Models\Goods', 'goods_id', 'id');
    }
    
    public function orderGoods(){
        return $this->hasMany('YiZan\Models\OrderGoods', 'order_id', 'id');
    }
    
    public function user(){
        return $this->belongsTo('YiZan\Models\User');
    }
    public function restaurant(){
        return $this->belongsTo('YiZan\Models\Restaurant','restaurant_id','id');
    }

    
    /**
     * 是否可以完成服务(卖家员工)
     * @return bool
     */
    public function getIsCanFinishAttribute()
    {
        $status     = $this->attributes['status'];
        
        return $status == ORDER_STATUS_START_SERVICE;
    }

    public function getIsCanStartServiceAttribute()
    {
        $status     = $this->attributes['status'];
        
        return $status == ORDER_STATUS_AFFIRM_SELLER;
    }
    /**
     * 是否操作退款(商家)
     * @return bool
     */
    public function getIsCanRefundSellerAttribute()
    {
        $payFee    = $this->attributes['pay_fee'];
        $status     = $this->attributes['status'];
        return 
         ($status == ORDER_STATUS_CANCEL_ADMIN && $payFee >= 0.0001) ||
         ($status == ORDER_STATUS_USER_DELETE  && $payFee >= 0.0001) ||
         ($status == ORDER_STATUS_CANCEL_USER  && $payFee >= 0.0001) ||
         ($status == ORDER_STATUS_FINISH_USER  && $payFee >= 0.0001) ||
         ($status == ORDER_REFUND_ADMIN_AGREE  && $payFee >= 0.0001) ||
         ($status == ORDER_REFUND_ADMIN_REFUSE && $payFee >= 0.0001);
    }
	
	/**
     * 状态流程图片(无后缀和路径)
     * @return string
     */
    public function getStatusFlowImageAttribute()
    {
        $status = $this->attributes['status'];

        //订单删除状态处理
        if ($status == ORDER_STATUS_USER_DELETE ||
            $status == ORDER_STATUS_SELLER_DELETE ||
            $status == ORDER_STATUS_ADMIN_DELETE
        ) {
            $payStatus = $this->attributes['pay_status'];
            $autoFinishTime = (int)$this->attributes['auto_finish_time'];
            $buyerFinishTime = (int)$this->attributes['buyer_finish_time'];
            $staffFinishTime = (int)$this->attributes['staff_finish_time'];
            $sellerConfirmTime = (int)$this->attributes['seller_confirm_time'];
            $payTime = (int)$this->attributes['pay_time'];
            $cancelTime = (int)$this->attributes['cancel_time'];
            $autoCancelTime = (int)$this->attributes['auto_cancel_time'];
            if ($buyerFinishTime > 0 || ($autoFinishTime > 0 && $autoFinishTime < UTC_TIME)) {
                $status = ORDER_STATUS_FINISH_USER;
            } elseif ($staffFinishTime > 0) {
                $status = ORDER_STATUS_FINISH_STAFF;
            } elseif ($sellerConfirmTime > 0) {
                $status = ORDER_STATUS_AFFIRM_SELLER;
            } elseif ($payTime > 0) {
                $status = ORDER_STATUS_PAY_SUCCESS;
            } elseif ($cancelTime > 0) {
                $status = ORDER_STATUS_CANCEL_USER;
            } elseif ($payTime == 0 && $autoCancelTime < UTC_TIME) {
                $status = ORDER_STATUS_CANCEL_AUTO;
            }
        }

        switch($status)
        {
            case ORDER_STATUS_BEGIN_USER:
                return "statusflow_3";

            case ORDER_STATUS_PAY_SUCCESS:
            case ORDER_STATUS_PAY_DELIVERY:
                return "statusflow_4";

            case ORDER_STATUS_AFFIRM_SELLER:
            case ORDER_STATUS_START_SERVICE:
                return "statusflow_5";

            case ORDER_STATUS_FINISH_STAFF:
                return "statusflow_6";

            case ORDER_STATUS_FINISH_SYSTEM:
            case ORDER_STATUS_FINISH_USER:
                return "statusflow_7";

            case ORDER_STATUS_REFUND_AUDITING:
                return "statusflow_9";
             case ORDER_STATUS_CANCEL_REFUNDING:
                return "statusflow_10";
            case ORDER_STATUS_REFUND_HANDLE:
            case ORDER_REFUND_SELLER_AGREE:
            case ORDER_REFUND_ADMIN_AGREE:
                return "statusflow_10";
            case ORDER_STATUS_REFUND_REFUSE:
            case ORDER_STATUS_REFUND_FAIL:
            case ORDER_REFUND_SELLER_REFUSE:
            case ORDER_REFUND_ADMIN_REFUSE:
                return "statusflow_2_1";
            case ORDER_STATUS_REFUND_SUCCESS:
                return "statusflow_11";
            case ORDER_STATUS_CANCEL_USER:
            case ORDER_STATUS_CANCEL_AUTO:
            case ORDER_STATUS_CANCEL_SELLER:
            case ORDER_STATUS_CANCEL_ADMIN:
            case ORDER_STATUS_USER_DELETE:
            case ORDER_STATUS_SELLER_DELETE:
            case ORDER_STATUS_ADMIN_DELETE:
                return "statusflow_0";
        }
        return null;
    }

    public function getIsCashOnDeliveryAttribute() {
        return $this->attributes['pay_type'] == 'cashOnDelivery';
    }
}