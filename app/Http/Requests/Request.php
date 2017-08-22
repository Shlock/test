<?php namespace YiZan\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Lang;

abstract class Request extends FormRequest {

	protected $tpl;
	protected $lang;

	public function authorize() {
		return true;
	}

	public function response(array $errors) {
		$error = current($errors);
		if ($this->ajax() || $this->wantsJson()) {
			$info = [];
			$info['status'] = false;
			$info['msg'] 	= Lang::get($this->lang.'.code.'.$error[0]);
			$info['data'] 	= key($errors);
			die(json_encode($info));
		}else {
			View::share('msg', $error[0]);
			return View::make($this->tpl.'._layouts.error');
		}
	}
}