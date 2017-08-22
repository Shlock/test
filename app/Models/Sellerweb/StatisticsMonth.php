<?php namespace YiZan\Models\Seller;

class StatisticsMonth extends \YiZan\Models\Order {
	protected $table = 'order';
	protected $visible = ['month','num','total'];
	protected $appends = array();
}