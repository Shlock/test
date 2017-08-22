<?php namespace YiZan\Models\Www;

class ShoppingCart extends \YiZan\Models\ShoppingCart {

	public function goods(){
        return $this->belongsTo('YiZan\Models\Www\Goods', 'goods_id', 'id');
    }
}
