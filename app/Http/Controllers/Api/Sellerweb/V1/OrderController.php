<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\OrderService;
use Lang, Validator;

/**
 * 订单
 */
class OrderController extends BaseController 
{
    /**
     * 订单列表
     */
    public function lists()
    {
        $data = OrderService::getSellerList
        (
            $this->sellerId,
            $this->request('sn'),
            $this->request('orderType'),
            $this->request('provinceId'),
            $this->request('cityId'),
            $this->request('areaId'),
            intval($this->request('status')),
            intval($this->request('beginTime')),
            intval($this->request('endTime')),
            intval($this->request('mobile')),
            trim($this->request('staffName')),
            trim($this->request('name')),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
		$this->outputData($data);
    }
    /**
     * 订单列表
     */
    public function getcarstaff()
    {
        $data = OrderService::getCarList
        (
            $this->staffId,
            intval($this->request('status')),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        $this->outputData($data);
    }
    /**
     * 获取订单
     */
    public function detail()
    {
        $order = OrderService::getSellerOrderDetail($this->sellerId, intval($this->request('orderId')));
        
        $this->outputData($order);
    }
    /**
     * 开始服务
     */
    public function start()
    {
        $result = OrderService::updateSellerOrder(intval($this->request('orderId')), $this->sellerId, ORDER_STATUS_START_SERVICE);
        
        $this->output($result);
    }
    /**
     * 完成服务
     */
    public function finish()
    {
        $result = OrderService::updateSellerOrder(intval($this->request('orderId')), $this->sellerId, ORDER_STATUS_FINISH_SERVICE);
        
        $this->output($result);
    }
    /**
     * 更新订单
     **/
    public function updatestatus()
    {
        $result = OrderService::updateSellerOrder(intval($this->request('orderId')), $this->sellerId, intval($this->request('status')), $this->request('content'));
        
        $this->output($result);
    }
    /**
     * 删除订单
     */
    public function delete()
    {
        die;//暂先隐藏此功能
        $result = OrderService::deleteOrder($this->sellerId, intval($this->request('id')));
        
        $this->output($result);
    }
    /**
     * 商家指派人员
     */
    public function designate() {
        $result = OrderService::designate(
            $this->request('orderId'),
            (int)$this->request('staffId'),
            $this->sellerId
        );
        $this->output($result);
    }
    /**
     * 随机指派人员
     */
    public function ranupdate() {
        $result = OrderService::ranUpdate(
            (int)$this->request('orderId'),
            $this->request('serviceContent'),
            $this->request('money')
        );
        $this->output($result);
    }

    /**
     *  经营分类统计，分类收入统计
     */
    public function cateincome(){
        $endTime = strtotime($this->request('endTime') . ' 00:00:00')-8*3600;
        $beginTime = strtotime($this->request('beginTime'). ' 00:00:00')-8*3600;
        //return ['code'=>0,date('Y-m-d H:i:s',$endTime),date('Y-m-d H:i:s',$beginTime)];
        $data = OrderService::cateMoney($this->sellerId,$beginTime,$endTime);
        $this->output($data);
    }

    /**
     *  经营分类统计，分类收入统计
     */
    public function statusincome(){
        $endTime = strtotime($this->request('endTime'). ' 00:00:00')-8*3600;
        $beginTime = strtotime($this->request('beginTime'). ' 00:00:00')-8*3600;
        $sellerId = $this->request('sellerId');
        $status0 = OrderService::statusMoney($this->sellerId,$beginTime,$endTime,$sellerId);
        $status1 = OrderService::statusMoney($this->sellerId,$beginTime,$endTime,$sellerId,[ORDER_STATUS_FINISH_SYSTEM,ORDER_STATUS_FINISH_USER]);
        $status2 = OrderService::statusMoney($this->sellerId,$beginTime,$endTime,$sellerId,[ORDER_STATUS_BEGIN_USER,ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_PAY_DELIVERY,ORDER_STATUS_AFFIRM_SELLER]);
        $status3 = OrderService::statusMoney($this->sellerId,$beginTime,$endTime,$sellerId,[ORDER_STATUS_START_SERVICE,ORDER_STATUS_FINISH_STAFF]);
        $status4 = OrderService::statusMoney($this->sellerId,$beginTime,$endTime,$sellerId,[ORDER_STATUS_CANCEL_USER,ORDER_STATUS_CANCEL_AUTO,ORDER_STATUS_CANCEL_SELLER,ORDER_STATUS_CANCEL_ADMIN]);
        $status5 = OrderService::statusMoney($this->sellerId,$beginTime,$endTime,$sellerId,[ORDER_STATUS_REFUND_AUDITING,ORDER_STATUS_REFUND_HANDLE,ORDER_STATUS_REFUND_FAIL,ORDER_STATUS_REFUND_SUCCESS]);
        $status6 = OrderService::statusMoney($this->sellerId,$beginTime,$endTime,$sellerId,[ORDER_STATUS_USER_DELETE]);
        
        $this->output([$status0,$status1,$status2,$status3,$status4,$status5,$status6]);
    }

    /**
     *  经营分类统计，分类收入统计
     */
    public function statusbills(){
        $endTime = strtotime($this->request('endTime'). ' 00:00:00')-8*3600;
        $beginTime = strtotime($this->request('beginTime'). ' 00:00:00')-8*3600;
        //return ['code'=>0,date('Y-m-d H:i:s',$endTime),date('Y-m-d H:i:s',$beginTime)];
        $cateId = $this->request('sellerId');
        $status = $this->request('status');
        $rows = OrderService::getStatusList($this->sellerId,$cateId,$status,$beginTime,$endTime);
        $this->output($rows);
    }
}