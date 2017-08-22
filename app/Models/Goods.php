<?php namespace YiZan\Models;

class Goods extends Base { 

    /**
     * 商家自定义商品
     */
    const SELLER_GOODS = 1;
     /**
     * 商家自定义服务
     */
    const SELLER_SERVICE = 2;

	//protected $visible = ['id', 'seller_id', 'name', 'image', 'images', 'price_type', 'price', 'market_price', 'brief', 'duration', 'seller', 'collect', 'detail','status','isMultiStaff', 'staff', 'extend','goods_tags', 'call_price','sales_volume', 'url', 'saleStatus' , 'condition_type'];

	protected $appends = array('image','isMultiStaff', 'url', 'saleStatus');

	public function seller(){
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id', 'id');
    }
    
    public function collect(){
        return $this->belongsTo('YiZan\Models\UserCollect', 'id', 'goods_id');
    }

    public function extend(){
        return $this->belongsTo('YiZan\Models\GoodsExtend', 'id', 'goods_id');
    }

    public function goodsStaff(){
        return $this->hasMany('YiZan\Models\GoodsStaff', 'goods_id', 'id');
    }

    public function getSaleStatusAttribute() {
        if (!isset($this->attributes['sale_status'])) {
            return 1;
        } 
        return $this->attributes['sale_status'];
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

    public function getIsMultiStaffAttribute() {
        if (!isset($this->attributes['staffCount']) || $this->attributes['staffCount'] < 1) {
            return false;
        }
        return $this->attributes['staffCount'] > 1;
    }

    public function getUrlAttribute() {
        return u('wap#Goods/appbrief',['id'=>$this->attributes['id']]);
    }

    public function type() {
        return $this->belongsTo('YiZan\Models\GoodsType', 'type_id', 'id');
    }

    public function restaurant(){
        return $this->belongsTo('YiZan\Models\Restaurant','restaurant_id','id');
    }

    public function norms() {
        return $this->hasMany('YiZan\Models\GoodsNorms', 'goods_id', 'id');
    }
}
