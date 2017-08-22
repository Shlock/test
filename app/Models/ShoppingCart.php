<?php namespace YiZan\Models;

class ShoppingCart extends Base {

    //protected $visible = ['id','goods_id','seller_id','user_id','num','norms_id','user','goods','norms','seller'];

    public function seller(){
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id', 'id');
    }

    public function user(){
        return $this->belongsTo('YiZan\Models\User', 'user_id', 'id');
    }

    public function goods(){
        return $this->belongsTo('YiZan\Models\Goods', 'goods_id', 'id');
    }

    public function norms(){
        return $this->belongsTo('YiZan\Models\GoodsNorms', 'norms_id', 'id');
    }
}
