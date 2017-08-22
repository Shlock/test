<?php 
namespace YiZan\Models;
/**
 * 服务举报
 */
class GoodsComplain extends Base 
{
    public function seller()
    {
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id');
    }

    public function goods()
    {
        return $this->belongsTo('YiZan\Models\Goods', 'goods_id');
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
        return $this->belongsTo('YiZan\Models\SellerStaff', 'seller_id');
    }
}
