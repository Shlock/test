<?php namespace YiZan\Models\System;

class SellerStaff extends \YiZan\Models\SellerStaff 
{	
	protected $visible = [
        'id', 
        'name',
        'mobile',
        'avatar',
        'photos',
        'brief',
        'address',
        'map_point',
        'map_point_str',
        'map_pos',
        'map_pos_str',
        'status',
        'extend',
        'province',
        'city',
        'area',
        'sort',
        'seller',
        'user',
        'collect',
        'sex',
        'birthday',
        'age',
        'sexStr',
        'now_order',
        'goods',
        'authentication',
        'recruitment',
        'hobbies',
        'constellation',
        'business_district',
        'district',
        'districtName',
        'job_number',
        'card_number',
        'begin_time',
        'end_time',
        'order_status',
        'type',
        'authenticate_img'
    ];

	public function address(){
        return $this->belongsTo('YiZan\Models\UserAddress', 'id', 'user_id');
    }

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
}
