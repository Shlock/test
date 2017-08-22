<?php 
namespace YiZan\Http\Controllers\Callback;
use View;

class IndexController extends BaseController {
	public function index() { 
		return View::make('callback.index.index');
	}
}
