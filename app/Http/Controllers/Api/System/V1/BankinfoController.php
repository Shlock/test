<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\Sellerweb\BankInfoService; 
use YiZan\Services\Sellerweb\UserService;  
use YiZan\Models\UserVerifyCode;
use Lang, Validator;

class BankinfoController extends BaseController {

	/**
	 * 更新银行卡
	 */
	public function update(){   
		$result = BankInfoService::updateBankInfo((int)$this->request('id'),$this->request('bank'),$this->request('bankNo'),$this->request('mobile'),$this->request('name'));  
        $this->output($result);
	} 

}