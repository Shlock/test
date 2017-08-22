<?php namespace YiZan\Http\Requests\Admin;

class SellerWithdrawMoneyPostRequest extends BaseRequest {
	public function rules() {
		return [
			'id'		=> ['required'],
			'content'	=> ['required'],
			'status'	=> ['required'],
		];
	}

	public function messages() {
		return [
		    'id.required' => '24010', 
		    'content.required' => '24012',
		    'status.required' => '24015',
		];
	} 
}