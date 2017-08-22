<?php namespace YiZan\Http\Requests\Admin;

class GoodsCateCreatePostRequest extends BaseRequest {
	public function rules() {
		return [
			'pid' => ['required','numeric'],
        	'name' => ['required'],
		];
	}

	public function messages() {
		return [
		    'pid.required' => '21007',
		    'pid.numeric' => '21008',
		    'name.required' => '21013',
		];
	}
}
