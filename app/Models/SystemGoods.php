<?php namespace YiZan\Models;

class SystemGoods extends Base {
	protected $visible = ['id', 'name', 'image', 'images', 'price_type', 'price', 'market_price', 'brief', 'duration', 'detail','status', 'sort', 'create_time', 'update_time', 'cate', 'cityPrices'];

	protected $appends = array('image');

	public function cate(){
        return $this->belongsTo('YiZan\Models\GoodsCate');
    }

    public function cityPrices(){
        return $this->hasMany('YiZan\Models\SystemGoodsPrice', 'system_goods_id', 'id');
    }

    public function getImagesAttribute() {
    	if (!isset($this->attributes['images']) || empty($this->attributes['images'])) {
    		return [];
    	}
	    return explode(',', $this->attributes['images']);
	}

	public function getImageAttribute() {
		if (!isset($this->attributes['images']) || empty($this->attributes['images'])) {
    		return '';
    	}
	    return current(explode(',', $this->attributes['images']));
	}
}
