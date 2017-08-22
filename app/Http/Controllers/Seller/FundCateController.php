<?php

namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Funds;
use View,
    Input,
    Lang,
    Route,
    Page,
    Validator,
    Session,
    Response,
    Time;

/**
 * 资金
 */
class FundCateController extends AuthController {

    public function index() {
        $args = Input::all();
        $args['beginTime'] = !empty($args['beginTime']) ? strval($args['beginTime']) : Time::toDate(UTC_TIME - 1 * 24 * 3600, 'Y-m-d');
        $args['endTime'] = !empty($args['endTime']) ? strval($args['endTime']) : Time::toDate(UTC_TIME, 'Y-m-d');
        if (!empty($args['beginTime'])) {
            if (!empty($args['endTime'])) {
                if ($args['beginTime'] > $args['endTime']) {
                    return $this->error("开始时间不能大于结束时间");
                }
            } else {
                return $this->error("结束时间未选择");
            }
        }
        $lists = $this->requestApi('order.cateincome', $args);
        //die(print_r($lists,true));
        View::share('list', $lists);
        View::share('args', $args);
        return $this->display();
    }
    
    public function seller() {
        $args = Input::all();
        $args['beginTime'] = !empty($args['beginTime']) ? strval($args['beginTime']) : Time::toDate(UTC_TIME - 1 * 24 * 3600, 'Y-m-d');
        $args['endTime'] = !empty($args['endTime']) ? strval($args['endTime']) : Time::toDate(UTC_TIME, 'Y-m-d');
        if (!empty($args['beginTime'])) {
            if (!empty($args['endTime'])) {
                if ($args['beginTime'] > $args['endTime']) {
                    return $this->error("开始时间不能大于结束时间");
                }
            } else {
                return $this->error("结束时间未选择");
            }
        }
        $status = $this->requestApi('order.statusincome', $args);
        $lists = [['name'=>'全部订单'],['name'=>'已完成订单'],['name'=>'待发货订单'],['name'=>'待完成订单'],['name'=>'已撤销订单'],['name'=>'已退款订单'],['name'=>'已删除订单']];
        foreach($status as $key => $list) {
            $lists[$key]['money'] = $list[0]['money'];
            $lists[$key]['status'] = $key;
        }
        View::share('list', $lists);
        View::share('args', $args);
        View::share('name', $status[0][0]['name']);
        return $this->display();
    }
    
    public function lists() {
        $args = Input::all();
        $args['beginTime'] = !empty($args['beginTime']) ? strval($args['beginTime']) : Time::toDate(UTC_TIME - 1 * 24 * 3600, 'Y-m-d');
        $args['endTime'] = !empty($args['endTime']) ? strval($args['endTime']) : Time::toDate(UTC_TIME, 'Y-m-d');
        if (!empty($args['beginTime'])) {
            if (!empty($args['endTime'])) {
                if ($args['beginTime'] > $args['endTime']) {
                    return $this->error("开始时间不能大于结束时间");
                }
            } else {
                return $this->error("结束时间未选择");
            }
        }
        $result = $this->requestApi('order.statusbills', $args);
        //die(print_r($result,true));
        if(isset($result['code'])) {
            View::share('list', []);
        } else {
            View::share('list', $result);
        }
        View::share('args', $args);
        View::share('name', $result[0]['name']);
        View::share('searchUrl', u('FundCate/lists', ['status' => $args['status'], 'nav' => $args['nav'],'sellerId'=>$args['sellerId']]));
        return $this->display();
    }

    /**
     * 订单详细
     */
    public function detail() {
        $args['orderId'] = Input::get('orderId');
        if ($args['orderId'] > 0) {
            $result = $this->requestApi('order.detail', $args);
            if ($result['code'] == 0) {
                View::share('data', $result['data']);
                View::share('staff', $result['data']['staffList']);
            }
        }
        return $this->display();
    }
}
