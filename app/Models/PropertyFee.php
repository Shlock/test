<?php namespace YiZan\Models;

class PropertyFee extends Base { 
	
    public function puser()
    {
        return $this->belongsTo('YiZan\Models\PropertyUser', 'puser_id');
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

    public function room()
    {
        return $this->belongsTo('YiZan\Models\PropertyRoom', 'room_id');
    } 
}
