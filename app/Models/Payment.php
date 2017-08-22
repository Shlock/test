<?php namespace YiZan\Models;

class Payment extends Base {
	protected $visible = ['code', 'name', 'config','status'];

	public function getConfigAttribute() {
	    return json_decode($this->attributes['config'], true);
	}
}