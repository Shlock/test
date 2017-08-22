<?php
namespace YiZan\Models;

class Refund extends Base
{
	public function order() {
        return $this->belongsTo('YiZan\Models\Order', 'order_id', 'id');
    }
}
