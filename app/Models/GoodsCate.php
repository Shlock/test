<?php 
namespace YiZan\Models;

class GoodsCate extends Base {
	
    public function goods() {
        return $this->hasMany('YiZan\Models\Goods', 'cate_id', 'id');
    }

   	public function cates()  {
        return $this->belongsTo('YiZan\Models\SellerCate', 'trade_id', 'id');
    }

}
