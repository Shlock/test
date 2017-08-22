<?php 
namespace YiZan\Models;

use YiZan\Utils\Time;

class ActivityGoods extends Base 
{
	public function goods() {
        return $this->belongsTo('YiZan\Models\Goods', 'goods_id', 'id');
    }

    public function activity() {
    	return $this->belongsTo('YiZan\Models\Activity', 'activity_id', 'id');
    }
}
