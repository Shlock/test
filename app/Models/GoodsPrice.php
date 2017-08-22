<?php namespace YiZan\Models;

class GoodsPrice extends Base {
	protected $visible = ['model_code', 'price', 'market_price', 'city'];

	public function city(){
        return $this->belongsTo('YiZan\Models\Region', 'city_id', 'id');
    }
}
