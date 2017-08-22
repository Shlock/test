<?php namespace YiZan\Models;

class UserCollectGoods extends Base {
	protected $visible = ['id', 'create_time', 'goods', 'salesVolume'];

	public function goods(){
        return $this->belongsTo('YiZan\Models\Goods');
    }
}
