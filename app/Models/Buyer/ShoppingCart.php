<?php namespace YiZan\Models\Buyer;

class ShoppingCart extends \YiZan\Models\ShoppingCart {

	public function goods(){
        return $this->belongsTo('YiZan\Models\Buyer\Goods', 'goods_id', 'id');
    }
}
