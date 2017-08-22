<?php namespace YiZan\Models\Sellerweb;

class SellerCertificate extends \YiZan\Models\SellerCertificate {
	protected $visible = ['seller_id', 'certificates', 'status', 'update_time', 'dispose_time', 'dispose_remark', 'admin'];

	public function admin(){
        return $this->belongsTo('YiZan\Models\System\AdminUser', 'dispose_admin', 'id');
    }
}
