<?php namespace YiZan\Models\Sellerweb;

class UserRefund extends \YiZan\Models\UserRefund {
	protected $visible = ['id', 'sn', 'money', 'content', 
		'dispose_time', 'dispose_remark', 'dispose_admin', 'create_time', 'status', 
		'user', 'seller', 'order', 'admin'];

	public function admin(){
        return $this->belongsTo('YiZan\Models\System\AdminUser', 'dispose_admin', 'id');
    }

    public function seller(){
        return $this->belongsTo('YiZan\Models\System\Seller', 'seller_id', 'id');
    }

    public function user(){
        return $this->belongsTo('YiZan\Models\System\User', 'user_id', 'id');
    }

    public function order(){
        return $this->belongsTo('YiZan\Models\System\Order', 'order_id', 'id');
    }
}
