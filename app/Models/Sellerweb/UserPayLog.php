<?php namespace YiZan\Models\Sellerweb;

class UserPayLog extends \YiZan\Models\UserPayLog {
	protected $visible = ['id', 'sn', 'payment_type', 'money', 'content', 'create_time', 'pay_time', 
		'user', 'seller', 'order'];

    public function user(){
        return $this->belongsTo('YiZan\Models\System\User');
    }

    public function seller(){
        return $this->belongsTo('YiZan\Models\System\Seller');
    }

    public function order(){
        return $this->belongsTo('YiZan\Models\System\Order');
    }
}

