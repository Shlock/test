<?php namespace YiZan\Models;

class OrderRate extends Base {
	protected $visible = ['id', 'content','order_id', 'images', 'star','score', 'reply', 'reply_time', 'result', 'create_time', 'user', 'order','seller', 'staff', 'is_ano'];

	public function order(){
        return $this->belongsTo('YiZan\Models\Order', 'order_id', 'id');
    }

    public function user(){
        return $this->belongsTo('YiZan\Models\User');
    }

    public function seller(){
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id', 'id');
    }

    public function staff(){
        return $this->belongsTo('YiZan\Models\SellerStaff', 'staff_id', 'id');
    }

    public function getImagesAttribute() {
        if (!isset($this->attributes['images']) || empty($this->attributes['images'])) {
            return [];
        }
	    return explode(',', $this->attributes['images']);
	}
}
