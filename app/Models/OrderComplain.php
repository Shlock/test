<?php 
namespace YiZan\Models;
/**
 * 订单举报
 */
class OrderComplain extends Base 
{
    /**
     * 未处理
     */
    const STATUS_NO     = 1;
    /**
     * 已处理
     */
    const STATUS_OK     = 2;
    /**
     * 已驳回
     */
    const STATUS_BACK   = 3;

    public function order()
    {
        return $this->belongsTo('YiZan\Models\Order', 'order_id');
    }

    public function staff()
    {
        return $this->belongsTo('YiZan\Models\SellerStaff', 'staff_id');
    }

    public function user()
    {
        return $this->belongsTo('YiZan\Models\User', 'user_id');
    }
    
    public function adminUser()
    {
        return $this->belongsTo('YiZan\Models\AdminUser', 'dispose_admin_id');
    }
     
}
