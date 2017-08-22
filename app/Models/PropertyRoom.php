<?php 
namespace YiZan\Models;

class PropertyRoom extends Base 
{
	public function seller(){
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id', 'id');
    }

    public function district(){
        return $this->belongsTo('YiZan\Models\District', 'district_id', 'id');
    }

    public function build(){
        return $this->belongsTo('YiZan\Models\PropertyBuilding', 'build_id', 'id');
    }
}
