<?php

namespace YiZan\Services;

use YiZan\Models\Order;
use YiZan\Models\Goods;
use YiZan\Models\System\SellerStaffExtend;
use YiZan\Models\UserAddress;
use YiZan\Models\UserMobile;
use YiZan\Models\OrderGoods;
use YiZan\Models\User;
use YiZan\Models\GoodsExtend;
use YiZan\Models\Payment;
use YiZan\Models\Restaurant;
use YiZan\Models\PromotionSn;
use YiZan\Models\UserPayLog;
use YiZan\Services\SellerMoneyLogService;
use YiZan\Services\SellerService;
use YiZan\Models\SellerMoneyLog;
use YiZan\Models\SellerExtend;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use Exception,
    DB,
    Lang,
    Validator,
    App;

class OrderService extends BaseService {

    /**
     * 获取订单
     * @param  [type] $userId  [description]
     * @param  [type] $orderId [description] 825722
     * @return [type]          [description]
     */
    public static function getOrder($userId, $orderId) {
        //检测超时支付订单
        self::endOrder();

        return Order::where('id', $orderId)
                        ->where('user_id', $userId)
                        ->with('seller', 'goods', 'promotion', 'staff', 'userRefund', 'orderComplain')
                        ->first();
    }

    /**
     * 根据订单编号获取订单
     * @param  [int] $userId  	 [会员编号] 
     * @param  [int] $orderId  	 [订单编号] 
     * @return [object]          [订单对象]
     */
    public static function getOrderById($userId, $orderId) {
        //检测超时支付订单
        self::endOrder();
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => ''
        );
        $res = Order::where('user_id', $userId)->where('id', $orderId)->with('cartSellers', 'seller', 'staff', 'user')->first()->toArray();
        if ($res['id'] == false) {
            return false;
        }
        $res['invoiceTitle'] = $res['invoiceRemark'];
        $res['price'] = $res['totalFee'];
        $res['giftContent'] = $res['giftRemark'];
        $res['sellerName'] = $res['seller']['name'];
        $res['sellerTel'] = $res['seller']['serviceTel'];
        $res['staffName'] = $res['staff']['name'];
        $res['staffMobile'] = $res['staff']['mobile'];
        return $res;
    }

    /**
     * 社区订单列表
     * @param  [type] $userId [description]
     * @param  [type] $status [description]
     * @param  [type] $page   [description]
     * @return [type]         [description]
     */
    public static function getList($userId, $status, $page) {
        self::endOrder();
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => ''
        );

        $rules = array(
            'userId' => ['required'],
        );

        $messages = array(
            'userId.required' => '60404',
        );

        $validator = Validator::make([
                    'userId' => $userId,
                        ], $rules, $messages);

        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        //检测超时支付订单
        self::endOrder();
        $data = ['commentNum' => 0, 'orderList' => []];
        $data['commentNum'] = Order::where('user_id', $userId)
                ->whereIn('status', [
                    ORDER_STATUS_FINISH_SYSTEM,
                    ORDER_STATUS_FINISH_USER
                ])->where('is_rate', 0)
                ->count();
        $list = Order::where('user_id', $userId);
        if ($status == 1) {
            $list->whereIn('status', [
                ORDER_STATUS_FINISH_SYSTEM,
                ORDER_STATUS_FINISH_USER
            ])->where('is_rate', 0);
        } else {
            $list->whereNotIn('status', [
                ORDER_STATUS_USER_DELETE,
                ORDER_STATUS_SELLER_DELETE,
                ORDER_STATUS_ADMIN_DELETE
            ]);
        }
        $list->orderBy('id', 'desc');
        $list = $list->skip(($page - 1) * 20)->take(20)->with('cartSellers', 'seller')->get()->toArray();
        $data['orderList'] = [];
        foreach ($list as $key => $vo) {
            $data['orderList'][$key] = $vo;
            $data['orderList'][$key]['shopName'] = $vo['seller']['name'];
            $data['orderList'][$key]['sellerTel'] = $vo['seller']['serviceTel'];
            $data['orderList'][$key]['countGoods'] = Goods::where('type', 1)->where('seller_id', $vo['seller']['id'])->where('status', 1)->count('id');
            $data['orderList'][$key]['countService'] = Goods::where('type', 2)->where('seller_id', $vo['seller']['id'])->where('status', 1)->count('id');
            $data['orderList'][$key]['goodsImages'] = [];
            foreach ($vo['cartSellers'] as $k => $v) {
                $data['orderList'][$key]['goodsImages'][$k] = $v['goodsImages'];
            }
            unset($data['orderList'][$key]['cartSellers']);
            unset($data['orderList'][$key]['seller']);
        }
        return $data;
    }

    public static function endOrder() {
        $promotionSnId = Order::where('auto_cancel_time', '<', UTC_TIME)
                ->where('status', ORDER_STATUS_BEGIN_USER)
                ->where('pay_status', ORDER_PAY_STATUS_NO)
                ->lists('promotion_sn_id');

        //超时自动取消,优惠券自动返还
        if (count($promotionSnId) > 0) {
            PromotionSn::whereIn('id', $promotionSnId)->update(['use_time' => 0]);
        }

        // 测试时使用，避免订单过期
        Order::where('auto_cancel_time', '<', UTC_TIME)
                ->where('status', ORDER_STATUS_BEGIN_USER)//未处理
                ->where('pay_status', ORDER_PAY_STATUS_NO)//未支付
                ->where('pay_money', 0)//余额未支付
                ->update(['status' => ORDER_STATUS_CANCEL_AUTO, 'cancel_remark' => '支付超时自动取消订单']);
        //设为支付超时
        //查询已经部分支付的订单
        $balanceOrderLists = Order::where('auto_cancel_time', '<', UTC_TIME)
                ->where('status', ORDER_STATUS_BEGIN_USER)//未处理
                ->where('pay_status', ORDER_PAY_STATUS_NO)//未支付
                ->where('pay_money', '>', 0)//余额部分支付
                ->get();
        // var_dump($balanceOrderLists->toArray());exit;
        foreach ($balanceOrderLists as $order) {
            //返还支付金额给会员余额
            $user = User::find($order->user_id);
            $user->balance = $user->balance + abs($order->pay_money);
            $user->save();
            //修改订单状态
            $order->status = ORDER_STATUS_CANCEL_AUTO;
            $order->cancel_remark = '支付超时自动取消订单';
            $order->save();
            //创建退款日志
            $userPayLog = new UserPayLog;
            $userPayLog->payment_type = 'balancePay';
            $userPayLog->pay_type = 3; //退款
            $userPayLog->user_id = $order->user_id;
            $userPayLog->order_id = $order->id;
            $userPayLog->activity_id = $order->activity_id > 0 ? $order->activity_id : 0;
            $userPayLog->seller_id = $order->seller_id;
            $userPayLog->money = $order->pay_money;
            $userPayLog->balance = $user->balance;
            $userPayLog->content = '支付超时，系统退款';
            $userPayLog->create_time = UTC_TIME;
            $userPayLog->create_day = UTC_DAY;
            $userPayLog->status = 1;
            $userPayLog->sn = Helper::getSn();
            $userPayLog->save();
        }
//
//            //更新服务人员扩展表
//            $sql = "UPDATE " . env('DB_PREFIX') . "seller_staff_extend AS E
//            INNER JOIN
//            (
//                    SELECT seller_staff_id,freight
//                        FROM " . env('DB_PREFIX') . "order
//                        WHERE auto_finish_time < " . UTC_TIME . "
//                         AND pay_status = " . ORDER_PAY_STATUS_YES . "
//                         AND status = " . ORDER_STATUS_FINISH_STAFF . "
//            ) AS T ON E.staff_id = T.seller_staff_id
//            SET E.withdraw_money = IFNULL(E.withdraw_money, 0) + IFNULL(T.freight, 0),
//            E.total_money = IFNULL(E.total_money, 0) + IFNULL(T.freight, 0),
//            E.order_count = IFNULL(E.order_count, 0) + 1";
//
//            DB::unprepared($sql);
//
//
//            //更新商家扩展表
//            $sql = "UPDATE " . env('DB_PREFIX') . "seller_extend AS E
//            INNER JOIN
//            (
//                    SELECT seller_id,pay_fee,seller_fee
//                        FROM " . env('DB_PREFIX') . "order
//                        WHERE auto_finish_time < " . UTC_TIME . "
//                         AND pay_status = " . ORDER_PAY_STATUS_YES . "
//                         AND status = " . ORDER_STATUS_FINISH_STAFF . "
//                         AND pay_type <> 'cashOnDelivery'
//            ) AS T ON E.seller_id = T.seller_id
//            SET E.total_money = IFNULL(E.total_money, 0) + IFNULL(T.seller_fee, 0),
//            E.money = IFNULL(E.money, 0) + IFNULL(T.seller_fee, 0),
//            E.order_count = IFNULL(E.order_count, 0) + 1,
//            E.wait_confirm_money = (
//            CASE
//                WHEN (IFNULL(E.wait_confirm_money, 0) - IFNULL(T.seller_fee, 0)) > 0 THEN
//                    (IFNULL(E.wait_confirm_money, 0) - IFNULL(T.seller_fee, 0))
//                ELSE
//                    0
//            END
//                    )";
//
//            DB::unprepared($sql);
        //更新订单状态
        Order::where('auto_finish_time', '<', UTC_TIME)
                ->where('pay_status', ORDER_PAY_STATUS_YES)//已支付
                ->where('status', ORDER_STATUS_FINISH_STAFF)// 已接受
                ->update(['status' => ORDER_STATUS_FINISH_SYSTEM, 'buyer_finish_time' => UTC_TIME]); //设为系统到期确认
        //自动确认之后写入商家资金日志
//            $orders = Order::where('status', ORDER_STATUS_FINISH_SYSTEM)
//                            ->where('pay_type', '<>', 'cashOnDelivery')
//                            ->whereNotIn('id', function ($query) {
//                                $query->select('related_id')
//                                ->from('seller_money_log')
//                                ->where('type', SellerMoneyLog::TYPE_ORDER_CONFIRM);
//                            })->select(DB::raw('*,(select money from ' . env('DB_PREFIX') . 'seller_extend where ' . env('DB_PREFIX') . 'order.seller_id = ' . env('DB_PREFIX') . 'seller_extend.seller_id) as balance'))
//                            ->get()->toArray();
//
//            $data = [];
//            $orderIds = [];
//            foreach ($orders as $k => $v) {
//                $data[$k] = [
//                    'sn' => Helper::getSn(),
//                    'seller_id' => $v['sellerId'],
//                    'type' => SellerMoneyLog::TYPE_ORDER_CONFIRM,
//                    'related_id' => $v['id'],
//                    'money' => $v['sellerFee'],
//                    'balance' => $v['balance'],
//                    'content' => '订单' . $v['sn'] . '结算入余额',
//                    'create_time' => UTC_TIME,
//                    'create_day' => UTC_DAY,
//                    'status' => 1
//                ];
//                $orderIds[$k] = $v['id'];
//            }
//
//            if (!empty($data)) {
//                SellerMoneyLog::insert($data);
//                SellerMoneyLog::where('type', 'order_pay')->whereIn('related_id', $orderIds)->update(['status' => 3]);
//            }
    }

    /**
     * 取消订单
     * @param  [type] $userId  [description]
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public static function cancelOrder($userId, $orderId, $cancelRemark) {
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.user_cancel_order')
        );
        $order = Order::with('staff')->where('id', $orderId)->where('user_id', $userId)->first();

        /* if($cancelRemark == ""){
          $result['code'] = 60116;
          return $result;
          } */
        //没有订单
        if (!$order) {
            $result['code'] = 60014;
            return $result;
        }

        //不能取消
        if (!in_array($order->status, [
                    ORDER_STATUS_BEGIN_USER,
                    ORDER_STATUS_PAY_SUCCESS,
                    ORDER_STATUS_PAY_DELIVERY
                ])) {
            $result['code'] = 60015;
            return $result;
        }
        DB::beginTransaction();
        try {
            if ($order->status == ORDER_STATUS_BEGIN_USER && $order->promotion_sn_id > 0) {
                PromotionSn::where('id', $order->promotion_sn_id)->update(['use_time' => 0]);
            }

            //更改订单状态
            $order->status = ORDER_STATUS_CANCEL_USER;
            $order->cancel_time = UTC_TIME;
            $order->cancel_remark = $cancelRemark;
            $order->save();

//            if ($order->promotion_sn_id > 0) {
//                //PromotionSn::where('id', $order->promotion_sn_id)->update(['use_time' => 0]);
//            }

            if ($order->pay_status == ORDER_PAY_STATUS_YES && $order->isCashOnDelivery()) {
                //如果是货到付款则退还商家支付的抽成金额
                $sellerMoneyLog = SellerMoneyLog::where('related_id', $order->id)
                        ->where('type', SellerMoneyLog::TYPE_DELIVERY_MONEY)
                        ->first();
                if ($sellerMoneyLog) {
                    //增加商家金额
                    SellerExtend::where('seller_id', $order->seller_id)
                            ->increment('money', abs($sellerMoneyLog->money));
                    //写入增加金额日志
                    SellerMoneyLogService::createLog(
                            $order->seller_id, SellerMoneyLog::TYPE_DELIVERY_MONEY, $orderId, $order->drawn_fee, '现金支付订单' . $order->sn . '取消，佣金返还', 1
                    );
                }
            }

            //如果未支付订单 包含余额支付金额 则退款
            if (($order->pay_status == ORDER_PAY_STATUS_NO && $order->pay_money > 0.0001) || ($order->pay_money >= 0.0001 && $order->pay_status == ORDER_PAY_STATUS_YES && $order->isCashOnDelivery === false)) {
                //返还支付金额给会员余额
                $user = User::find($order->user_id);
                $user->balance = $user->balance + abs($order->pay_money);
                $user->save();
                //创建退款日志
                $userPayLog = new UserPayLog;
                $userPayLog->payment_type = 'balancePay';
                $userPayLog->pay_type = 3; //退款
                $userPayLog->user_id = $order->user_id;
                $userPayLog->order_id = $order->id;
                $userPayLog->activity_id = $order->activity_id > 0 ? $order->activity_id : 0;
                $userPayLog->seller_id = $order->seller_id;
                $userPayLog->money = $order->pay_money;
                $userPayLog->balance = $user->balance;
                $userPayLog->content = '会员取消订单退款';
                $userPayLog->create_time = UTC_TIME;
                $userPayLog->create_day = UTC_DAY;
                $userPayLog->status = 1;
                $userPayLog->sn = Helper::getSn();
                $userPayLog->save();
            }
            //写入退款日志
            if (
                    $order->pay_fee >= 0.0001 &&
                    $order->pay_status == ORDER_PAY_STATUS_YES &&
                    $order->isCashOnDelivery === false
            ) {
                $pay_type = $order->getPayType();

                $userPayLogs = UserPayLog::where('order_id', $order->id)
                        ->where('pay_type', 1)
                        ->where('status', 1)
                        ->get()
                        ->toArray();

                if (count($userPayLogs) > 0) {
                    $userRefundLog = [];
                    foreach ($userPayLogs as $k => $v) {
                        if ($v['paymentType'] == 'balancePay') {
                            $userRefundLog[] = [
                                "sn" => $order->sn,
                                "user_id" => $order->user_id,
                                "order_id" => $order->id,
                                "trade_no" => $v['tradeNo'],
                                "seller_id" => $order->seller_id,
                                "payment_type" => $v['paymentType'],
                                "money" => $v['money'],
                                "content" => "用户取消",
                                "create_time" => UTC_TIME,
                                "create_day" => UTC_DAY,
                                "status" => 1
                            ];
                        } else {
                            $userRefundLog[] = [
                                "sn" => $order->sn,
                                "user_id" => $order->user_id,
                                "order_id" => $order->id,
                                "trade_no" => $v['tradeNo'],
                                "seller_id" => $order->seller_id,
                                "payment_type" => $v['paymentType'],
                                "money" => $v['money'],
                                "content" => "用户取消",
                                "create_time" => UTC_TIME,
                                "create_day" => UTC_DAY,
                                "status" => 0
                            ];
                        }
                    }
                    DB::table('refund')->insert($userRefundLog);

                    SellerMoneyLogService::createLog(
                            $order->seller_id, SellerMoneyLog::TYPE_ORDER_REFUND, $orderId, $order->pay_fee, '订单取消，退款：' . $order->sn
                    );
                }
                \YiZan\Services\SellerService::decrementExtend($order->seller_id, 'wait_confirm_money', $order->seller_fee);
            }

            self::cancelOrderStock($orderId);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        try {
            $result['data'] = self::getOrderById($userId, $order->id);
            $order = $result['data'];
            //通知服务人员
            if (count($order['staff']) > 0) {
                PushMessageService::notice($order['staff']['userId'], $order['staff']['mobile'], 'order.cancel', $order, ['sms', 'app'], 'staff', 3, $order['id']);
            }
            PushMessageService::notice($order['user']['id'], $order['user']['mobile'], 'order.usercancel', $order, ['sms', 'app'], 'buyer', 3, $order['id']);
        } catch (Exception $e) {
            
        }

        return $result;
    }

    /**
     * 取消订单还原库存
     * @param int $orderId 订单编号
     */
    public static function cancelOrderStock($orderId) {

        $sql = "UPDATE " . env('DB_PREFIX') . "order_goods AS A
		LEFT OUTER JOIN " . env('DB_PREFIX') . "goods AS G ON G.id = A.goods_id AND A.goods_norms_id = 0
		LEFT OUTER JOIN " . env('DB_PREFIX') . "goods_norms AS GN ON A.goods_norms_id = GN.id
	SET G.stock = G.stock + A.num,
			GN.stock = GN.stock + A.num
	WHERE A.order_id = " . $orderId;

        DB::unprepared($sql);
    }

    /**
     * 支付订单
     * @param  [type] $userId  [description]
     * @param  [type] $orderId [description]
     * @param  [type] $payment [description]
     * @return [type]          [description]
     */
    public static function payOrder($userId, $orderId, $payment, $extend = []) {

        if ($payment == "cashOnDelivery") {
            $result = self::delivery($userId, $orderId);
            return $result;
        }
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.user_pay_order')
        );

        $order = Order::where('id', $orderId)->where('user_id', $userId)->with('seller', 'staff')->first();

        if (!$order) {//没有订单
            $result['code'] = 60014;
            return $result;
        }

        if ($order->pay_status != 0) {//已经支付过了
            $result['code'] = 60018;
            return $result;
        }

        if ($order->status != ORDER_STATUS_BEGIN_USER) {//订单不能再进行支付
            $result['code'] = 60017;
            return $result;
        }
        $order->goods_name = "社区服务";
        $payLog = PaymentService::createPayLog($order, $payment, $extend, 0);
        //return $payLog;
        if (is_numeric($payLog)) {
            $result['code'] = abs($payLog);

            return $result;
        }
        if ($payLog) {
            $order = $order->toArray();
            try {
                PushMessageService::notice($order['seller']['userId'], $order['seller']['mobile'], 'order.create', $order, ['sms', 'app'], 'seller', '3', $orderId, "music1.caf");
                if ($order['staff'] && $order['seller']['userId'] != $order['staff']['userId']) {
                    PushMessageService::notice($order['staff']['userId'], $order['staff']['mobile'], 'order.create', $order, ['sms', 'app'], 'staff', '3', $orderId, "music1.caf");
                }
            } catch (Exception $e) {
                
            }
        }
        $result['data'] = $payLog;

        return $result;
    }

    /**
     * 余额支付订单
     * @param  [type] $order  [description]
     * @param  [type] $userPayLog [description]
     * @return [type]          [description]
     */
    public static function balanceOrder($order, $userPayLog) {
        DB::connection()->enableQueryLog();
        $order = Order::with('staff', 'goods', 'user')->find($order->id);
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => ''
        );

        if (!$order) {//没有订单
            $result['code'] = 60014;
            return $result;
        }

        if ($order->pay_status != 0) {//已经支付过了
            $result['code'] = 60018;
            return $result;
        }

        if ($order->status != ORDER_STATUS_BEGIN_USER) {//订单不能再进行支付
            $result['code'] = 60017;
            return $result;
        }
        //更新用户余额 
        $userbalance = $order->user->balance - $order->pay_fee;
        if ($userbalance < 0) {
            $result['code'] = 60318;
            return $result;
        }
        // var_dump($userPayLog);
        // exit;
        DB::beginTransaction();
        try {
            $status = UserPayLog::where('id', $userPayLog['id'])->update([
                'pay_time' => UTC_TIME,
                'pay_day' => UTC_DAY,
                'balance' => $userbalance,
                'status' => 1
            ]);

            //修改状态
            $status = Order::where('id', $order->id)->update([
                'pay_time' => UTC_TIME,
                'pay_status' => ORDER_PAY_STATUS_YES,
                'status' => ORDER_STATUS_PAY_SUCCESS,
                'pay_type' => $userPayLog['paymentType'],
                'pay_money' => $order->pay_fee,
            ]);

            if ($status) {
                //更新服务人员待到帐金额
                SellerService::incrementExtend($order->seller_id, 'wait_confirm_money', $order->pay_fee);

                //写入日志
                SellerMoneyLogService::createLog(
                        $order->seller_id, 'order_pay', $order->id, $order->pay_fee, '订单支付:' . $order->sn
                );

                User::where('id', $order->user_id)->update(['balance' => $userbalance]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $status = false;
        }
        file_put_contents("/mnt/wwwroot/o2o/storage/logs/updateBalance.log", Time::toDate(UTC_TIME, 'Y-m-d') . "=>" . print_r(DB::getQueryLog(), true), FILE_APPEND);
        if ($status) {
            $order = self::getOrderById($order->user_id, $order->id);
            try {
                PushMessageService::notice($order['seller']['userId'], $order['seller']['mobile'], 'order.pay', $order, ['sms', 'app'], 'seller', '3', $order['id'], "pay.caf");
                if ($order['staff'] && $order['seller']['userId'] != $order['staff']['userId']) {
                    PushMessageService::notice($order['staff']['userId'], $order['staff']['mobile'], 'order.pay', $order, ['sms', 'app'], 'staff', '3', $order['id'], "pay.caf");
                }
            } catch (Exception $e) {
                
            }
        } else {
            $result['code'] = 60019;
        }
    }

    /**
     * 删除订单
     * @param  [type] $userId  [description]
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public static function deleteOrder($userId, $orderId) {
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.user_delete_order')
        );

        $order = Order::where('id', $orderId)->where('user_id', $userId)->first();
        if (!$order) {//没有订单
            $result['code'] = 60014;
            return $result;
        }
        //当订单状态不为以下状态时
        if ($order->isCanDelete == false) {
            $result['code'] = 60020;

            return $result;
        }
        //会员已删除订单
        $order->status = ORDER_STATUS_USER_DELETE;
        $order->save();
        return $result;
    }

    /**
     * 货到付款
     * @param  [type] $userId  [description]
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public static function delivery($userId, $orderId) {

        $order = Order::where('id', $orderId)->where('user_id', $userId)->first();
        if (!$order) {//没有订单
            $result['code'] = 60014;
            return $result;
        }

        if ($order->pay_status != 0) {//已经支付过了
            $result['code'] = 60018;
            return $result;
        }

        if ($order->status != ORDER_STATUS_BEGIN_SERVICE) {//订单不能再进行支付
            $result['code'] = 60017;
            return $result;
        }
        $order->pay_time = UTC_TIME;                        // 支付时间
        $order->pay_type = 2;                               // 货到付款
        $order->pay_status = ORDER_PAY_STATUS_YES;            // 已支付 
        $order->status = ORDER_STATUS_PAY_DELIVERY;       // 货到付款 


        DB::beginTransaction();
        try {
            $order->save();
            DB::commit();
            $bln = true;
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
            $bln = false;
        }
        if ($bln) {
            $order = self::getOrderById($userId, $orderId);
            $result['data'] = $order;
        }
        return $result;
    }

    /**
     * 确认订单
     * @param  [type] $userId  [description]
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public static function confirmOrder($userId, $orderId) {
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.user_confirm_order')
        );

        $order = Order::with('staff', 'seller')->where('id', $orderId)->where('user_id', $userId)->first();
        if (!$order) {//没有订单
            $result['code'] = 60014;
            return $result;
        }

        //当订单状态为 会员确认,订单已经确认过
        if ($order->status == ORDER_STATUS_FINISH_SYSTEM || $order->status == ORDER_STATUS_FINISH_USER) {
            $result['code'] = 60022;
            return $result;
        }

        //当订单状态不为 服务完成,订单不能确认
        if (!in_array($order->status, [
                    ORDER_STATUS_FINISH_STAFF,
                    ORDER_STATUS_START_SERVICE
                ])) {
            $result['code'] = 60021;
            return $result;
        }

        $order->buyer_finish_time = UTC_TIME;
        $order->status = ORDER_STATUS_FINISH_USER; //会员确认


        DB::beginTransaction();
        try {
            $order->save();
            //更新服务人员余额
            SellerStaffExtend::where('staff_id', $order->seller_staff_id)->increment('total_money', $order->staff_fee);
            SellerStaffExtend::where('staff_id', $order->seller_staff_id)->increment('withdraw_money', $order->staff_fee);
            SellerStaffExtend::where('staff_id', $order->seller_staff_id)->increment('order_count', 1);

            //更新商家扩展
            if ($order->isCashOnDelivery === false) {
                SellerService::incrementExtend($order->seller_id, 'total_money', $order->seller_fee);
                SellerService::incrementExtend($order->seller_id, 'money', $order->seller_fee);
                SellerService::decrementExtend($order->seller_id, 'wait_confirm_money', $order->seller_fee);
                if ($order->seller_fee > 0.001) {
                    SellerMoneyLogService::createLog(
                            $order->seller_id, SellerMoneyLog::TYPE_ORDER_CONFIRM, $orderId, $order->seller_fee, '订单' . $order->sn . '结算入余额', 1
                    );
                    SellerMoneyLog::where('type', 'order_pay')
                            ->where('related_id', $orderId)
                            ->where('seller_id', $order->seller_id)
                            ->update(['status' => 3]);
                }
            }

            DB::commit();
            $bln = true;
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
            $bln = false;
        }

        if ($bln) {
            $order = $order->toArray();
            try {
                //通知服务人员
                PushMessageService::notice($order['seller']['userId'], $order['seller']['mobile'], 'order.confirm', $order, ['sms', 'app'], 'seller', '3', $orderId);
                if ($order['staff'] && $order['seller']['userId'] != $order['staff']['userId']) {
                    PushMessageService::notice($order['staff']['userId'], $order['staff']['mobile'], 'order.confirm', $order, ['sms', 'app'], 'staff', '3', $orderId);
                }
            } catch (Exception $e) {
                
            }

            $order = self::getOrderById($userId, $orderId);
            $result['data'] = $order;
        }
        return $result;
    }

    /**
     * [refund 退款]
     * @param  [int] $userId            [会员编号]
     * @param  [int] $orderId           [订单编号]
     * @param  [string] $refundImages   [退款举证图片]
     * @param  [string] $refundContent  [退款理由]
     * @return [array]                  [返回代码]
     */
    public static function refund($userId, $orderId, $refundImages, $refundContent) {
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.order_refund_create')
        );

        $order = OrderService::getOrderById($userId, $orderId);

        if (!$order->isCanRefund) {
            $result['code'] = 60306;
            return $result;
        }

        if (!$order) {
            $result['code'] = 60301;
            return $result;
        }

        if ($refundContent == '') {
            $result['code'] = 60305;
            return $result;
        }

        if (count($refundImages) > 0) {
            foreach ($refundImages as $key => $image) {
                $refundImages[$key] = self::moveOrderImage($orderId, $image);
                if (!$refundImages[$key]) {
                    $result['code'] = 60308;
                    return $result;
                }
            }
            $refundImages = implode(',', $refundImages);
        } else {
            $refundImages = '';
        }

        $order_refund_info = [
            'status' => ORDER_STATUS_REFUND_AUDITING,
            'refund_images' => $refundImages,
            'refund_content' => $refundContent,
            'refund_time' => UTC_TIME,
        ];

        try {
            Order::where('user_id', $userId)
                    ->where('id', $orderId)
                    ->update($order_refund_info);
        } catch (Exception $e) {
            $result['code'] = 60307;
        }
        return $result;
    }

    /**
     * 创建外卖以外的所有订单
     * @param  int    $userId       会员编号
     * @param  int    id            服务Id 
     * @param  string $addressId      地址
     * @return array                订单信息
     */
    public static function createOrderAll($userId, $id, $mobileId, $addressId, $remark) {
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => ''
        );

        $rules = array(
            'id' => ['required'],
            'mobileId' => ['required'],
            'addressId' => ['required'],
        );

        $messages = array(
            'id.required' => '60317',
            'mobileId.required' => '60008',
            'addressId.required' => '60010',
        );

        $validator = Validator::make([
                    'id' => $id,
                    'mobileId' => $mobileId,
                    'addressId' => $addressId,
                        ], $rules, $messages);

        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        $address = UserAddress::where('user_id', $userId)->find($addressId);
        if ($address == null) {
            $result['code'] = 61001;
            return $result;
        }
        $mobile = UserMobile::where('user_id', $userId)->find($mobileId);
        if ($mobile == null) {
            $result['code'] = 60008;
            return $result;
        }
        $goods = Goods::find($id);
        $type = [
            '1' => '1',
            '2' => '3',
            '3' => '4',
            '4' => '5',
            '5' => '6',
        ];
        if (!$goods) {
            $result['code'] = 61001;
            return $result;
        }

        $order = new Order;
        $result['msg'] = Lang::get('api.success.user_create_order');
        $orderdata = [
            'user_id' => $userId,
            'goods_id' => $id,
            'restaurant_id' => $goods->restaurant_id,
            'order_type' => $type[$goods->type],
            'mobile' => $mobile->mobile,
            'address_id' => $addressId,
            'address' => $address->address,
            'status' => ORDER_STATUS_BEGIN_SERVICE,
            'pay_type' => 2,
            'seller_id' => $goods->seller_id,
            'total_fee' => $goods->price,
            'pay_fee' => $goods->price,
            'buy_remark' => $remark,
        ];
        //订单创建时间	     
        $orderdata['create_time'] = UTC_TIME;
        $orderdata['create_day'] = UTC_DAY;
        DB::beginTransaction();
        do {
            try {
                $orderdata['sn'] = Helper::getSn();
                $orderdata['reservation_code'] = Helper::getCode(0, 6);
                $insert = Order::insertGetId($orderdata);
                $bln = true;
            } catch (Exception $e) {
                $bln = false;
            }
        } while (!$bln);

        try {
            GoodsExtend::where('goods_id', $id)->increment('sales_volume');
            $bln = true;
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $bln = false;
            $result['code'] = 60013;
            return $result;
        }
        if ($bln) {
            $order = self::getOrderById($userId, $insert);
            $result['data'] = $order;
            return $result;
        }
    }

    /**
     * 创建外卖订单
     * @param  int    $userId       会员编号
     * @param  int    $goods        菜品列表
     * @param  string $addressId      地址
     * @param  string $remark       买家备注
     * @param  int    $type         订单类型
     * @return array                订单信息
     */
    public static function createOrder($userId, $goods, $mobileId, $addressId, $remark, $type) {

        $run_subscribe_lunch_begintime = SystemConfigService::getConfigByCode('run_subscribe_lunch_begintime');        //预约午餐开始时间
        $run_subscribe_lunch_endtime = SystemConfigService::getConfigByCode('run_subscribe_lunch_endtime');            //预约午餐结束时间

        $run_subscribe_lunch_begintime = (int) str_replace(":", "", $run_subscribe_lunch_begintime);
        $run_subscribe_lunch_endtime = (int) str_replace(":", "", $run_subscribe_lunch_endtime);
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => ''
        );
        $rules = array(
            'goods' => ['required'],
            'mobileId' => ['required'],
            'addressId' => ['required'],
            'type' => ['required'],
        );

        $messages = array(
            'goods.required' => '60317',
            'mobileId.required' => '60008',
            'addressId.required' => '60010',
            'type.required' => '60316',
        );

        $validator = Validator::make([
                    'goods' => $goods,
                    'mobileId' => $mobileId,
                    'addressId' => $addressId,
                    'type' => $type,
                        ], $rules, $messages);

        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        if ($type > 2) {
            $result['code'] = 60407;
            return $result;
        }
        if ($type == 2) {
            if ($run_subscribe_lunch_begintime > (int) Time::toDate(UTC_TIME, 'Hi')) {
                $result['code'] = 60405;
                return $result;
            }
            if ($run_subscribe_lunch_endtime < (int) Time::toDate(UTC_TIME, 'Hi')) {
                $result['code'] = 60406;
                return $result;
            }
        }
        $address = UserAddress::where('user_id', $userId)->find($addressId);
        if (!$address) {
            $result['code'] = 61001;
            return $result;
        }
        $mobile = UserMobile::where('user_id', $userId)->find($mobileId);
        if (!$mobile) {
            $result['code'] = 60008;
            return $result;
        }
        $user = User::find($userId);
        $result = self::moneyComputeCheckOrder($goods, $type); //金额计算及菜品核审
        if ($result['code'] != 0) {
            return $result;
        }
        $order = new Order;
        $order_goods = $result['data'];
        $result['data'] = null;
        $result['msg'] = Lang::get('api.success.user_create_order');
        $orderdata = [
            'user_id' => $userId,
            'restaurant_id' => $order_goods['order']['restaurant_id'],
            'order_type' => $type,
            'mobile' => $mobile->mobile,
            'total_fee' => $order_goods['order']['total_fee'],
            'address_id' => $addressId,
            'address' => $address->address,
            'seller_id' => $order_goods['order']['seller_id'],
            'buy_remark' => $remark,
            'pay_fee' => $order_goods['order']['total_fee'],
        ];

        $run_over_part_num = SystemConfigService::getConfigByCode('run_over_part_num');        //超出份数
        $run_over_add_money = SystemConfigService::getConfigByCode('run_over_add_money');       //超出份数加收
        $run_forthwith_send_fee = SystemConfigService::getConfigByCode('run_forthwith_send_fee');   //即时默认
        $run_subscribe_send_fee = SystemConfigService::getConfigByCode('run_subscribe_send_fee');   //预约默认

        $orderdata['service_fee'] = "";
        //即时送餐
        if ($type == 1) {
            if ($order_goods['order']['total_count'] > $run_over_part_num) {
                // 默认配送费+（总份数-超出份数）*超出费用
                $orderdata['total_fee'] = $orderdata['pay_fee'] += $orderdata['service_fee'] = $run_forthwith_send_fee + ($order_goods['order']['total_count'] - $run_over_part_num) * $run_over_add_money;          //配送费
            } else {
                $orderdata['total_fee'] = $orderdata['pay_fee'] += $orderdata['service_fee'] = $run_forthwith_send_fee;      //配送费
            }
        } else {
            if ($order_goods['order']['total_count'] > $run_over_part_num) {
                $orderdata['total_fee'] = $orderdata['pay_fee'] += $orderdata['service_fee'] = $run_subscribe_send_fee + ($order_goods['order']['total_count'] - $run_over_part_num) * $run_over_add_money;          //配送费
            } else {
                $orderdata['total_fee'] = $orderdata['pay_fee'] += $orderdata['service_fee'] = $run_subscribe_send_fee;      //配送费
            }
        }

        $orderdata['status'] = ORDER_STATUS_AFFIRM_SERVICE; //未处理
        //当支付金额为0时
        if ($orderdata['pay_fee'] <= 0) {
            $orderdata['pay_status'] = ORDER_PAY_STATUS_YES; //已支付
            $orderdata['status'] = ORDER_STATUS_PAY_SUCCESS;
            $orderdata['pay_time'] = UTC_TIME;
        } else {
            $orderdata['pay_status'] = ORDER_PAY_STATUS_NO; //未支付
            $orderdata['status'] = ORDER_STATUS_BEGIN_SERVICE;
        }
        $orderdata['pay_end_time'] = UTC_TIME + (int) SystemConfigService::getConfigByCode('system_order_pass') * 60;
        //订单创建时间
        $orderdata['create_time'] = UTC_TIME;
        $orderdata['create_day'] = UTC_DAY;
        DB::beginTransaction();
        $orderdata['sn'] = Helper::getSn();
        do {
            try {
                $orderdata['reservation_code'] = Helper::getCode(0, 6);
                $orderdata['order_num'] = Order::where('order_type', $type)->where('create_day', $orderdata['create_day'])->count() + 1;
                $insert = Order::insertGetId($orderdata);
                $bln = true;
            } catch (Exception $e) {
                die(print_r($e, true));
                $bln = false;
            }
        } while (!$bln);
        try {
            //更新服务销售数量
            foreach ($order_goods['goods'] as $key => $orders) {
                $order_goods['goods'][$key]['order_id'] = $insert;
                GoodsExtend::where('goods_id', $orders['goods_id'])->increment('sales_volume', intval($orders['num']));
                Restaurant::where('id', $orderdata['restaurant_id'])->increment('sale_count', intval($orders['num']));
            }
            OrderGoods::insert($order_goods['goods']);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $bln = false;
            $result['code'] = 60013;
        }
        if ($bln) {
            $order = $order->with('cartSellers')->find($insert)->toArray();
            $result['data'] = $order;
        }

        return $result;
    }

    /**
     * 外卖订单金额计算
     * @param  int    $goods     菜品
     * @return array data
     */
    public static function moneyComputeCheckOrder($goods, $type) {
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => ''
        );
        if (!is_array($goods) || count($goods) < 1) {
            $result['code'] = 60400;
            return $result;
        }
        $new_goods = [];
        foreach ($goods as $key => $v) {
            $new_goods[$v['id']] = $v;
            $id[$key] = $v['id'];
            if ($type != $v['type']) {
                $result['code'] = 60407;
                return $result;
            }
        }
        $order_goods = Goods::whereIn('id', array_keys($new_goods))->get();
        $count = $order_goods->count();
        if ($count != count($id)) {
            $result['code'] = 60401;
            return $result;
        }
        $checkRes = Goods::whereIn('id', array_keys($new_goods))->groupBy('restaurant_id')->get()->toArray();
        if (count($checkRes) > 1) {
            $result['code'] = 60403;
            return $result;
        }
