<?php namespace YiZan\Models\System;

class Goods extends \YiZan\Models\Goods 
{
	//protected $visible = ['id', 'seller_id','restaurant_id','restaurant','type_id', 'name','image', 'images', 'old_price', 'price', 'brief',
    //    'duration', 'seller', 'collect', 'type', 'sort', 'status', 'join_service', 'dispose_status', 'dispose_time','dispose_result','dispose_admin_id', "detail",'statusStr','disposeStatusStr','typeStr'];

    protected $appends = ['statusStr','disposeStatusStr', 'image', 'typeStr'];
    public function seller()
    {
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id', 'id');
    }
    
    public function type()
    {
        return $this->belongsTo('YiZan\Models\GoodsType', 'type_id', 'id');
    }

    public function restaurant()
    {
        return $this->belongsTo('YiZan\Models\Restaurant', 'restaurant_id', 'id');
    }
    
    public function cate()
    {
        return $this->belongsTo('YiZan\Models\GoodsCate', 'cate_id', 'id');
    }

    public function norms() {
        return $this->hasMany('YiZan\Models\GoodsNorms', 'goods_id', 'id');
    }

    public function getStatusStrAttribute() {
        return  $this->attributes['status'] == 0 ? '下架' : '上架';
    }

    public function getTypeStrAttribute() {
        $type = [
            '2' => '跑腿',
            '3' => '家政',
            '4' => '汽车',
            '5' => '其他'
        ];
        return  $type[$this->attributes['type']];
    }

    public function getDisposeStatusStrAttribute() {
        switch($this->attributes['dispose_status']){
            case '0': $str = '待审核'; break;
            case '1': $str = '通过'; break;
            case '-1': $str = '未通过'; break;
        }
        return  $str;
    }

}
