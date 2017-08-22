<?php namespace YiZan\Models;

class GoodsStaff extends Base {
	public function staffers() {
        return $this->belongsTo('YiZan\Models\SellerStaff', 'staff_id', 'id');
    }
}
