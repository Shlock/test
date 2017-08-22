<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\UserAccountService; 
use YiZan\Services\Sellerweb\UserService;  
use YiZan\Models\UserVerifyCode;
use YiZan\Services\Sellerweb\SellerService;
use Lang, Validator;

class UseraccountController extends BaseController {
	
	/**
	 * 帐户余额
	 */
	public function get()
	{ 
		$this->outputData(UserAccountService::getAccount($this->sellerId));
	} 
	
	/**
	 * 提现申请
	 */
	public function withdraw(){  
		$this->output(UserAccountService::createWithdraw($this->sellerId,(int)$this->request('id'),(float)$this->request('money'),$this->request('mobile'),$this->request('verifyCode')));
	} 
	
	/**
	 * 提款手机号码验证
	 */
	public function withdrawverify() 
    {
		$result = UserService::sendVerifyCode($this->request('mobile'), UserVerifyCode::TYPE_WITHDRAW);
		$this->output($result);
	}
	
	/**
	 * 资金流水记录
	 */
	public function lists(){  
            //return array('code'=>0);
		$this->outputData(
			UserAccountService::logLists(
				$this->sellerId,
				$this->request('beginTime'),
				$this->request('endTime'),
				$this->request('status'),
                max((int)$this->request('page'), 1),
                max((int)$this->request('pageSize'), 20)
			)
		);
	}

    /**
     * 评价统计
     */
    public function comment() {
        $data = SellerService::getById($this->sellerId, 0);
        $this->outputData($data);
    }

}