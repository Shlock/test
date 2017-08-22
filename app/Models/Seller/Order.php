<?php namespace YiZan\Models\Seller;

class Order extends \YiZan\Models\Order 
{
	protected $visible = ['id', 'sn', 'seller', 'goods', 'user', 'appoint_time', 'promotion', 'user_name', 'mobile', 
		'address', 'mapPoint', 'pay_fee', 'discount_fee', 'total_fee', 'buy_remark', 'is_rate', 'status', 'orderStatusStr', 'pay_status', 
		'create_time','appoint_day','appoint_hour','goods_name', 'service_finish_time', 'service_start_time','reservation_code',
        "isCanStartService", "isCanFinishService",'province_id','city_id','area_id','province','city','area','gift_remark', 'isCanAccept'];
    
    protected $appends = array('mapPoint', 'orderStatusStr', "isCanStartService", "isCanFinishService", 'isCanAccept');
    
    /**
     * 是否可以开始服务(卖家员工)
     * @return bool
     */
    public function getIsCanStartServiceAttribute()
    {
        $status 	= $this->attributes['status'];
        
        return $status == ORDER_STATUS_SELLER_ACCEPT ||
            $status == ORDER_STATUS_STAFF_ACCEPT ||
            $status == ORDER_STATUS_STAFF_SETOUT;
    }
    /**
     * 是否可以完成服务(卖家员工)
     * @return bool
     */
    public function getIsCanFinishServiceAttribute()
    {
        $status 	= $this->attributes['status'];
        
        return $status == ORDER_STATUS_START_SERVICE;
    }
}