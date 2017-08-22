<?php namespace YiZan\Models\Sellerweb;

class SellerAuthenticate extends \YiZan\Models\SellerAuthenticate {
	protected $visible = ['seller_id', 'real_name', 'idcard_sn', 'idcard_positive_img', 
		'idcard_negative_img', 'status', 'update_time', 'certificate_img', 'business_licence_img'];

}
