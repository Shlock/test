<?php 
namespace YiZan\Http\Controllers\Api\Staff;

use YiZan\Services\Staff\SellerService;
use YiZan\Services\SellerCateService;
use YiZan\Services\PaymentService;
use DB;
/**
 * 服务人员
 */
class SellerController extends BaseController { 

    /**
     * 商家创建充值
     */
    public function recharge() {
        $data = PaymentService::createSellerPayLog(
            $this->sellerId,
            (int)$this->request('money'),
            $this->request('payment')
        );
        $this->output($data);
    }

	/**
	 * 商家评价列表
	 */
	public function evalist(){
		$result = SellerService::getOrderRates(
				$this->sellerId,
                (int)$this->request('type'),
				$this->request('page')
			);
		$this->outputData($result);
	}

	/**
	 * 评价回复
	 */
	public function evareply(){ 
		$result = SellerService::replyOrderRate(
                $this->sellerId,
				$this->request('id'),
				$this->request('content')
			);
		$this->output($result);
	}

	/**
	 * 商家经营类型
	 */
	public function trade(){
		$result = SellerCateService::getSellerCateLists($this->sellerId);
		$this->outputData($result);
	}

    /**
     * 添加银行卡信息
     */
    public function savebankinfo() {
        $result = SellerService::saveBankInfo(
            $this->sellerId,
            (int)$this->request('id'),
            $this->request('bank'),
            $this->request('bankNo'),
            $this->request('mobile'),
            $this->request('name'),
            $this->request('verifyCode')
        );
        $this->output($result);
    }

    /**
     * 获取银行卡信息
     */
    public function getbankinfo() {
        $result = SellerService::getBankInfo(
            $this->sellerId
        );
        $this->output($result);
    }

    /**
     * 删除银行卡信息
     */
    public function delbankinfo() {
        $result = SellerService::delBankInfo(
            $this->sellerId,
            (int)$this->request('id')
        );
        $this->output($result);
    }

}