<?php namespace YiZan\Models\Sellerweb;

class Seller extends \YiZan\Models\Seller 
{
    protected $visible = ['id', 'name', 'mobile', 'contacts', 'type', 'brief', 'address', 'mapPoint','mapPos','map_point_str','map_pos_str', 'collect', 'user_id', 'province', 'city', 'brief',  'area', 'status', 'sort', 'staff','staffCount', 'create_time', 'is_authshow', 'image', 'authenticate', 'is_authenticate','banks', 'sellerCate', 'service_fee', 'delivery_fee', 'logo', 'is_check', 'service_tel', 'delivery_time', 'deduct', 'image', 'deliveryTimes','extend', 'district', 'is_cash_on_delivery'];

    public function getMapPointAttribute() {
        if (!isset($this->attributes['map_point_str'])) {
            return '';
        }
        return $this->attributes['map_point_str'];
    }

    public function getMapPosAttribute() {
        if (!isset($this->attributes['map_pos_str'])) {
            return '';
        }
        return $this->attributes['map_pos_str'];
    }

    public function deliveryTimes()
    {
        return $this->hasMany('YiZan\Models\SellerDeliveryTime', 'seller_id', 'id');
    }
}
