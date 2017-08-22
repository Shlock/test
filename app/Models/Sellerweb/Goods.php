<?php namespace YiZan\Models\Sellerweb;

class Goods extends \YiZan\Models\Goods 
{
	//protected $visible = ['id', 'seller_id', 'name', 'unit', 'stock' 'image', 'images', 'price', 'old_price', 'market_price', 'brief', 'duration', 'seller', 'collect', 'cate','status','dispose_time','dispose_result', 'dispose_status','dispose_admin_id', 'type', 'restaurant', 'join_service', 'type_id', 'restaurant_id', 'norms'];
    
    public function seller()
    {
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id', 'id');
    }
    
    public function cate()
    {
        return $this->belongsTo('YiZan\Models\GoodsCate', 'cate_id', 'id');
    }

    public function type() {
    	return $this->belongsTo('YiZan\Models\GoodsType', 'type_id', 'id');
    }

    public function restaurant() {
    	return $this->belongsTo('YiZan\Models\Restaurant', 'restaurant_id', 'id');
    }

    public function norms() {
        return $this->hasMany('YiZan\Models\GoodsNorms', 'goods_id', 'id');
    }
}
