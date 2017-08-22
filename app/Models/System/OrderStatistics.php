<?php namespace YiZan\Models\System;

class OrderStatistics extends \YiZan\Models\Order 
{
	protected $table = 'order';
	protected $visible = ['date','num','total'];
}