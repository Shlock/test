<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\PaymentService;
use Lang, Validator;

/**
 * 支付方式
 */
class PaymentController extends BaseController 
{
    /**
     * 支付方式列表
     */
    public function lists()
    {
        $data = PaymentService::getList();
        
        $this->outputData($data);
    }
    /**
     * 支付方式更新
     */
    public function update() {
        $result = PaymentService::update(
            $this->request('code'),
            $this->request('config'),
            (int)$this->request('status')
        );
        $this->output($result);
    }
    
    /**
     * 更新状态
     */
    public function updateStatus() {
        $result = PaymentService::updatePaymentStatus(
            $this->request('code'),
            (int)$this->request('status')
        );
        $this->output($result);
    }
    
    
}