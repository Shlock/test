<?php 
namespace YiZan\Models;
/**
 * 意见反馈
 */
class Feedback extends Base 
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
}
