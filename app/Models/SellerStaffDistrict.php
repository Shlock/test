<?php namespace YiZan\Models;

class SellerStaffDistrict extends Base {

    protected $visible = ['district_id','district','staff'];
    public function seller()
    {
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id');
    }

    public function staff()
    {
        return $this->belongsTo('YiZan\Models\SellerStaff', 'staff_id');
    }

    public function district()
    {
        return $this->belongsTo('YiZan\Models\SellerDistrict', 'district_id', 'id');
    }


   /* public function getMapPosAttribute() {
        $pos    = [];
        $points = !isset($this->attributes['map_pos_str']) ? [] : explode('|', $this->attributes['map_pos_str']);
        foreach ($points as $point) {
            $point  = explode(',', $point);
            $pos[]  = ['x'=>$point[0],'y'=>$point[1]];
        }
        return $pos;
    }*/

}
