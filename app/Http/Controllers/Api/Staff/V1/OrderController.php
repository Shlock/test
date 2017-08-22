<?php 
namespace YiZan\Http\Controllers\Api\Staff;

use YiZan\Services\Staff\OrderService;
use YiZan\Services\Sellerweb\OrderService as SellerOrder;
use YiZan\Services\Staff\SellerService;
use Lang, Validator, View,
    Input,Time;

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

        $data = OrderService::getList
        (
            $this->sellerId,
            $this->staffId,
            (int)$this->request('status'),
            $this->request('date'),
            trim($this->request('keywords')),
            max((int)$this->request('page'), 1)
        );
        
		$this->outputData($data);
    }
    /**
     * 获取订单
     */
    public function detail()
    {
        $order = OrderService::getOrderById(
            $this->sellerId,
            $this->staffId,
            (int)$this->request('id')
        );
        $this->outputData($order);
    }
    /**
     * 更新订单(old)
     */
    /*public function status()
    {
        $result = OrderService::updateStaffOrder(intval($this->request('id')), $this->staffId, intval($this->request('status')));
        
        $this->output($result);
    }*/

    /**
     * 订单状态改变
     */
    public function status() {
        $result = OrderService::updateOrder(
            $this->sellerId,
            $this->staffId,
            (int)$this->request('id'),
            (int)$this->request('status'),
            trim($this->request('remark'))

        );
        $this->output($result);
    }
    
    /**
     * 完成订单
     */
    public function complete() {
        $data = OrderService::completeOrder(
            $this->staffId,
            (int)$this->request('id'),
              $this->request('code')
        );
        $this->output($data);
    }

    /**
     * 日程列表
     */
    public function schedule() {
        $data = OrderService::getSchedule(
            $this->staffId,
            max((int)$this->request('type'),1),
            max((int)$this->request('page'),1)
        );
        $this->outputData($data);
    }


    /**
     * 订单分配人员
     */
    public function designate() {
        $result = OrderService::designate(
            $this->sellerId,
            (int)$this->request('id'),
            (int)$this->request('staffId')
        );
        $this->output($result);
    }

    /**
     * 服务、配送人员列表
     */
    public function stafflist(){
        $result = SellerService::getStaffLists(
            $this->sellerId, 
            (int)$this->request('type')
        );
        $this->outputData($result);
    }

    /**
     * 经营统计
     */
    public function statistics(){
        $days = max((int)$this->request('days'),1);
        $data = OrderService::businessStat(
            $this->sellerId,
            $days
        );
        $args = [
            'token' => $this->token,
            'userId' => $this->userId,
            'days' => $days
        ];
        
        //print_r($data);

        View::share('args', $args);
        View::share('data', $data);
        return View::make('api.statistics.index');
    }

    /**
     *  经营分类统计，分类收入统计
     */
    public function cateincome(){
        $args = Input::all();
        $args['beginTime'] = !empty($args['beginTime']) ? strval($args['beginTime']) : Time::toDate(UTC_TIME - 1 * 24 * 3600, 'Y-m-d');
        $args['endTime'] = !empty($args['endTime']) ? strval($args['endTime']) : Time::toDate(UTC_TIME, 'Y-m-d');
        $beginTime = strtotime($args['beginTime'].' 00:00:00')-8*3600;
        $endTime = strtotime($args['endTime'].' 00:00:00')-8*3600;
        $data = SellerOrder::cateMoney($this->sellerId,$beginTime,$endTime);
        View::share('args', $args);
        View::share('data', $data);
        return View::make('api.statistics.cateincome');
    }

    /**
     *  经营分类统计，分类收入统计
     */
    public function seller(){
        $args = Input::all();
        $args['beginTime'] = !empty($args['beginTime']) ? strval($args['beginTime']) : Time::toDate(UTC_TIME - 1 * 24 * 3600, 'Y-m-d');
        $args['endTime'] = !empty($args['endTime']) ? strval($args['endTime']) : Time::toDate(UTC_TIME, 'Y-m-d');
        $beginTime = strtotime($args['beginTime'].' 00:00:00')-8*3600;
        $endTime = strtotime($args['endTime'].' 00:00:00')-8*3600;
        $sellerId = $args['sellerId'];
        $status0 = SellerOrder::statusMoney($this->sellerId,$beginTime,$endTime,$sellerId);
        $status1 = SellerOrder::statusMoney($this->sellerId,$beginTime,$endTime,$sellerId,[ORDER_STATUS_FINISH_SYSTEM,ORDER_STATUS_FINISH_USER]);
        $status2 = SellerOrder::statusMoney($this->sellerId,$beginTime,$endTime,$sellerId,[ORDER_STATUS_BEGIN_USER,ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_PAY_DELIVERY,ORDER_STATUS_AFFIRM_SELLER]);
        $status3 = SellerOrder::statusMoney($this->sellerId,$beginTime,$endTime,$sellerId,[ORDER_STATUS_START_SERVICE,ORDER_STATUS_FINISH_STAFF]);
        $status4 = SellerOrder::statusMoney($this->sellerId,$beginTime,$endTime,$sellerId,[ORDER_STATUS_CANCEL_USER,ORDER_STATUS_CANCEL_AUTO,ORDER_STATUS_CANCEL_SELLER,ORDER_STATUS_CANCEL_ADMIN]);
        $status5 = SellerOrder::statusMoney($this->sellerId,$beginTime,$endTime,$sellerId,[ORDER_STATUS_REFUND_AUDITING,ORDER_STATUS_REFUND_HANDLE,ORDER_STATUS_REFUND_FAIL,ORDER_STATUS_REFUND_SUCCESS]);
        $status6 = SellerOrder::statusMoney($this->sellerId,$beginTime,$endTime,$sellerId,[ORDER_STATUS_USER_DELETE]);
        
        $status =[$status0,$status1,$status2,$status3,$status4,$status5,$status6];
        $lists = [['name'=>'全部订单'],['name'=>'已完成订单'],['name'=>'待发货订单'],['name'=>'待完成订单'],['name'=>'已撤销订单'],['name'=>'已退款订单'],['name'=>'已删除订单']];
        foreach($status as $key => $list) {
            $lists[$key]['money'] = $list[0]->money;
            $lists[$key]['status'] = $key;
        }
        View::share('args', $args);
        View::share('data', $lists);
        View::share('name', $status[0][0]->name);
        return View::make('api.statistics.seller');
    }

    /**
     *  经营分类统计，分类收入统计
     */
    public function olist(){
        $args = Input::all();
        $args['beginTime'] = !empty($args['beginTime']) ? strval($args['beginTime']) : Time::toDate(UTC_TIME - 1 * 24 * 3600, 'Y-m-d');
        $args['endTime'] = !empty($args['endTime']) ? strval($args['endTime']) : Time::toDate(UTC_TIME, 'Y-m-d');
        $beginTime = strtotime($args['beginTime'].' 00:00:00')-8*3600;
        $endTime = strtotime($args['endTime'].' 00:00:00')-8*3600;
        $cateId = $args['cateId'];
        $status = $args['status'];
        View::share('args', $args);
        $lists = [['name'=>'全部订单'],['name'=>'已完成订单'],['name'=>'待发货订单'],['name'=>'待完成订单'],['name'=>'已撤销订单'],['name'=>'已退款订单'],['name'=>'已删除订单']];
        View::share('name', $lists[$status]['name']);
        $rows = SellerOrder::getStatusList($this->sellerId,$cateId,$status,$beginTime,$endTime);
        View::share('data', $rows);
        return View::make('api.statistics.lists');
    }
    /**
     * 获取订单
     */
    public function oitem()
    {
        $order = OrderService::getOrderById(
            $this->sellerId,
            $this->staffId,
            (int)$this->request('orderId')
        );
        //return ['code'=>0,'msg'=>$order['orderGoods']];
        View::share('data', $order);
        return View::make('api.statistics.order');
    }
}