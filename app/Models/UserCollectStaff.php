<?php namespace YiZan\Models;

class UserCollectStaff extends Base {
	protected $visible = ['id', 'create_time', 'staff'];

	public function staff(){
        return $this->belongsTo('YiZan\Models\SellerStaff', 'staff_id', 'id');
    }
}
