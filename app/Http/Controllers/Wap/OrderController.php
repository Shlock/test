<?php

namespace YiZan\Http\Controllers\Wap;

use Input,
    View,
    Redirect,
    Request,
    Time,
    Response,
    Cache;

/**
 * 用户订单控制器
 */
class OrderController extends UserAuthController {

    protected $_config = ''; //基础配置信息

    public function __construct() {
        parent::__construct();
        View::share('nav', 'mine');
        $this->_config = Session::get('site_config');
        $sellerServiceTel = \YiZan\Services\SystemConfigService::getConfigByCode('seller_service_tel');
        View::share('sellerServiceTel', $sellerServiceTel);
        View::share('config', $this->_config);
    }

    /**
     * 订单列表页
     */
    public function index() {
        $args = Input::all();
        $args['status'] = (int) Input::get('status');
        $list = $this->requestApi('order.lists', $args);
        View::share('args', $args);
        View::share('nav_back_url', u('Index/index'));
        if ($list['code'] == 0)
            View::share('list', $list['data']);
        if (Input::ajax()) {
            return $this->display('item');
        } else {
            return $this->display();
        }
    }

    /**
     * 订单详情
     */
    public function detail() {
        $id = (int) Input::get('id');
        $result = $this->requestApi('order.detail', array('id' => $id));
        View::share('data', $result['data']);
        $payments = $this->getPayments();
        View::share('payments', $payments);

        //活动名称
        $activity_result = $this->requestApi('Activity.getshare', ['orderId' => $id]);
        View::share('activity', $activity_result['data']);

        if (!empty($activity_result['data'])) {
            $brief_count = count($result['data']['brief']);
            $desc = $result['data']['brief'][rand(0, $brief_count - 1)];
            View::share('desc', $desc);

            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $url = $protocol . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI];
            $weixin_arrs = $this->requestApi('Useractive.getweixin', array('url' => $url));

            if ($weixin_arrs['code'] == 0) {
                View::share('weixin', $weixin_arrs['data']);
            }
            $link_url = u('UserCenter/obtaincoupon', array('orderId' => $id, 'activityId' => $activity_result['data']['id']));
            View::share('link_url', $link_url);
        }

