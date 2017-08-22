<?php namespace YiZan\Http\Requests\Admin;

class WithdrawPostRequest extends BaseRequest {
	public function rules() {
		return [
			'data' => ['required'],
		];
	}

	public function messages() {
		return [
		    'data.required' => '错误代号2XXXX',
		];
	}
}
