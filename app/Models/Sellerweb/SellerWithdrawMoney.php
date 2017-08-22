<?php namespace YiZan\Models\Sellerweb;

class SellerWithdrawMoney extends \YiZan\Models\SellerWithdrawMoney {
	protected $visible = ['id', 'sn', 'money', 'bank', 'bank_no', 'content', 
		'dispose_time', 'dispose_remark', 'dispose_admin', 'create_time', 'status', 
		'admin', 'seller', 'authenticate'];

	public function admin(){
        return $this->belongsTo('YiZan\Models\System\AdminUser', 'dispose_admin', 'id');
    }

    public function seller(){
        return $this->belongsTo('YiZan\Models\System\Seller', 'seller_id', 'id');
    }

    public function authenticate(){
        return $this->belongsTo('YiZan\Models\System\SellerAuthenticate', 'seller_id', 'seller_id');
    }
}
