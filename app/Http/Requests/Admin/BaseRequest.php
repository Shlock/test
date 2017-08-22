<?php namespace YiZan\Http\Requests\Admin;

use YiZan\Http\Requests\Request;

abstract class BaseRequest extends Request {
	protected $tpl 	= 'admin';
	protected $lang = 'admin';
}
