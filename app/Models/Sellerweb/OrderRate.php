<?php namespace YiZan\Models\Sellerweb;

class OrderRate extends \YiZan\Models\OrderRate {
	
	protected $visible = ['id', 'content', 'images', 'reply', 'specialty_score', 'communicate_score', 'punctuality_score', 
			'score', 'result', 'create_time', 'user', 'order', 'staff', 'star'];


    public function staff(){
        return $this->belongsTo('YiZan\Models\SellerStaff','staff_id','id');
    }
}
