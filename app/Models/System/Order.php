<?php namespace YiZan\Models\System;

class Order extends \YiZan\Models\Order 
{
	protected $visible = ['id','user_id', 'sn', 'seller', 'orderGoods', 'service_fee','user', 'appoint_time', 'promotion', 'name', 'mobile',
		'address', 'mapPoint', 'pay_fee', 'discount_fee', 'total_fee', 'buy_remark', 'is_rate',  
		'orderStatusStr', 'pay_status', 'pay_type', 'pay_time', 'create_time',  'confirm_time', 'refund',
		'service_start_time', 'service_finish_time', 'staff', 'deduct_amount', 'reservation_code', 'is_to_store', 'status',
        "isCanDelete", "isCanRate", "isCanComplain", "isCanCancel", "isCanPay", "isCanContact", "isCanConfirm", "statusFlowImage", "statusNameDate",'statusFlowImage',
        'seller_confirm_time', "IsReceivability","SellerStaff", 'order_type','restaurant','buyer_cancel_time', 'seller_confirm_end_time', 'refund_images',
        'refund_content', 'refund_time', 'deposit_refund_time', 'deposit_refund_content','begin_img','end_img', 'depositRefundAdmin','goods_duration',
        'Duration','seller_staff_id','seller_id','service_content','freight','goods_fee','app_time','address','payment','province_id','city_id',
        'area_id','province','city','area','gift_remark','total','money','cancel_remark','invoice_remark', 'isCanAccept','drawn_fee','promotion_sn_id',
        'discount_fee','seller_fee', 'isCashOnDelivery','auto_finish_time','buyer_finish_time', 'isCanStartService','isCanFinish', 'year_name', 'totalPayfee', 'totalNum', 'totalDrawnfee', 'totalOnline','totalCash','totalDiscountFee','totalSellerFee', 'orderStatus'];
    
    protected $appends = array('mapPoint', 'orderStatusStr', "isCanDelete", "IsReceivability","isCanRate", "isCanComplain", "isCanCancel", "isCanPay", "isCanContact", "isCanConfirm", "statusFlowImage", "statusNameDate", 'Duration', 'isCanAccept','isCashOnDelivery', 'isCanStartService', 'isCanFinish','orderStatus');
    
    public function refund(){
        return $this->belongsTo('YiZan\Models\System\UserRefund', 'id', 'order_id');
    }

    public function user(){
        return $this->belongsTo('YiZan\Models\System\User', 'user_id', 'id');
    }

    public function staff(){
        return $this->belongsTo('YiZan\Models\System\SellerStaff','seller_staff_id', 'id');
    }


    /**
     * 是否可以删除(餐厅-服务站-总后台)
     * @return bool
     */
    public function getIsCanDeleteAttribute()
    {
        $status 	= $this->attributes['status'];

        return $status == ORDER_STATUS_SELLER_DELETE;
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
                return "statusflow_0";
        }
        return null;
    }
    /**
     * 是否可以取消(买家)
     * @return bool
     */
    /*public function getIsCanCancelAttribute()
    {
        $status 	= $this->attributes['status'];
        $payFee    = $this->attributes['pay_fee'];
    
        return $status == ORDER_STATUS_BEGIN_USER ||
        ($status == ORDER_STATUS_PAY_SUCCESS && $payFee < 0.0001) ||
        ($status == ORDER_STATUS_PAY_DELIVERY && $payFee < 0.0001) ||
        ($status == ORDER_STATUS_AFFIRM_SELLER && $payFee < 0.0001);
    }*/
    

    public function getIsCanCancelAttribute() {
       /* $status     = $this->attributes['status'];
        $payFee    = $this->attributes['pay_fee'];
        $pay_status = $this->attributes['pay_status'];
        $cancel_time = $this->attributes['cancel_time'];
        return ($pay_status == 1 && $payFee >= 0.01 && $cancel_time <= 0);*/
        $status     = $this->attributes['status'];
        return $status  == ORDER_STATUS_BEGIN_USER ||
                $status  == ORDER_STATUS_PAY_SUCCESS ||
                $status  == ORDER_STATUS_START_SERVICE ||
                $status  == ORDER_STATUS_PAY_DELIVERY ||
                $status  == ORDER_STATUS_AFFIRM_SELLER ||
                $status  == ORDER_STATUS_FINISH_STAFF;
    }

    public function getIsCashOnDeliveryAttribute() {
        return $this->attributes['pay_type'] == 'cashOnDelivery';
    }
}