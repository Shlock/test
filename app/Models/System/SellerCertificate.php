<?php namespace YiZan\Models\System;

class SellerCertificate extends \YiZan\Models\SellerCertificate 
{
	protected $visible = ['seller_id', 'certificates', 'status', 'update_time', 'dispose_time', 'dispose_remark', 'admin', 'seller', 'type'];

	public function admin()
    {
        return $this->belongsTo('YiZan\Models\System\AdminUser', 'dispose_admin', 'id');
    }

    public function seller()
    {
        return $this->belongsTo('YiZan\Models\System\Seller', 'seller_id', 'id');
    }
}
