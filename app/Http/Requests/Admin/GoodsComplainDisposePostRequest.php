<?php namespace YiZan\Http\Requests\Admin;

class GoodsAccusationDisposePostRequest extends BaseRequest {
	public function rules() {
		return [
			'id' => ['required','numeric'],
			'content' => ['required'],
		];
	}

	public function messages() {
		return [
		    'id.required' => '21014',
		    'id.numeric' => '21015',
		    'content.required' => '21016',
		];
	}

}
