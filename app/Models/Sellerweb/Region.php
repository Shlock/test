<?php namespace YiZan\Models\Sellerweb;

class Region extends \YiZan\Models\Region {
	protected $visible = ['id', 'pid', 'name', 'is_default', 'firstChar', 'sort', 'level'];

	protected $appends = array('firstChar');

	protected $casts = [
	    'isDefault' => 'boolean',
	];

	public function getFirstCharAttribute() {
	    return strtoupper($this->attributes['py']{0});
	}
}
