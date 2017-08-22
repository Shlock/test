<?php 
namespace YiZan\Models;

/**
 * 车辆信息
 */
class Vehicle extends Base 
{
	/*车型*/

	public function brand(){

        return $this->belongsTo('YiZan\Models\CarBrand', 'brand_id', 'id');

    }
    /*车系*/
    
    public function series(){
    
        return $this->belongsTo('YiZan\Models\CarSeries', 'series_id', 'id');
    
    }
    
}
