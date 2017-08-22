<?php namespace YiZan\Http\Requests\Admin;

class ArticleCreatePostRequest extends BaseRequest {
	public function rules() {
		return [
        	'title' => ['required'],
        	'cateId' => ['required','numeric'],
        	'image' => ['required'],
			'brief' => ['required'],
		];
	}

	public function messages() {
		return [
			'title.required' => '27011',
		    'cateId.required' => '27012',
		    'cateId.numeric' => '27013',
		    'image.required' => '27014',
		    'content.required' => '27015',
		    'brief.required' => '27016',
		];
	}
}
