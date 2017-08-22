<?php 
namespace YiZan\Models;

class Seller extends Base 
{
    /**
     * 自营机构
     */
    const SELF_ORGANIZATION = 1;
     /**
     * 服务机构
     */
    const SERVICE_ORGANIZATION = 2;
    
    /**
     * 物业公司
     */
    const PROPERTY_ORGANIZATION = 3;
    
    
    //protected $visible = ['id', 'name', 'mobile', 'contacts', 'address', 'collect', 'user_id', 'province', 'city',  'area', 'status', 'sort', 'staff', 'image', 'mapPoint', 'mapPos', 'authenticate', 'is_authenticate', 'banks', 'sellerCate', 'type', 'service_fee', 'delivery_fee', 'logo', 'is_check', 'brief', 'business_desc', 'delivery_time','deduct'];

    protected $appends = array('mapPoint', 'mapPos');

    public function collect(){
        return $this->belongsTo('YiZan\Models\UserCollect', 'id', 'seller_id');
    }

    public function extend(){
        return $this->belongsTo('YiZan\Models\SellerExtend', 'id', 'seller_id');
    }

    public function user(){
        return $this->belongsTo('YiZan\Models\User', 'id', 'user_id');
    }

    public function authenticate(){
        return $this->belongsTo('YiZan\Models\SellerAuthenticate', 'id', 'seller_id');
    }

    public function banks(){
        return $this->hasMany('YiZan\Models\SellerBank', 'seller_id', 'id');
    }

    public function sellerCate(){
        return $this->hasMany('YiZan\Models\SellerCateRelated', 'seller_id', 'id');
    }

    public function deliveryTimes()
    {
        return $this->hasMany('YiZan\Models\SellerDeliveryTime', 'seller_id', 'id');
    }

    public function getMapPointAttribute() {
        if (!isset($this->attributes['map_point_str'])) {
            return ['x'=>0,'y'=>0];
        }
        $point = explode(',', $this->attributes['map_point_str']);
        return ['x'=>$point[0],'y'=>$point[1]];
    }

    public function province()
    {
        return $this->belongsTo('YiZan\Models\Region', 'province_id', 'id');
    }
    
    public function city()
    {
        return $this->belongsTo('YiZan\Models\Region', 'city_id', 'id');
    }
    
    public function area()
    {
        return $this->belongsTo('YiZan\Models\Region', 'area_id', 'id');
    }
    
    public function getMapPosAttribute() {
        $pos    = [];
        $points = !isset($this->attributes['map_pos_str']) ? [] : explode('|', $this->attributes['map_pos_str']);
        foreach ($points as $point) {
            $point  = explode(',', $point);
            $pos[]  = ['x'=>$point[0],'y'=>$point[1]];
        }
        return $pos;
    }

    public function getPhotosAttribute() {
        if (!isset($this->attributes['photos']) || empty($this->attributes['photos'])) {
            return [];
        }
        return explode(',', $this->attributes['photos']);
    }

    public function district(){
        return $this->belongsTo('YiZan\Models\District', 'id', 'seller_id');
    }
}
