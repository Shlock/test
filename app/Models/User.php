<?php namespace YiZan\Models;

class User extends Base {
	protected $visible = ['id', 'mobile', 'name', 'avatar', 'province', 'city',  'area','restaurant', 'propertyUser', 'balance', 'total_money','recommend_uid'];
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


    public function restaurant(){
    
        return $this->belongsTo('YiZan\Models\Restaurant','id', 'user_id');
    
    }
    
    public function propertyUser()
    {
        return $this->hasMany('YiZan\Models\PropertyUser', 'user_id', 'id');
    }
    
}
