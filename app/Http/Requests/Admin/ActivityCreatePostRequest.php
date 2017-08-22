<?php 
namespace YiZan\Http\Requests\Admin;

class ActivityCreatePostRequest extends BaseRequest {
	public function rules() {
		return [
        	'type' => ['required','numeric'],
        	'name' => ['required'],
        	'beginTime' => ['required','date'],
			'endTime' => ['required','date'],
			'sellTicketId' => ['required','numeric'],
			'sellTicketNum' => ['required','integer'],
			'giftTicketId' => ['required','numeric'],
			'giftTicketNum' => ['required','integer'],
			'price' => ['required','gt:0','numeric']
		];
	}

	public function messages() {
		return [
			'type.required' => '28100',
		    'type.numeric' => '28101',
		    'name.required' => '28102',
		    'beginTime.required' => '28103',
		    'beginTime.date' => '28104',
		    'endTime.required' => '28105',
		    'endTime.date' => '28106',
		    'sellTicketId.required' => '28107',
		    'sellTicketId.numeric' => '28108',
		    'sellTicketNum.required' => '28109',
		    'sellTicketNum.integer' => '28110',
		    'giftTicketId.required' => '28111',
		    'giftTicketId.numeric' => '28112',
		    'giftTicketNum.required' => '28113',
		    'giftTicketNum.integer' => '28114',
		    'price.required' => '28115',
		    'price.gt' => '28116',
		    'price.numeric' => '28116',
		];
	}
}