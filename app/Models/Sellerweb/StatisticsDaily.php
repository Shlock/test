<?php namespace YiZan\Models\Sellerweb;

class StatisticsDaily extends \YiZan\Models\Order {
	protected $table = 'order';
	protected $visible = ['type','num','duration','trading','total','durationint'];
	protected $appends = array();
}