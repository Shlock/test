<?php namespace YiZan\Models;

class UserAddress extends Base {
	protected $visible = ['id', 'address', 'mapPoint', 'is_default', 'province', 'city', 'area','doorplate','name','mobile','map_point_str', 'detail_address'];

	protected $appends = ['mapPoint'];

	protected $casts = [
	    'isDefault' => 'boolean',
	];

	public function getMapPointAttribute() {
		if (!isset($this->attributes['map_point_str']) || empty($this->attributes['map_point_str'])) {
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
}