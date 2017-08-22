<?php

namespace YiZan\Http\Controllers\Callback\Payment;

use YiZan\Http\Controllers\Callback\BaseController;
use YiZan\Models\UserPayLog;
use YiZan\Models\SellerPayLog;
use YiZan\Models\Order;
use YiZan\Models\PromotionSn;
use YiZan\Models\Payment;
use YiZan\Models\User;
use YiZan\Models\UserCard;
use YiZan\Models\SellerExtend;
use YiZan\Models\SellerMoneyLog;
use YiZan\Services\SellerService;
use YiZan\Services\SellerMoneyLogService;
use YiZan\Services\PushMessageService;
use YiZan\Services\ActivityService;
use YiZan\Services\OrderService;
use Illuminate\Database\Query\Expression;
use YiZan\Services\System\PromotionService;
use DB,
    Exception,
    YiZan\Models\Refund,
    YiZan\Models\UserRefundLog;

/**
 * 微信支付
 */
class AlipayController extends BaseController {

    /**
     * 会员订单手机端回调
     */
    public function appnotify() {


        if (empty($_REQUEST['notify_id']) ||
                empty($_REQUEST['seller_id']) ||
                empty($_REQUEST['out_trade_no']) ||
                empty($_REQUEST['sign'])) {
            die('参数不全');
        }

        if ($_REQUEST['trade_status'] != 'TRADE_SUCCESS' && $_REQUEST['trade_status'] != 'TRADE_FINISHED') {
            die('success');
        }

        $payment = Payment::where('code', 'alipay')->first()->toArray();
        $payment = $payment['config'];

        if ($_REQUEST['seller_id'] != $payment['partnerId']) {
            die('非法请求');
        }

        $check_status = trim(@file_get_contents('https://mapi.alipay.com/gateway.do?service=notify_verify&partner=' . $payment['partnerId'] . '&notify_id=' . $_REQUEST['notify_id']));
        if ($check_status !== 'true') {
            die('不是支付宝请求');
        }


        require_once base_path() . '/vendor/alipay/alipay_core.function.php';
        require_once base_path() . '/vendor/alipay/alipay_rsa.function.php';

        $check_status = rsaVerify(createLinkstring(argSort(paraFilter($_REQUEST))), $payment['partnerPubKey'], $_REQUEST['sign'], false);
        if (!$check_status) {
            die('签名错误');
        }
        $this->_notify($_REQUEST);
    }

    /**
     * 商家订单手机端回调
     */
    public function appnotifys() {


        if (empty($_REQUEST['notify_id']) ||
                empty($_REQUEST['seller_id']) ||
                empty($_REQUEST['out_trade_no']) ||
                empty($_REQUEST['sign'])) {
            die('参数不全');
        }

        if ($_REQUEST['trade_status'] != 'TRADE_SUCCESS' && $_REQUEST['trade_status'] != 'TRADE_FINISHED') {
            die('success');
        }

        $payment = Payment::where('code', 'alipay')->first()->toArray();
        $payment = $payment['config'];

        if ($_REQUEST['seller_id'] != $payment['partnerId']) {
            die('非法请求');
        }

        $check_status = trim(@file_get_contents('https://mapi.alipay.com/gateway.do?service=notify_verify&partner=' . $payment['partnerId'] . '&notify_id=' . $_REQUEST['notify_id']));
        if ($check_status !== 'true') {
            die('不是支付宝请求');
        }


        require_once base_path() . '/vendor/alipay/alipay_core.function.php';
        require_once base_path() . '/vendor/alipay/alipay_rsa.function.php';

        $check_status = rsaVerify(createLinkstring(argSort(paraFilter($_REQUEST))), $payment['partnerPubKey'], $_REQUEST['sign'], false);
        if (!$check_status) {
            die('签名错误');
        }
        $this->_notifys($_REQUEST);
    }

