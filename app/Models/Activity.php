<?php 
namespace YiZan\Models;

use YiZan\Utils\Time;

class Activity extends Base 
{
	protected $appends = ['surplusTime'];

	public function getSurplusTimeAttribute() {
        return Time::getEndTimelag($this->attributes['end_time']);
    }

    public function promotion(){
        return $this->hasMany('YiZan\Models\ActivityPromotion','activity_id','id');
    }

    public function del(){
        return $this->hasManyCount('YiZan\Models\PromotionSn','activity_id','id');
    }

    public function logs(){
        return $this->hasMany('YiZan\Models\ActivityLogs','activity_id','id');
    }
}
