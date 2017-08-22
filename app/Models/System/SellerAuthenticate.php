<?php namespace YiZan\Models\System;

class SellerAuthenticate extends \YiZan\Models\SellerAuthenticate 
{
	protected $visible = ['seller_id', 'real_name', 'idcard_sn', 'idcard_positive_img', 
		'idcard_negative_img', 'status', 'update_time', 'certificate_img', 'business_licence_img'];

    public function seller(){
        return $this->belongsTo('YiZan\Models\System\Seller', 'seller_id', 'id','type');
    }
}
