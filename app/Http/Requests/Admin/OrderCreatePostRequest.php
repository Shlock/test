<?php namespace YiZan\Http\Requests\Admin;

class OrderCreatePostRequest extends BaseRequest {
	public function rules() {
		return [
			'goodsId' => ['required','numeric'],
			'userId' => ['required','numeric'],
			'userName' => ['required'],
			'appointTime' => ['required'],
			'appointTime2' => ['required'],
			'mobile' => ['required','numeric'],
			'address' => ['required'],
			'mapPoint' => ['required'],
			'remark' => ['required'],
		];
	}

	public function messages() {
		return [
		    'goodsId.required' => '23003',
		    'goodsId.numeric' => '23004',
		    'userId.required' => '23005',
		    'userId.numeric' => '23006',
		    'userName.required' => '23008',
		    'appointTime.required' => '23009',
		    'appointTime2.required' => '23009',
		    'mobile.required' => '23010',
		    'mobile.numeric' => '23011',
		    'address.required' => '23012',
		    'mapPoint.required' => '23013',
		    'remark.required' => '23014',
		];
	}
}
