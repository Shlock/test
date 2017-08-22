<?php namespace YiZan\Models;

class SellerPayLog extends Base {
	//protected $visible = ['id', 'sn', 'payment_type', 'money'];

	public function payment(){
        return $this->belongsTo('YiZan\Models\Payment','payment_type','code');
    } 

    public function seller(){
        return $this->belongsTo('YiZan\Models\Seller','seller_id','id');
    }

}
