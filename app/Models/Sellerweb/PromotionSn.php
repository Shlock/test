<?php namespace YiZan\Models\Sellerweb;

class PromotionSn extends \YiZan\Models\PromotionSn {
	protected $visible = ['id', 'sn', 'send_time', 'create_time', 'expire_time', 'use_time', 'status', 
		'promotion', 'user', 'seller'];

	protected $casts = [
	    'status' => 'int',
	];

	public function promotion(){
        return $this->belongsTo('YiZan\Models\System\Promotion');
    }

    public function user(){
        return $this->belongsTo('YiZan\Models\System\User');
    }

    public function seller(){
        return $this->belongsTo('YiZan\Models\System\Seller');
    }
}
