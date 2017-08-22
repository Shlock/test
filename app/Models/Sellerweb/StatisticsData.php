<?php namespace YiZan\Models\Sellerweb;

class StatisticsData extends \YiZan\Models\Order {
	protected $table = 'order';
	protected $visible = [ 'num','trading','total','date'];
	protected $appends = array();
}