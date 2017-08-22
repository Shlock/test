<?php

namespace YiZan\Http\Controllers\Wap;

use YiZan\Utils\Time;
use View,
    Input,
    Lang,
    Route,
    Page;

/**
 * 首页
 */
class IndexController extends BaseController {

    //
    public function __construct() {
        parent::__construct();
        View::share('nav', 'index');
    }

    // h5支付测试
    public function paytest() {
        return $this->display();
    }

    // 创建支付日志
    public function createpaylog() {
        $order = \YiZan\Models\Order::where("pay_fee", ">", 0)
                ->where("status", ORDER_STATUS_BEGIN_USER)
                ->first();

        $result = \YiZan\Services\OrderService::payOrder($order->user_id, $order->id, "alipay");

        die(json_encode($result["data"]));
    }

    /**
     * 首页信息 
     */
    public function index() {

        $defaultAddress = Session::get("defaultAddress");
        if (!$defaultAddress) {
            $address = $this->requestApi('user.address.getdefault');
            $defaultAddress = $address['data'];
            Session::put("defaultAddress", $address['data']);
        }
        if (isset($defaultAddress['here']) && $defaultAddress['here'] == 1) {
            $defaultAddress = [];
        }

        $args = Input::get();
        if (!empty($args['address']) && !empty($args['mapPointStr'])) {
            $defaultAddress['address'] = Input::get('address');
            $defaultAddress['mapPointStr'] = Input::get('mapPointStr');
            Session::put("defaultAddress", $defaultAddress);
        }

        //首页轮播图信息 + 菜单 + 活动
        $data = $this->requestApi('config.index', ['mapPoint' => $defaultAddress['mapPointStr']]);
        //print_r($data);
        if ($data['code'] == 0)
            View::share('data', $data['data']);
        //print_r($data['data']['sellers']);
        $seller_cates = $this->requestApi('seller.catelists');
        if ($seller_cates['code'] == 0) {
            View::share("sellerCates", $seller_cates['data']);
        }
        //View::share('location', Session::get('default_city')); 
        View::share("orderData", $defaultAddress);
        return $this->display();
    }

    /**
     * 城市定位
     * @return [type] [description]
     */
    public function location() {
        View::share('service_citys', $this->getServiceCitys());
        return $this->display();
    }

    public function here() {
        Session::put('orderData.here', 1);
    }

    private function formatAdv($advs) {
        foreach ($advs as $key => $value) {
            switch ($value['type']) {
                case '1':
                    $advs[$key]['url'] = u('Goods/index', array('categoryId' => $value['arg']));
                    break;
                case '2':
                    $advs[$key]['url'] = u('Goods/detail', array('goodsId' => $value['arg']));
                    break;
                case '3':
                    $advs[$key]['url'] = u('Seller/detail', array('sellerId' => $value['arg']));
                    break;
                case '4':
                    $advs[$key]['url'] = u('Article/detail', array('articleId' => $value['arg']));
                    break;
                case '5':
                    $advs[$key]['url'] = $value['arg'];
                    break;
                case '6':
                    $advs[$key]['url'] = u('Reservation/index');
                    break;
                default:
                    $advs[$key]['url'] = u('Goods/index');
                    break;
            }
        }
        return $advs;
    }

    public function activity() {
        return $this->display();
    }

}