//         $join_service = Goods::whereIn('id',array_keys($new_goods))->groupBy('join_service')->get()->toArray();
//         if (count($join_service) > 1){
//             $result['code'] = 60403;
//             return $result;
//         }
        $pay_fee = "";
        $restaurant_id = "";
        $goods_data = [];
        $total_count = "";
        foreach ($order_goods as $key => $v) {
            $goods_data[$key] = [
                'goods_id' => $v->id,
                'price' => $v->price,
                'num' => $new_goods[$v['id']]['count']
            ];
            $pay_fee += $v->price * $new_goods[$v['id']]['count'];
            $restaurant_id = $v->restaurant_id;
            $total_count += $new_goods[$v['id']]['count'];
        }
        $result['data']['goods'] = $goods_data;
        $result['data']['order']['restaurant_id'] = $restaurant_id;
        $result['data']['order']['total_fee'] = $pay_fee;
        $result['data']['order']['seller_id'] = $order_goods[0]['seller_id'];
        $result['data']['order']['total_count'] = $total_count;
// 		//优惠金额
// 		$promotion_money = 0;
// 		if (!empty($promotionSn)) 
//         {
// 			$promotion_money = PromotionService::getPromotionMoneyCar($promotionSn, $userId, $goods->total_money);
// 			if ($promotion_money < 0) 
//             {
// 				$result['code'] = abs($promotion_money);
// 				return $result;
// 			}
// 		}
// 		$result['data']['totalMoeny'] = $goods->total_money;
// 		$result['data']['promotionMoney'] = $promotion_money;
// 		$result['data']['payMoney'] = $promotion_money > $goods->total_money ? 0 : $goods->total_money - $promotion_money;

        return $result;
    }

    /**
     * 餐厅订单列表
     * @param  [type] $userId [description]
     * @param  [type] $status [description]
     * @param  [type] $page   [description]
     * @return [type]         [description]
     */
    public static function getRestaurantList($restaurantId, $status, $beginTime, $endTime, $orderType, $page, $pageSize) {

        $list = Order::where('restaurant_id', $restaurantId);
        $list->with('user');
        $list->orderBy('id', 'desc');
        if ($beginTime != '') {
            $list->where('create_time', '>=', Time::toTime(strval($beginTime)));
        }

        if ($endTime != '') {
            $list->where('create_time', '<=', Time::toTime(strval($endTime)) + 24 * 60 * 60 - 1);
        }
        if ($orderType > 0) {
            $list->where('order_type', $orderType);
        }
        switch ($status) {
            case 1://待接受
                $list->whereIn('status', [
                    ORDER_STATUS_BEGIN_SERVICE,
                    ORDER_STATUS_PAY_SUCCESS,
                    ORDER_STATUS_PAY_DELIVERY
                ]);
                break;

            case 2://待完成
                $list->whereIn('status', [
                    ORDER_STATUS_AFFIRM_SERVICE,
                    ORDER_STATUS_AFFIRM_ASSIGN_SERVICE,
                    ORDER_STATUS_ASSIGN_SERVICE,
                    ORDER_STATUS_REFUND_REFUSE,
                    ORDER_STATUS_REFUND_HANDLE,
                    ORDER_STATUS_REFUND_FAIL,
                    ORDER_STATUS_REFUND_AUDITING,
                    ORDER_STATUS_SYSTEM_CONFIRM
                ]);
                break;
            case 3:
                $list->whereIn('status', [
                    ORDER_STATUS_SYSTEM_CONFIRM,
                    RESTAURANT_TIMEOUT_TO_REFUSE_SERVICE,
                    SYSTEM_TIMEOUT_TO_REFUSE_SERVICE,
                    ORDER_USER_CANCEL_SERVICE,
                    ORDER_STATUS_REFUND_SUCCESS,
                    ORDER_STATUS_ADMIN_DELETE,
                    ORDER_STATUS_SELLER_DELETE,
                    ORDER_STATUS_USER_DELETE,
                    ORDER_OVER_PAY_SERVICE
                ]);
                break;
            default:
                break;
        }
        $totalCount = $list->count();
        $list = $list->skip(($page - 1) * 20)->take(20)->get()->toArray();
        return ["list" => $list, "totalCount" => $totalCount];
    }

    /**
     * 订单金额分类统计， add by skywod 2016-05-05
     * @param $sellerId 商家ID
     * @param $beginTime 订单创建开始时间，unix时间戳
     * @param $endTime  订单创建结束时间，unix时间戳
     */
    public static function cateMoney($sellerId, $beginTime, $endTime) {
        self::endOrder();
        $pre = 'yz_';
        $sql = "select d.name,c.cate_id,sum(b.num*b.price) money from {$pre}order a, {$pre}order_goods b, {$pre}goods c, {$pre}goods_cate d where a.seller_id={$sellerId} and a.id = b.order_id and c.id = b.goods_id and c.cate_id = d.id and a.create_time>={$beginTime} and a.create_time<={$endTime} group by d.id";
        //return $sql;
        $results = DB::select($sql);
        $sql = "select id, name from {$pre}goods_cate where seller_id={$sellerId}";
        $cates = DB::select($sql);
        foreach ($cates as $key1 => $cate) {
            $exists = false;
            if (count($results) > 0) {
                foreach ($results as $key2 => $value) {
                    if ($value->cate_id == $cate->id) {
                        $exists = true;
                        break;
                    }
                }
            }
            if (!$exists) {
                $newItem = new \stdClass();
                $newItem->name = $cate->name;
                $newItem->cate_id = $cate->id;
                $newItem->money = 0;
                $results[] = $newItem;
            }
        }
        return $results;
    }

}
