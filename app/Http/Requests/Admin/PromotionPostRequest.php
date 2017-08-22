<?php namespace YiZan\Http\Requests\Admin;

class PromotionPostRequest extends BaseRequest {
	public function rules() {
		return [ 
			'name'	=>  ['required'],
			'sellerId' => ['required','numeric'],
			'data'=> ['required','numeric'],
			'beginTime' => ['required'],
			'endTime' =>  ['required'],
			'expireDay' => ['required','numeric'],
			'conditionType' => ['required'],
			'brief'	=>  ['required'],
		];
	}

	public function messages() {
		return [
		    'name.required' => '28000',
		    'sellerId.required' => '28001',
		    'sellerId.numeric' => '28002',
		    'data.required' => '28003',
		    'data.numeric' => '28004',
		    'beginTime.required' => '28005',
		    'endTime.required' => '28006',
		    'expireDay.required' => '28007',
		    'expireDay.numeric' => '28008',
		    'conditionType.required' => '28009',
		    'brief.required' => '28010',
		];
	}
}