    /**
     * 会员订单网页回调
     */
    public function webpcnotify() {
        //file_put_contents('/mnt/www/paotui/storage/logs/pay.log',print_r($_REQUEST,true));

        $payment = Payment::where('code', 'alipayWeb')->first()->toArray();

        $payment = $payment['config'];

        if ($_REQUEST['seller_id'] != $payment['partnerId']) {
            die('fail');
        }

        require_once base_path() . '/vendor/alipay/alipay_pc_notify.class.php';

        $alipay_config['partner'] = $payment['partnerId'];
        $alipay_config['seller_email'] = $payment['sellerId'];
        $alipay_config['key'] = $payment['partnerKey'];
        $alipay_config['sign_type'] = 'MD5';
        $alipay_config['input_charset'] = 'utf-8';
        $alipay_config['cacert'] = base_path() . '/vendor/alipay/cacert.pem';
        $alipay_config['transport'] = 'https';

        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);

        $verify_result = $alipayNotify->verifyNotify();

        if ($verify_result == false) {
            die('fail');
        }

        $this->_notify($_REQUEST);
    }

    /**
     * 商家订单网页回调
     */
    public function webpcnotifys() {
        //file_put_contents('/mnt/www/paotui/storage/logs/pay.log',print_r($_REQUEST,true));

        $payment = Payment::where('code', 'alipayWeb')->first()->toArray();

        $payment = $payment['config'];

        if ($_REQUEST['seller_id'] != $payment['partnerId']) {
            die('fail');
        }

        require_once base_path() . '/vendor/alipay/alipay_pc_notify.class.php';

        $alipay_config['partner'] = $payment['partnerId'];
        $alipay_config['seller_email'] = $payment['sellerId'];
        $alipay_config['key'] = $payment['partnerKey'];
        $alipay_config['sign_type'] = 'MD5';
        $alipay_config['input_charset'] = 'utf-8';
        $alipay_config['cacert'] = base_path() . '/vendor/alipay/cacert.pem';
        $alipay_config['transport'] = 'https';

        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);

        $verify_result = $alipayNotify->verifyNotify();

        if ($verify_result == false) {
            die('fail');
        }

        $this->_notifys($_REQUEST);
    }

    /**
     * 退款回调
     */
    public function refundnotify() {
        //file_put_contents('/mnt/www/paotui/storage/logs/pay.log',print_r($_REQUEST,true));

        $batch_no = $_POST['batch_no'];

        //批量退款数据中转账成功的笔数
        $success_num = $_POST['success_num'];

        $userRefundLog = UserRefundLog::where('sn', $batch_no)->first();

        $payment = Payment::where('code', $userRefundLog->payment_type)->first()->toArray();

        $payment = $payment['config'];

        require_once base_path() . '/vendor/alipay/alipay_pc_notify.class.php';

        $alipay_config['partner'] = $payment['partnerId'];
        $alipay_config['seller_email'] = $payment['sellerId'];
        $alipay_config['key'] = $payment['partnerKey'];
        $alipay_config['sign_type'] = 'MD5';
        $alipay_config['input_charset'] = 'utf-8';
        $alipay_config['cacert'] = base_path() . '/vendor/alipay/cacert.pem';
        $alipay_config['transport'] = 'https';

        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);

        $verify_result = $alipayNotify->verifyNotify();

        if ($verify_result == false) {
            die('fail');
        }

        // 都是单笔退款
        if ($success_num == 1) {
            $userRefundLog->status = 1;

            $userRefundLog->save();

            $refund = Refund::where("id", $userRefundLog->refund_id)->first();

            $refund->status = 1;

            $refund->save();

            $order = Order::where("id", $refund->order_id)->with('user')->first();

            $order->status = ORDER_STATUS_REFUND_SUCCESS;

            $order->save();

            $order = $order->toArray();

            try {
                if ($order['promotionSnId'] > 0) {
                    PromotionSn::where('id', $order['promotionSnId'])->update(['use_time' => 0]);
                }
                //更新商家扩展,资金日志
                SellerMoneyLog::where('type', 'order_refund')->where('related_id', $order->id)->update(['status' => 1]);
                SellerMoneyLog::where('type', 'order_pay')->where('related_id', $order->id)->update(['status' => 4]);
                //SellerService::decrementExtend($order->seller_id, 'wait_confirm_money', $order->seller_fee);
                //通知客户
                PushMessageService::notice($order['user']['id'], $order['user']['mobile'], 'order.refundpay', $order);
            } catch (Exception $e) {
                
            }
        }

        die('success');
    }

    public function webnotify() {
        $request = (array) @simplexml_load_string($_REQUEST['notify_data'], 'SimpleXMLElement', LIBXML_NOCDATA);
        file_put_contents('/mnt/www/paotui/storage/logs/webnotify.log', var_export($_REQUEST, true), FILE_APPEND);
        if (!$request) {
            die('参数不全');
        }

        if (!isset($request['seller_id']) ||
                !isset($request['trade_status']) ||
                !isset($request['out_trade_no']) ||
                !isset($request['notify_id']) ||
                !isset($_REQUEST['sign'])) {
            die('参数不全');
        }

        if ($request['trade_status'] != 'TRADE_SUCCESS' && $request['trade_status'] != 'TRADE_FINISHED') {
            die('success');
        }

        $payment = Payment::where('code', 'alipayWap')->first()->toArray();
        $payment = $payment['config'];

        if ($request['seller_id'] != $payment['partnerId']) {
            die('非法请求');
        }

        $check_status = trim(@file_get_contents('https://mapi.alipay.com/gateway.do?service=notify_verify&partner=' . $payment['partnerId'] . '&notify_id=' . $request['notify_id']));
        if ($check_status !== 'true') {
            die('不是支付宝请求');
        }

        $para_sort = [];
        $para_sort['service'] = $_REQUEST['service'];
        $para_sort['v'] = $_REQUEST['v'];
        $para_sort['sec_id'] = $_REQUEST['sec_id'];
        $para_sort['notify_data'] = $_REQUEST['notify_data'];

        require_once base_path() . '/vendor/alipay/alipay_core.function.php';
        require_once base_path() . '/vendor/alipay/alipay_md5.function.php';
        $check_status = md5Verify(createLinkstring($para_sort), $_REQUEST['sign'], $payment['partnerKey']);
        if (!$check_status) {
            die('签名错误');
        }

        $this->_notify($request);
    }

    /**
     * 会员支付公共回调处理方法
     */
    private function _notify($request) {
        $userPayLog = UserPayLog::where('sn', $request['out_trade_no'])->first();

        if (!$userPayLog) {
            die('找不到支付日志');
        }

        if ($userPayLog->status == 1) {
            die('该订单已支付，请勿重复刷单');
        }

        if ($userPayLog->activity_id < 1) {
            if ($userPayLog->order_id < 1) {
                $this->recharge($request, $userPayLog); //充值
            } else {
                $this->order($request, $userPayLog); //订单
            }
        } else {
            $this->activity($request, $userPayLog); //活动
        }
    }

    /**
     * 商家支付公共回调处理方法
     */
    private function _notifys($request) {
        $sellerPayLog = SellerPayLog::where('sn', $request['out_trade_no'])->first();

        if (!$sellerPayLog) {
            die('找不到支付日志');
        }

        if ($sellerPayLog->status == 1) {
            die('该订单已支付，请勿重复刷单');
        }

        $this->orders($request, $sellerPayLog); //订单
        // if ($sellerPayLog->activity_id < 1) {
        //     $this->orders($request, $userPayLog); //订单
        // } else {
        //     $this->activity($request, $userPayLog); //活动
        // }
    }

    /**
     * [order 处理会员订单]
     * @param  [type] $request    [description]
     * @param  [type] $userPayLog [description]
     * @return [type]             [description]
     */
    private function order($request, $userPayLog) {
        $order = Order::with('goods')->find($userPayLog->order_id);

        if (!$order) {
            die('找不到订单');
        }

        if ($order->pay_status == 1) {
            die('订单已支付');
        }
        // if ($order->status != ORDER_STATUS_WAIT_PAY) {
        // die('订单不能支付');
        // }

        $goods = $order['goods'];
        $isCard = [];
        foreach ($goods as $value) {//75844
            $tmp = json_decode($value, true);
            if ($tmp['goodsId'] == '75837') {
                $isCard[] = 1;
            }
            if ($tmp['goodsId'] == '75844') {
                $isCard[] = 2;
            }
        }
        $userPayLog->pay_account = $request['buyer_email'];
        $userPayLog->pay_time = UTC_TIME;
        $userPayLog->pay_day = UTC_DAY;
        $userPayLog->status = 1;
        $userPayLog->trade_no = $request['trade_no'];

        DB::beginTransaction();
        try {
            $userPayLog->save();
            //$endTime 	= UTC_TIME + (int)\YiZan\Services\SystemConfigService::getConfigByCode('system_seller_order_confirm') * 60;
            //修改状态 flag 为是否自提
            $isDiy = ($order['fre_time'] == 0 && $order['app_time'] == 0) ? ORDER_STATUS_FINISH_USER : ORDER_STATUS_PAY_SUCCESS;
            $status = Order::where('id', $userPayLog->order_id)->update([
                'pay_time' => UTC_TIME,
                'pay_status' => ORDER_PAY_STATUS_YES,
                'status' => $isDiy,
                'pay_type' => $userPayLog->payment_type
            ]);

            if ($status) {
                //更新服务人员待到帐金额
                SellerService::incrementExtend($order->seller_id, 'wait_confirm_money', $order->seller_fee);

                // 写入日志
                SellerMoneyLogService::createLog(
                        $order->seller_id, 'order_pay', $order->id, $order->pay_fee, '订单支付:' . $order->sn, 1
                );
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $status = false;
        }


        if ($status) {
            foreach ($isCard as $is) {
                if ($is == 1) {
                    PromotionService::send(
                            34, 'AU', 6, $order->user_id
                    );
                    PromotionService::send(
                            33, 'AU', 8, $order->user_id
                    );
                    PromotionService::send(
                            35, 'AU', 1, $order->user_id
                    );
                }
                if ($is == 2) {
                    PromotionService::send(
                            34, 'AU', 2, $order->user_id
                    );
                    PromotionService::send(
                            33, 'AU', 4, $order->user_id
                    );
                    PromotionService::send(
                            35, 'AU', 1, $order->user_id
                    );
                }
            }
            if (count($isCard) > 0) {
                $card = UserCard::where('user_id', $order->user_id)
                                ->orderBy('id', 'DESC')->first();
                if ($card['status'] == 0) {
                    UserCard::where('user_id', $order->user_id)
                            ->where('status', 0)->update(array(
                        'money' => $order['pay_fee'],
                        'ctime' => time(),
                        'status' => 1
                    ));
                } else {
                    UserCard::insert(array(
                        'user_id' => $order->user_id,
                        'card_id' => $card['card_id'],
                        'money' => $order['pay_fee'],
                        'ctime' => time(),
                        'status' => 1
                    ));
                }
            }
            $order = OrderService::getOrderById($order->user_id, $order->id);
            try {
                PushMessageService::notice($order['seller']['userId'], $order['seller']['mobile'], 'order.pay', $order, ['sms', 'app'], 'seller', '3', $order['id'], "pay.caf");
                if ($order['staff'] && $order['seller']['userId'] != $order['staff']['userId']) {
                    PushMessageService::notice($order['staff']['userId'], $order['staff']['mobile'], 'order.pay', $order, ['sms', 'app'], 'staff', '3', $order['id'], "pay.caf");
                }
            } catch (Exception $e) {
                
            }

            die('success');
        } else {
            die('更新订单失败');
        }
    }

    /**
     * [orders 处理商家订单]
     * @param  [type] $request    [description]
     * @param  [type] $userPayLog [description]
     * @return [type]             [description]
     */
    private function orders($request, $sellerPayLog) {

        $sellerPayLog->pay_account = $request['buyer_email'];
        $sellerPayLog->pay_time = UTC_TIME;
        $sellerPayLog->pay_day = UTC_DAY;
        $sellerPayLog->status = 1;
        $sellerPayLog->trade_no = $request['trade_no'];

        DB::beginTransaction();
        try {
            $status = $sellerPayLog->save();

            //更新服务人员余额
            SellerExtend::where('seller_id', $sellerPayLog->seller_id)
                    ->update([
                        'money' => new Expression("money + " . $sellerPayLog->money),
                        'total_money' => new Expression("total_money + " . $sellerPayLog->money),
            ]);

            // 写入日志
            SellerMoneyLogService::createLog(
                    $sellerPayLog->seller_id, SellerMoneyLog::TYPE_SELLER_RECHARGE, $sellerPayLog->id, $sellerPayLog->money, '充值:' . $sellerPayLog->sn, 1
            );
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $status = false;
        }

        die('success');
        // if ($status) {
        //     $order = OrderService::getOrderById($order->user_id, $order->id);
        //     try {
        //         PushMessageService::notice( $order['seller']['userId'],  $order['seller']['mobile'], 'order.create',  $order,['sms', 'app'],'seller','3',$order['id'], "music1.caf");
        //         if($order['staff'] && $order['seller']['userId'] != $order['staff']['userId']){
        //             PushMessageService::notice( $order['staff']['userId'],  $order['staff']['mobile'], 'order.create',  $order,['sms', 'app'],'staff','3',$order['id'], "music1.caf");
        //         }
        //     } catch (Exception $e) {
        //     }
        //     die('success');
        // } else {
        //     die('更新订单失败');
        // }
    }

    /**
     * [activity 处理活动]
     * @param  [type] $request    [description]
     * @param  [type] $userPayLog [description]
     * @return [type]             [description]
     */
    private function activity($request, $userPayLog) {
        $userPayLog->pay_account = $request['buyer_email'];
        $userPayLog->pay_time = UTC_TIME;
        $userPayLog->pay_day = UTC_DAY;
        $userPayLog->status = 1;
        $userPayLog->trade_no = $request['trade_no'];

        DB::beginTransaction();
        try {
            $status = $userPayLog->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $status = false;
        }

        if ($status) {
            ActivityService::payfinish($userPayLog->user_id, $userPayLog->sn, $userPayLog->activity_id);
            die('success');
        } else {
            die('更新活动失败');
        }
    }

    /**
     * [recharge 充值]
     * @param  [type] $request    [description]
     * @param  [type] $userPayLog [description]
     * @return [type]             [description]
     */
    private function recharge($request, $userPayLog) {
        $user = User::find($userPayLog->user_id);
        if (!$user) {
            die('找不到会员');
        }

        $userPayLog->pay_account = $request['buyer_email'];
        $userPayLog->pay_time = UTC_TIME;
        $userPayLog->pay_day = UTC_DAY;
        $userPayLog->status = 1;
        $userPayLog->trade_no = $request['trade_no'];
        $userPayLog->balance = $user->balance + $userPayLog->money;

        DB::beginTransaction();
        try {
            $status = $userPayLog->save();
            User::where('id', $userPayLog->user_id)->update([
                'balance' => new Expression("balance + " . $userPayLog->money),
                'total_money' => new Expression("total_money + " . $userPayLog->money),
            ]);
            DB::commit();
            die('success');
        } catch (Exception $e) {
            DB::rollback();
            die('更新余额失败');
        }
    }

}
