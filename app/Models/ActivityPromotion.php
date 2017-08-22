<?php 
namespace YiZan\Models;

class ActivityPromotion extends Base
{
    public function promotion(){
        return $this->belongsTo('YiZan\Models\Promotion','promotion_id','id');
    }
}
