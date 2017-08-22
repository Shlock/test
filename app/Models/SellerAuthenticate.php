<?php namespace YiZan\Models;

class SellerAuthenticate extends Base 
{
	protected $primaryKey = 'seller_id';
	
	protected $visible = ['seller_id', 'real_name', 'idcard_sn', 'idcard_positive_img', 'idcard_negative_img', 'status', 'business_licence_sn', 'business_licence_img', 'certificate_img'];
}
