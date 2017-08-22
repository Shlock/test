<?php namespace YiZan\Models;

class Region extends Base {
	protected $visible = ['id', 'name', 'is_default', 'firstChar', 'sort', 'level', 'pid'];

	protected $appends = array('firstChar');

	protected $casts = [
	    'isDefault' => 'boolean',
	];

	public function getFirstCharAttribute() {
	    return strtoupper($this->attributes['py']{0});
	}
}
