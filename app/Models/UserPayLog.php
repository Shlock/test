<?php namespace YiZan\Models;

class UserPayLog extends Base {
	//protected $visible = ['id', 'sn', 'order', 'payment_type', 'money','order_id', 'activity_id', 'user_id'];

	public function payment(){
        return $this->belongsTo('YiZan\Models\Payment','payment_type','code');
    }

    public function order(){
        return $this->belongsTo('YiZan\Models\Order');
    }
}
