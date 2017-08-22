<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb\Order;

use YiZan\Services\Sellerweb\OrderRateService;
use YiZan\Http\Controllers\Api\Sellerweb\BaseController;
use Lang, Validator;

/**
 * 评价管理
 */
class RateController extends BaseController 
{
    /**
     * 评价列表
     */
    public function lists()
    {
        $data = OrderRateService::getSystemList (
            $this->sellerId,
            $this->request('userMobile'),
            $this->request('goodsName'),
            $this->request('sellerMobile'),
            $this->request('orderSn'),
            intval($this->request('beginTime')),
            intval($this->request('endTime')),
            $this->request('result'),
            intval($this->request('replyStatus')),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		$this->outputData($data);
    } 
    /**
     * 根据ID获取评价
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function get(){
        $data = OrderRateService::getReply(
            $this->sellerId,
            $this->request('id')
        );
        $this->outputData($data);
    }
    /**
     * 评价回复
     */
    public function reply()
    {
        $result = OrderRateService::replySystem(
            $this->sellerId,
            intval($this->request('id')),
            $this->request('content')
        );
        
        $this->output($result);
    }
    /**
     * 评价删除
     */
    public function delete()
    {
        $result = OrderRateService::deleteSystem(intval($this->request('id')));
        
        $this->output($result);
    }
}