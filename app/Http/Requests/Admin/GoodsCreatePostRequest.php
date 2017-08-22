<?php namespace YiZan\Http\Requests\Admin;

class GoodsCreatePostRequest extends BaseRequest {
	public function rules() {
		return [
			// 'sellerId' => ['required_with_all:sellerId','numeric'],
        	'name' => ['required'],
        	// 'duration' => ['required','numeric'],
			// 'price' => ['required','numeric'],
			//'marketPrice' => ['required','numeric'],
			'cateId' => ['required','numeric'],
			'brief' => ['required'],
			'images' => ['required'],
			
		];
	}

	public function messages() {
		return [
		    // 'sellerId.required' => '21000',
		    // 'sellerId.numeric' => '21001',
		    'name.required' => '21002',
		    // 'duration.required' => '21011',
		    // 'duration.numeric' => '21012',
		    // 'price.required' => '21003',
		    // 'price.numeric' => '21004',
		    'cateId.required' => '21007',
		    'cateId.numeric' => '21008',
		    //'marketPrice.required' => '21005',
		    //'marketPrice.numeric' => '21006',
		    'brief.required' => '21009',
		    'images.required' => '21010',
		    
		];
	}
}