        View::share('nav_back_url', u('Order/index'));
        return $this->display();
    }

    /**
     * [wxpay 微信支付]
     */
    public function wxpay() {
        $args = Input::all();
        $url = u('Order/pay', $args);
        $openid = Session::get('wxpay_open_id');
        if (empty($openid)) {
            $url = u('Weixin/authorize', ['url' => urlencode($url)]);
        } else {
            $url .= '&openId=' . $openid;
        }
        return Redirect::to($url);
    }

    public function unionPay() {
        $args = Input::all();
    }

    /**
     * 订单支付
     */
    public function pay() {
        $args = Input::all();
        if (isset($args['payment']) && $args['payment'] == 'weixinJs') {
            Session::put('wxpay_open_id', $args['openId']);
            Session::put('pay_payment', 'weixinJs');
            Session::save();
            return Redirect::to(u('Order/pay', ['id' => $args['id']]));
        }
        /* 货到付款 */
        if (isset($args['payment']) && $args['payment'] == 'cashOnDelivery') {
            return Redirect::to(u('Order/delivery', ['id' => $args['id']]));
        }
        if (!isset($args['payment'])) {
            $args['payment'] = Session::get('pay_payment');
            $args['openId'] = Session::get('wxpay_open_id');
        }
        $args['extend']['url'] = Request::fullUrl();

        if (!empty($args['openId'])) {
            $args['extend']['openId'] = $args['openId'];
        }
        if (!empty($args['balancePay'])) {
            $args['extend']['balancePay'] = (int) $args['balancePay'];
        }
        $pay = $this->requestApi('order.pay', $args);
        //var_dump($pay);
        //die();
        if ($pay['code'] == 0) {
            if (isset($pay['data']['payRequest']['html'])) {
                echo $pay['data']['payRequest']['html'];
                exit;
            }
            View::share('pay', $pay['data']['payRequest']);
        }

        $result = $this->requestApi('order.detail', array('id' => $args['id']));
        if ($result['code'] == 0) {
            View::share('data', $result['data']);
            if ($result['data']['payStatus']+0 > 0) {
                return Redirect::to(u('Order/detail', ['id' => $result['data']['id']]));
            }
        }
        View::share('payment', $args['payment']);
        return $this->display('wxpay');
    }

    /**
     * 外卖下单
     */
    public function detection() {
        $orderdata = Session::get("disps");
        $shop = $this->requestApi("Shopping.getCart");
        if ($shop['data']) {
            $args = [
                'mobileId' => $orderdata['mobileId'],
                'addressId' => $orderdata['addressId'],
                'type' => $orderdata['type'],
                'remark' => $orderdata['remark'],
                'goods' => $shop['data']
            ];
            $result = $this->requestApi('restaurant.order.create', $args);
            View::share('data', $result['data']);
            if ($result['code'] != 0) {
                return $this->error($result['msg']);
            } else {
                $this->requestApi("Shopping.clearCart");
                Session::put("id", $result['data']['id']);
                Session::put("disps", null);
                Session::save();
            }
        } else {
            $orderId_ok = Session::get("id");
            if ($orderId_ok) {
                Session::put("id", null);
                Session::save();
                return $this->error("不要重复刷新订单", u('Order/detail', array('id' => $orderId_ok)));
            } else {
                return $this->error("你还没有挑选菜品");
            }
        }
        $payments = $this->getPayments();
        View::share('payments', $payments);
        View::share('orderdata', $orderdata);
        return $this->display();
    }

    /**
     * 其他ALL下单
     */
    public function detections() {
        $orderdata = Session::get("orderData");
        $id = (int) input::get('id');
        $args = [
            'mobileId' => $orderdata['mobileId'],
            'addressId' => $orderdata['addressId'],
            'id' => $id,
        ];
        $result = $this->requestApi('service.order.create', $args);
        return Response::json($result);
    }

    /**
     * 去支付
     */
    public function toPay() {
        $id = input::get("orderId");
        $result = $this->requestApi('order.detail', array('id' => $id));
        if ($result['code'] == 0) {
            $orderdata['total_price'] = $result['data']['payFee'];
            View::share('orderdata', $orderdata);
            View::share('data', $result['data']);
        }
        $payments = $this->getPayments();
        View::share('payments', $payments);
        return $this->display("detection");
    }

    /**
     * delivery 餐到付款
     */
    public function delivery() {
        $id = (int) Input::get('id');
        $result = $this->requestApi('order.delivery', array('orderId' => $id));
        return Redirect::to(u('Order/detail', ['id' => $id]));
    }

    /**
     * [cancelorder 取消订单]
     */
    public function cancelorder() {
        $args = Input::all();
        $result = $this->requestApi('order.cancel', $args);
        return Response::json($result);
    }

    /**
     * [delorder 删除订单]
     */
    public function delorder() {
        $id = (int) Input::get('id');
        $result = $this->requestApi('order.delete', array('id' => $id));
        return Response::json($result);
    }

    /**
     * [confirmorder 订单完成]
     */
    public function confirmorder() {
        $id = (int) Input::get('id');
        $result = $this->requestApi('order.confirm', array('id' => $id));
        return Response::json($result);
    }

    /**
     * [commentlist 订单评论页面]
     */
    public function commentlist() {
        $id = (int) Input::get('oid');
        $result = $this->requestApi('order.detail', array('id' => $id));
        $system_order_pass = $this->getConfig('system_order_pass');
        View::share('system_order_pass', $system_order_pass);
        if ($result['code'] > 0) {
            return $this->error($result['msg']);
        }
        $order = $result['data'];
        if ($order['payEndTime'] > UTC_TIME) {
            View::share('pay_end_str', Time::getEndTimelag($order['payEndTime']));
        }
        View::share('data', $order);
        return $this->display();
    }

    /**
     * [docomment 评论订单]
     */
    public function docommentlist() {
        $data = Input::all();
        $result = $this->requestApi("rate.order.create", $data);
        return Response::json($result);
    }

    /**
     * [comment 订单评论页面]
     */
    public function comment() {
        $orderId = (int) Input::get('orderId');
        if ($orderId < 1) {
            return Redirect::to(u('Order/index'));
        }
        $order = $this->requestApi('order.detail', array('id' => $orderId));
        if (!$order['data']['isCanRate']) {
            return Redirect::to(u('Order/detail', ['id' => $orderId]));
        }
        View::share('order', $order['data']);
        return $this->display();
    }

    /**
     * [docomment 评论订单]
     */
    public function docomment() {
        $data = Input::all();
        $result = $this->requestApi("rate.order.create", $data);
        return Response::json($result);
    }

    /**
     * [refund 申请退款]
     */
    public function refund() {
        $id = (int) Input::get('id');
        if ($id < 1) {
            return $this->error('非法请求');
        }
        $result = $this->requestApi('order.detail', array('orderId' => $id));
        //var_dump($result['data']);
        if ($result['code'] == 0) {
            View::share('data', $result['data']);
        }
        return $this->display();
    }

    /**
     * [dorefund 申请退款]
     */
    public function dorefund() {
        $data = Input::all();
        //var_dump($data);
        $result = $this->requestApi("order.refund", $data);
        return Response::json($result);
    }

    /*
     * 日程列表
     */

    public function schedule() {
        $args = Input::all();
        $args['status'] = (int) Input::get('status');
        $list = $this->requestApi("order.schedule", $args);
        View::share('args', $args);
        if ($list['code'] == 0)
            View::share('list', $list['data']);
        if (Input::ajax()) {
            return $this->display('item');
        } else {
            return $this->display('index');
        }
    }

    /**
     * 保存来自下单选择服务人员的时间参数
     */
    public function saveOrderData() {
        $args = Input::get("orderData");
        if (!$args['orderTime'])
            die(0);
        Session::put("orderData.staffId", $args['staffId']);
        Session::put("orderData.goodsId", $args['goodsId']);
        Session::put("orderData.staffName", $args['staffName']);
        Session::put("orderData.orderTime", \YiZan\Utils\Time::toDate($args['orderTime'], "Y-m-d H:i"));
        Session::save();
        echo 1;
    }

    /**
     * 保存地址
     */
    public function saveOrderDataAdd() {
        $address = Input::get("address");
        if (empty($address))
            die(0);
        Session::put("orderData.address", $address);
        Session::save();
        echo 1;
    }

    /**
     * 备注
     */
    public function remark() {
        return $this->display();
    }

    /**
     * 保存备注
     */
    public function saveOrderDataRemark() {
        $disps['remark'] = Input::get("remark");
        Session::put("disps", $disps);
        Session::save();
        echo 1;
    }

    public function reimburse() {
        $id = (int) Input::get('id');
        $result = $this->requestApi('order.details', array('orderId' => $id));

        if ($result['code'] > 0) {
            return $this->error($result['msg']);
        }
        $order = $result['data'];

        if ($order['payEndTime'] > UTC_TIME) {
            View::share('pay_end_str', Time::getEndTimelag($order['payEndTime']));
        }

        View::share('data', $order);
        return $this->display();
    }

    /**
     * 创建订单 - 开始
     */
    public function order() {
        $ids = explode(',', Input::get('cartIds'));
        $result = $this->requestApi('shopping.getCartList', ['ids' => $ids]);
        if (empty($result['data']) || $result['code'] != 0) {
            return $this->error("一次只能下单一种类型", u('GoodsCart/index'));
// 		    return Redirect::to();
        }
        View::share('cartIds', implode(',', $ids));
        View::share('data', $result['data']);
        if ((int) Input::get('addressId') > 0) {
            $address = $this->requestApi('user.address.get', ['id' => (int) Input::get('addressId')]);
        } else {
            $address = $this->requestApi('user.address.getdefault');
        }
        Session::put("defaultAddress", $address['data']);
        Session::save();

        if ((int) Input::get('proId') > 0) {
            $promotion = $this->requestApi('user.promotion.get', ['id' => (int) Input::get('proId')]);
        } elseif ((int) Input::get('cancel') != 1) {
            //$promotion = $this->requestApi('user.promotion.first');
        }
        View::share('promotion', $promotion['data']);

        $fee = $this->requestApi('order.compute', ['cartIds' => $ids, 'promotionSnId' => $promotion['data']['id']]);
        //return ['code'=>0,$fee['data']];
        View::share('fee', $fee['data']);

        $system_order_pass = $this->getConfig('system_order_pass');
        view::share("time", $system_order_pass / 60);
        View::share('address', $address['data']);

        return $this->display();
    }

    /**
     * 创建订单 - 开始
     */
    public function toOrder() {
        $args = Input::all();
        $args['cartIds'] = explode(',', $args['cartIds']);
        $result = $this->requestApi('order.create', $args);
        return Response::json($result);
    }

    /**
     * 催单
     */
    public function urge() {
        $id = Input::get('id');
        $result = $this->requestApi('order.urge', ['id' => $id]);
        return Response::json($result);
    }

    /**
     * 支付选择页面
     */
    public function orderpay() {
        $orderId = (int) Input::get('orderId');
        $order = $this->requestApi('order.detail', ['id' => $orderId]);
        if ($order['code'] > 0 || $order['payStatus'] == 1) {
            return Redirect::to('Order/index');
        }
        $payments = $this->getPayments();
        View::share('payments', $payments);
        View::share('orderId', $orderId);
        return $this->display();
    }

    /**
     * 收银台
     */
    public function cashierdesk() {
        $orderId = (int) Input::get('orderId');
        $order = $this->requestApi('order.detail', ['id' => $orderId]);
        $data = $order['data'];
        if ($order['code'] > 0 || $data['payStatus'] == 1) {
            return Redirect::to('Order/index');
        }
        $payments = $this->getPayments();
        unset($payments['balancePay']);
        unset($payments['cashOnDelivery']);

        View::share('payments', $payments);
        View::share('data', $data);
        return $this->display();
    }

    /**
     * 余额支付
     */
    public function balancepay() {
        $args = Input::all();
        $result = $this->requestApi('order.pay', $args);

        if ($result['code'] == 0) {
            return Redirect::to(u('Order/detail', ['id' => $args['id']]));
        } else {
            return $this->error($result['msg']);
        }
    }

    /**
     * 退款详情
     */
    public function refundview() {
        $orderId = (int) Input::get('orderId');
        $result = $this->requestApi('order.refundview', ['orderId' => $orderId]);
        //print_r($result);
        View::share('data', $result['data']);
        return $this->display();
    }

    /**
     * 不显示优惠信息
     */
    public function notshow() {
        $orderId = (int) Input::get('orderId');
        $result = $this->requestApi('order.notshow', ['orderId' => $orderId]);
    }

}
