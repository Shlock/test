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
class FundsController extends AuthController {

    public function index() {
        $useracount = $this->requestApi('useraccount.get');
        if ($useracount['code'] == 0) {
            View::share('useracount', $useracount['data']);
        }
        $args = Input::all();
        $args['beginTime'] = !empty($args['beginTime']) ? strval($args['beginTime']) : Time::toDate(UTC_TIME - 7 * 24 * 3600, 'Y-m-d');
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

        $withdraw = $this->requestApi('useraccount.lists', $args);
        if ($withdraw['code'] == 0) {
            View::share('list', $withdraw['data']['list']);
        }
        View::share('args', $args);
        return $this->display();
    }
    
    public function cate() {
        $useracount = $this->requestApi('useraccount.get');
        if ($useracount['code'] == 0) {
            View::share('useracount', $useracount['data']);
        }
        $args = Input::all();
        $args['beginTime'] = !empty($args['beginTime']) ? strval($args['beginTime']) : Time::toDate(UTC_TIME - 7 * 24 * 3600, 'Y-m-d');
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

        $withdraw = $this->requestApi('useraccount.lists', $args);
        if ($withdraw['code'] == 0) {
            View::share('list', $withdraw['data']['list']);
        }
        View::share('args', $args);
        return $this->display();
    }

    /**
     * 提现
     */
    public function withdraw() {

        $bankinfo = $this->requestApi('bankinfo.lists', ['pageSize' => 10000]);
        if ($bankinfo['code'] == 0) {
            View::share('bank', $bankinfo['data']['list']);
        }
        $useracount = $this->requestApi('useraccount.get');
        if ($useracount['code'] == 0) {
            View::share('money', $useracount['data']['money']);
        }
        return $this->display();
    }

    /**
     * 提现
     */
    public function get() {
        $bankinfo = $this->requestApi('bankinfo.get', Input::All());
        return Response::json($bankinfo);
        return $this->display();
    }

    /**
     * 提现申请
     */
    public function ajaxwithdraw() {
        $args = Input::all();
        $result = $this->requestApi('useraccount.withdraw', $args);
        return Response::json($result);
    }

    /**
     * 更改银行卡
     */
    public function changebankcard() {
        $result = $this->requestApi('bankinfo.get');
        if ($result['code'] == 0) {
            View::share('data', $result['data']);
        }
        return $this->display();
    }

    public function updabankcard() {
        $args = Input::all();
        $result = $this->requestApi('bankinfo.update', $args);
        return Response::json($result);
    }

    /* 验证码 */

    public function bankverify() {
        $args = Input::all();
        $result = $this->requestApi('bankinfo.bankinfoverify', $args);
        return Response::json($result);
    }

    /* 验证码 */

    public function userverify() {
        $args = Input::all();
        $result = $this->requestApi('useraccount.withdrawverify', $args);
        return Response::json($result);
    }

}
