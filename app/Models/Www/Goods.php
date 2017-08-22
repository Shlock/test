<?php namespace YiZan\Models\Www;

class Goods extends \YiZan\Models\Goods {
    protected $visible = ['id',  'name', 'logo', 'banner', 'price','brief', 'url','goodsType','type','restaurant', 'extend','saleCount', 'commentCount', 'join_service'];
    protected $appends = ['logo','url','banner'];


    public function goodsType(){
        return $this->belongsTo('YiZan\Models\Www\GoodsType', 'type_id', 'id');
    }

    public function extend() {
        return $this->belongsTo('YiZan\Models\Www\GoodsExtend', 'id', 'goods_id');
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
