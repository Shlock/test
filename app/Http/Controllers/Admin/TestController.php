<?php

namespace YiZan\Http\Controllers\Admin;

use YiZan\Utils\Time;
use YiZan\Services\SmsService;
use YiZan\Services\System\PromotionService;
use YiZan\Models\Goods;
use YiZan\Models\UserCard;
use YiZan\Models\OrderGoods;
use YiZan\Models\Order;
use View,
    Input,
    Lang,
    Route,
    Page,
    Validator,
    Session,
    DB,
    Response,
    Redirect;

/**
 * 员工
 */
class TestController extends CommonController {

    public function __construct() {
        echo "child";
    }

    public function init() {
        echo "cinit";
    }

    public function index() {
        $order = Order::where("id", '6195')->first();
        $a = $order->toArray();
        print_r($a);
    }
}
