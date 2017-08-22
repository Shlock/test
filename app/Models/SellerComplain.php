<?php 
namespace YiZan\Models;
/**
 * 服务人员举报
 */
class SellerComplain extends Base 
{
    public function seller()
    {
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id');
    }

    public function user()
    {
        return $this->belongsTo('YiZan\Models\User', 'user_id');
    }
    
    public function adminUser()
    {
        return $this->belongsTo('YiZan\Models\AdminUser', 'dispose_admin_id');
    }
    
    public function staff()
    {
        return $this->belongsTo('YiZan\Models\SellerStaff', 'seller_staff_id');
    }
}
