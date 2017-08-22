<?php namespace YiZan\Models\System;

class User extends \YiZan\Models\User {
	protected $visible = ['id', 'mobile', 'name', 'avatar', 'reg_time', 'reg_ip', 
		'regProvince', 'regCity', 'login_time', 'login_ip', 'loginProvince', 'loginCity', 'extensionWorker',
        'address', 'status', 'restaurant', 'seller', 'staff', 'balance','recommend_uid','reg_lat','reg_lng','reg_address','is_extension_worker','extension_lat','extension_lng','extension_address','extension_range'];

	public function regProvince(){
        return $this->belongsTo('YiZan\Models\Region', 'reg_province_id', 'id');
    }

    public function regCity(){
        return $this->belongsTo('YiZan\Models\Region', 'reg_city_id', 'id');
    }

    public function loginProvince(){
        return $this->belongsTo('YiZan\Models\Region', 'login_province_id', 'id');
    }
    
    public function loginCity(){
        return $this->belongsTo('YiZan\Models\Region', 'login_city_id', 'id');
    }

    public function address(){
        return $this->belongsTo('YiZan\Models\UserAddress', 'id', 'user_id');
    }

    public function seller(){
        return $this->belongsTo('YiZan\Models\Seller', 'id', 'user_id');
    }

    public function staff(){
        return $this->belongsTo('YiZan\Models\SellerStaff', 'id', 'user_id');
    }

    public function extensionWorker(){
        return $this->belongsTo('\YiZan\Models\User', 'recommend_uid', 'id');
    }
}
