<?php 
namespace YiZan\Services\System;

use YiZan\Models\System\Order;
use YiZan\Models\Seller;
use YiZan\Models\Goods;
use YiZan\Models\Refund;
use YiZan\Models\SellerWithdrawMoney;

class TotalViewService extends \YiZan\Services\BaseService 
{

    /**
     * [total 概况浏览]
     */
    public static function total() {
        $data = array();
        $data['order'] = Order::where('status', '<>', ORDER_STATUS_ADMIN_DELETE)->count(); //待审核服务
        $data['seller'] = Seller::where('is_check','0')->count(); //待审核服务人员
        $data['refund'] = Refund::where('status', '0')->count(); //待审核退款
        $data['withdraw'] = SellerWithdrawMoney::where('status', '0')->count(); //待审提现
        return $data;
    }
}
