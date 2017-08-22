<?php namespace YiZan\Models;

class UserCollect extends Base {
	protected $visible = ['id', 'create_time', 'goods', 'goods_id', 'seller_id', 'seller', 'type'];

	public function goods(){
        return $this->belongsTo('YiZan\Models\Goods', 'goods_id', 'id');
    }

    public function seller(){
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id', 'id');
    }

}
