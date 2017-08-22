<?php namespace YiZan\Http\Requests\Admin;

class UserRefundPostRequest extends BaseRequest {
	public function rules() {
		return [
			'id' 		=> ['required'],
			'status' 	=> ['required','numeric'],
			'content' 		=> ['required'],
		];
	}

	public function messages() {
		return [
		    'id.required' => '错误代号2XXXX',
		    'status.required' => '错误代号2XXXX',
		    'status.numeric' => '错误代号2XXXX',
		    'content.required' => '40202',
		];
	}
}   