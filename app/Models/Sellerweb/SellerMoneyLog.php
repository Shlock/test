<?php namespace YiZan\Models\Sellerweb;

class SellerMoneyLog extends \YiZan\Models\SellerMoneyLog {
	protected $visible = ['id', 'sn', 'money', 'balance', 'content', 'type', 'related_id', 'create_time', 'seller'];

    public function seller(){
        return $this->belongsTo('YiZan\Models\System\Seller', 'seller_id', 'id');
    }
}
