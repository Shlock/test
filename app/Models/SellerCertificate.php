<?php 
namespace YiZan\Models;

/**
 * 卖家资质认证
 */
class SellerCertificate extends Base {
	protected $primaryKey = 'seller_id';
	
	protected $visible = ['seller_id', 'certificates', 'status'];

	public function getCertificatesAttribute() {
		if (!isset($this->attributes['certificates']) || empty($this->attributes['certificates'])) {
    		return [];
    	}
	    return explode(',', $this->attributes['certificates']);
	}
}
