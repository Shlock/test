<?php namespace YiZan\Models\Staff;

class Order extends \YiZan\Models\Order 
{
       protected $visible = ['id', 'sn', 'seller', 'goods', 'user', 'staff','name', 'appoint_time', 'promotion', 'service_fee', 'mobile', 'province','city','area',
        'address', 'pay_fee', 'discount_fee', 'total_fee', 'buy_remark', 'is_rate', 'status', 'pay_end_time','payStatusStr','isFinished','user_id',
        'orderStatusStr', 'pay_status',  'pay_time', 'create_time', 'pay_type', 'service_start_time', 'duration', 'goods_duration', 'reservation_code', 'is_to_store','userRefund', 'orderComplain',
        "isCanDelete", "isCanRate", "isCanComplain", "isCanCancel", "isCanRefund", "isCanPay", "isCanContact", "statusFlowImage", "statusNameDate", "isCanAccept", "isCanFinish", 'statusFlowImage',
        'seller_confirm_time', 'buyer_cancel_time', 'seller_confirm_end_time', 'refund_images', 'refund_content', 'order_num', 'deposit_refund_time', 'deposit_refund_content', 'Schedule','designate_type',
        'Designate','cartSellers','count','goods_fee','map_point','orderStr','orderRate','cancel_remark','order_type','isCanStartService','isCanFinishService','service_content','app_time','app_day',
           'orderGoods', 'isCanFinish','pay_type','isCanChangeStaff','freight','promotion_sn_id','drawn_fee','seller_id','discount_fee','seller_fee','isCashOnDelivery'];

       protected $appends = array('payStatusStr','isFinished','orderStatusStr', "isCanDelete", "isCanRate", "isCanAccept", "isCanFinish", "isCanComplain", "isCanCancel", "isCanRefund", "isCanPay", "isCanContact", 'isCanStartService','isCanFinishService',"statusFlowImage", "Schedule","Designate","duration",'isCanChangeStaff','isCashOnDelivery');

    /**
     * 是否可以开始服务(卖家员工)
     * @return bool
     */
    public function getIsCanStartServiceAttribute()
    {
        $status     = $this->attributes['status'];
        
        return $status == ORDER_STATUS_AFFIRM_SELLER;
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
    /**
     * 是否可以接单(卖家)
     * @return bool
     */
    public function getIsCanAcceptAttribute()
    {
        $status     = $this->attributes['status'];

        return $status == ORDER_STATUS_PAY_SUCCESS ||
                $status == ORDER_STATUS_PAY_DELIVERY;
    }

    /**
     * 是否可以更换服务人员
     * @return bool
     */
    public function getIsCanChangeStaffAttribute()
    {
        $status     = $this->attributes['status'];

        return $status == ORDER_STATUS_AFFIRM_SELLER ||
                $status == ORDER_STATUS_BEGIN_USER ||
                $status == ORDER_STATUS_PAY_SUCCESS ||
                $status == ORDER_STATUS_PAY_DELIVERY ||
                $status == ORDER_STATUS_START_SERVICE;
    }

    /**
     * 付款状态显示
     * @return string
     */
    public function getPayStatusStrAttribute()
    {
        $payStatus     = $this->attributes['pay_status'];
        return $payStatus == 1 ? '已付款' : '未付款';
    }

    /**
     * 是否已完成
     * @return string
     */
    public function getIsFinishedAttribute()
    {
        $status     = $this->attributes['status'];

        return $status == ORDER_STATUS_AFFIRM_SELLER ||
                $status == ORDER_STATUS_BEGIN_USER ||
                $status == ORDER_STATUS_PAY_SUCCESS ||
                $status == ORDER_STATUS_PAY_DELIVERY ||
                $status == ORDER_STATUS_START_SERVICE;
    }


    /**
     * 订单状态
     * @return string
     */
    public function getOrderStatusStrAttribute()
    {
        $statusStr =
            [
                ORDER_STATUS_BEGIN_USER      => '新订单',
                ORDER_STATUS_PAY_SUCCESS     => '已付款',
                ORDER_STATUS_PAY_DELIVERY    => '货到付款',
                ORDER_STATUS_AFFIRM_SELLER   => '已接单',
                ORDER_STATUS_FINISH_STAFF    => '服务人员完成',
                ORDER_STATUS_FINISH_SYSTEM   => '交易完成',
                ORDER_STATUS_FINISH_USER     => '交易完成',
                ORDER_STATUS_CANCEL_USER     => '交易关闭',
                ORDER_STATUS_CANCEL_AUTO     => '支付超时',
                ORDER_STATUS_CANCEL_SELLER   => '交易关闭',
                ORDER_STATUS_CANCEL_ADMIN    => '交易关闭',
                ORDER_STATUS_USER_DELETE     => '会员删除订单',
                ORDER_STATUS_SELLER_DELETE   => '商家删除订单',
                ORDER_STATUS_ADMIN_DELETE    => '总后台删除订单',

                ORDER_STATUS_REFUND_AUDITING     => '申请退款',
                ORDER_STATUS_CANCEL_REFUNDING   => '取消且退款中',
                ORDER_STATUS_REFUND_HANDLE   => '退款处理中',
                ORDER_STATUS_REFUND_FAIL   => '退款失败',
                ORDER_STATUS_REFUND_SUCCESS   => '退款成功',
                ORDER_REFUND_SELLER_AGREE   => '商家同意退款',
                ORDER_REFUND_SELLER_REFUSE   => '商家拒绝退款',
                ORDER_REFUND_ADMIN_AGREE   => '总后台同意退款',
                ORDER_REFUND_ADMIN_REFUSE   => '总后台拒绝退款',
            ];

        $orderType = $this->attributes['order_type'];
        if ($orderType == 1) {
            $statusStr[ORDER_STATUS_FINISH_STAFF] = '配送完成';
            $statusStr[ORDER_STATUS_START_SERVICE] = '配送中';
        } else {
            $statusStr[ORDER_STATUS_FINISH_STAFF] = '服务完成';
            $statusStr[ORDER_STATUS_START_SERVICE] = '服务中';
        }

        $status     = $this->attributes['status'];
        $isRate     = $this->attributes['is_rate'];

        if(($status == ORDER_USER_CONFIRM_SERVICE && $isRate == false) ||
            ($status == ORDER_STATUS_SYSTEM_CONFIRM && $isRate == false))
        {
            return "待评价";
        }

        return array_key_exists($status, $statusStr) ? $statusStr[$status] : "";
    }


    public function getIsCashOnDeliveryAttribute() {
        return $this->attributes['pay_type'] == 'cashOnDelivery';
    }

    /**
     * 是否可以取消(买家)
     * @return bool
     */
    public function getIsCanCancelAttribute()
    {
        $status 	= $this->attributes['status'];
        return
            $status ==  ORDER_STATUS_PAY_DELIVERY ||
            $status == ORDER_STATUS_PAY_SUCCESS ||
            $status == ORDER_STATUS_AFFIRM_SELLER ||
            $status == ORDER_STATUS_BEGIN_USER ||
            $status == ORDER_STATUS_START_SERVICE;
    }
}