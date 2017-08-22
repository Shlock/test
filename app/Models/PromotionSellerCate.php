<?php namespace YiZan\Models;

class PromotionSellerCate extends Base {

    public function cates(){
        return $this->belongsTo('YiZan\Models\SellerCate','seller_cate_id', 'id');
    }
}