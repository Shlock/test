<?php namespace YiZan\Http\Requests\Admin;

class SellerCreditRankPostRequest extends BaseRequest {
	public function rules() {
		return [
			'name'		=> 	['required'],
			'icon' 		=> 	['required'],
			'minScore'	=> 	['required'],
			'maxScore'	=>	['required'],

		];
	}

	public function messages() {
		return [
		    'name.required' => '22101',
		    'icon.required' => '22103',
		    'minScore.required' => '22105',
		    'maxScore.required' => '22107',
		];
	} 
} 