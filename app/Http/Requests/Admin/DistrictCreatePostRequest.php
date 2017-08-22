<?php namespace YiZan\Http\Requests\Admin;

class DistrictCreatePostRequest extends BaseRequest {
	public function rules() {
		return [
        	'provinceId' => ['required','numeric'],
        	'cityId' => ['required','numeric'],
			'row' => ['required'],
			'name' => ['required'],
			'address' => ['required'],
		];
	}

	public function messages() {
		return [
			'provinceId.required' => '21022',
		    'provinceId.numeric' => '21023',
		    'cityId.required' => '21022',
		    'cityId.numeric' => '21023',
		    'row.required' => '21024',
		    'name.required' => '21025',
		    'address.required' => '23013',
		];
	}
}
