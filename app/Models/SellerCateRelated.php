<?php 
namespace YiZan\Models;

class SellerCateRelated extends Base 
{
   	//protected $visible = ['cate_id', 'cates', 'sellers'];

   	public function cates()
    {
        return $this->belongsTo('YiZan\Models\SellerCate', 'cate_id', 'id');
    }

    public function sellers()
    {
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id', 'id');
    }
}
