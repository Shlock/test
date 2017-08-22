<?php 
namespace YiZan\Models;

class PropertyBuilding extends Base 
{

	public function seller(){
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id', 'id');
    }

    public function district(){
        return $this->belongsTo('YiZan\Models\District', 'district_id', 'id');
    }
}
