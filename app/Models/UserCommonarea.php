<?php namespace YiZan\Models;

class UserCommonarea extends Base {
	protected $visible = ['id','district'];

	public function district(){
        return $this->belongsTo('YiZan\Models\SellerDistrict', 'district_id', 'id');
    }
}
