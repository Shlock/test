<?php namespace YiZan\Models\Staff;

class StatisticsMonth extends \YiZan\Models\Order {
	protected $table = 'order';
	protected $visible = ['month','num','total','create_day'];
	protected $appends = array();
}