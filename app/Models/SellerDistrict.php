<?php namespace YiZan\Models;

class SellerDistrict extends Base {

    protected $visible = ['map_point','map_point_str','map_pos','map_pos_str','name','id', 'seller_id', 'province_id', 'city_id', 'area_id','address','province', 'city', 'area', 'districtStaffCount', 'row'];
    public function seller()
    {
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id');
    }

    public function province()
    {
        return $this->belongsTo('YiZan\Models\Region', 'province_id');
    }

    public function city()
    {
        return $this->belongsTo('YiZan\Models\Region', 'city_id');
    }

    public function area()
    {
        return $this->belongsTo('YiZan\Models\Region', 'area_id');
    }

    public function districtStaffCount() {
        return $this->hasManyCount('YiZan\Models\SellerStaffDistrict', 'district_id');
    }

    public function getMapPosAttribute() {
        $pos    = [];
        $points = !isset($this->attributes['map_pos_str']) ? [] : explode('|', $this->attributes['map_pos_str']);
        foreach ($points as $point) {
            $point  = explode(',', $point);
            if(is_array( $point) && count($point) == 2)
            {
                $pos[]  = ['x'=>$point[0],'y'=>$point[1]];
            }
        }
        return $pos;
    }
    public function getMapPointAttribute() {
        if (!isset($this->attributes['map_point_str'])) {
            return [];
        }
        $point = explode(',', $this->attributes['map_point_str']);
        
        if(is_array( $point) && count($point) == 2)
        {
            return ['x'=>$point[0],'y'=>$point[1]];
        }
        
        return [];
    }
}
