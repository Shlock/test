<?php namespace YiZan\Models\System;

class Promotion extends \YiZan\Models\Promotion {
	static $ids = [];
//	protected $visible = ['id', 'name', 'brief', 'type', 'data', 'condition_type', 'condition_data', 'send_type',
//		'send_condition', 'send_count', 'begin_time', 'end_time', 'create_time','expire_day', 'status', 'makeCount', 'sendUserCount', 'seller'];


    public function seller(){
        return $this->belongsTo('YiZan\Models\Seller');
    }

}