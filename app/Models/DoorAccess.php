<?php namespace YiZan\Models;

class DoorAccess extends Base { 
	
    public function user()
    {
        return $this->belongsTo('YiZan\Models\User', 'user_id');
    }
    
    public function district()
    {
        return $this->belongsTo('YiZan\Models\District', 'district_id');
    } 

    public function seller()
    {
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id');
    } 

    public function build()
    {
        return $this->belongsTo('YiZan\Models\PropertyBuilding', 'build_id');
    } 
}
