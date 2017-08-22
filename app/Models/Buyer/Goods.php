<?php namespace YiZan\Models\Buyer;

class Goods extends \YiZan\Models\Goods {
    //protected $visible = ['id',  'name', 'logo', 'banner', 'price','brief', 'url','goodsType','type','restaurant', 'extend','saleCount', 'commentCount'];
    protected $appends = ['logo','url','banner'];

    
    public function collect(){
        return $this->belongsTo('YiZan\Models\UserCollect', 'id', 'goods_id');
    }

    public function goodsType(){
        return $this->belongsTo('YiZan\Models\Buyer\GoodsType', 'type_id', 'id');
    }

    public function getLogoAttribute() {
        if (!isset($this->attributes['images']) || empty($this->attributes['images'])) {
            return '';
        }
        return current(explode(',', $this->attributes['images']));
    }

    public function getBannerAttribute() {
        if (!isset($this->attributes['images']) || empty($this->attributes['images'])) {
            return [];
        }
        return explode(',', $this->attributes['images']);
    }
}
