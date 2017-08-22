<?php namespace YiZan\Models;

class UserCollectSeller extends Base {
	protected $visible = ['id', 'create_time', 'seller'];

	public function seller(){
        return $this->belongsTo('YiZan\Models\Seller');
    }
}
