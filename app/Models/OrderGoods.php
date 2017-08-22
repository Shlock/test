<?php namespace YiZan\Models;

class OrderGoods extends Base 
{
    public function goods(){
        return $this->belongsToMany('YiZan\Models\Goods', 'order_goods', 'id', 'goods_id');
    }
}
