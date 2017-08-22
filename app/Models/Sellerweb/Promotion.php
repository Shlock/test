<?php namespace YiZan\Models\Sellerweb;

class Promotion extends \YiZan\Models\Promotion {
	static $ids = [];
	protected $visible = ['id', 'name', 'brief', 'type', 'data', 'condition_type', 'condition_data', 'send_type', 
		'send_condition', 'send_count', 'begin_time', 'end_time', 'expire_day', 'status', 'makeCount', 'sendUserCount', 'seller'];

	protected $casts = [
	    'status' => 'int',
	];

	public function getMoneyAttribute() {
	    return (float)$this->attributes['data'];
	}

	public function makeCount() {
        return $this->hasManyCount('YiZan\Models\System\PromotionSn', 'promotion_id', 'id');
    }

    public function sendUserCount() {
        return $this->hasManyCount('YiZan\Models\System\PromotionSn', 'promotion_id', 'id');
    }

    public function seller(){
        return $this->belongsTo('YiZan\Models\Seller');
    }
}