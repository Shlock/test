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
    Exception;

/**
 * 微信支付
 */
class WeixinController extends BaseController {

    public function notify() {
        $this->_notify('weixin');
    }

    public function notifys() {
        $this->_notifys('weixin');
    }

    public function jsnotify() {
        $this->_notify('weixinJs');
    }

    private function _notify($type) {
        $request = file_get_contents('php://input', 'r');
        if (empty($request)) {
            die('<xml>
				   <return_code><![CDATA[FAIL]]></return_code>
				   <return_msg><![CDATA[参数错误]]></return_msg>
				</xml>');
        }

        $payment = Payment::where('code', $type)->first()->toArray();
        $payment = $payment['config'];

        $xml = (array) @simplexml_load_string($request, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$xml ||
                !isset($xml['appid']) ||
                !isset($xml['mch_id']) ||
                $xml['appid'] != $payment['appId'] ||
                $xml['mch_id'] != $payment['partnerId']) {
            die('<xml>
				   <return_code><![CDATA[FAIL]]></return_code>
				   <return_msg><![CDATA[参数错误]]></return_msg>
				</xml>');
        }

        if ($xml['result_code'] != 'SUCCESS') {
            die('<xml>
				   <return_code><![CDATA[SUCCESS]]></return_code>
				   <return_msg><![CDATA[OK]]></return_msg>
				</xml>');
        }

        $sign = $xml['sign'];
        $args = '';

        unset($xml['sign']);
        ksort($xml);
        foreach ($xml as $key => $data) {
            $args .= "{$key}={$data}&";
        }

        $args = strtoupper(md5("{$args}key={$payment['partnerKey']}"));
        if ($args != $sign) {
            die('<xml>
				   <return_code><![CDATA[FAIL]]></return_code>
				   <return_msg><![CDATA[签名错误]]></return_msg>
				</xml>');
        }

        $userPayLog = UserPayLog::where('sn', $xml['out_trade_no'])->first();
        if (!$userPayLog) {
            die('<xml>
				   <return_code><![CDATA[FAIL]]></return_code>
				   <return_msg><![CDATA[找不到支付日志]]></return_msg>
				</xml>');
        }
        if ($userPayLog->status == 1) {
            die('<xml>
				   <return_code><![CDATA[FAIL]]></return_code>
				   <return_msg><![CDATA[该订单已支付，请勿重复刷单]]></return_msg>
				</xml>');
        }
        if ($userPayLog->activity_id < 1) {
            if ($userPayLog->order_id < 1) {
                $this->recharge($xml, $userPayLog); //充值
            } else {
                $this->order($xml, $userPayLog); //订单
            }
        } else {
            $this->activity($xml, $userPayLog); //活动
        }
    }

    /**
     * 商家支付回调通知
     */
    private function _notifys($type) {
        $request = file_get_contents('php://input', 'r');
        if (empty($request)) {
            die('<xml>
				   <return_code><![CDATA[FAIL]]></return_code>
				   <return_msg><![CDATA[参数错误]]></return_msg>
				</xml>');
        }

        $payment = Payment::where('code', $type)->first()->toArray();
        $payment = $payment['config'];

        $xml = (array) @simplexml_load_string($request, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$xml ||
                !isset($xml['appid']) ||
                !isset($xml['mch_id']) ||
                $xml['appid'] != $payment['appId'] ||
                $xml['mch_id'] != $payment['partnerId']) {
            die('<xml>
				   <return_code><![CDATA[FAIL]]></return_code>
				   <return_msg><![CDATA[参数错误]]></return_msg>
				</xml>');
        }

        if ($xml['result_code'] != 'SUCCESS') {
            die('<xml>
				   <return_code><![CDATA[SUCCESS]]></return_code>
				   <return_msg><![CDATA[OK]]></return_msg>
				</xml>');
        }

        $sign = $xml['sign'];
        $args = '';

        unset($xml['sign']);
        ksort($xml);
        foreach ($xml as $key => $data) {
            $args .= "{$key}={$data}&";
        }

        $args = strtoupper(md5("{$args}key={$payment['partnerKey']}"));
        if ($args != $sign) {
            die('<xml>
				   <return_code><![CDATA[FAIL]]></return_code>
				   <return_msg><![CDATA[签名错误]]></return_msg>
				</xml>');
        }

        $sellerPayLog = SellerPayLog::where('sn', $xml['out_trade_no'])->first();
        if (!$sellerPayLog) {
            die('<xml>
				   <return_code><![CDATA[FAIL]]></return_code>
				   <return_msg><![CDATA[找不到支付日志]]></return_msg>
				</xml>');
        }
        if ($sellerPayLog->status == 1) {
            die('<xml>
				   <return_code><![CDATA[FAIL]]></return_code>
				   <return_msg><![CDATA[该订单已支付，请勿重复刷单]]></return_msg>
				</xml>');
        }

        $sellerPayLog->pay_account = $xml['openid'];
        $sellerPayLog->pay_time = UTC_TIME;
        $sellerPayLog->pay_day = UTC_DAY;
        $sellerPayLog->status = 1;
        $sellerPayLog->trade_no = $xml['transaction_id'];

        DB::beginTransaction();
        try {
            $sellerPayLog->save();
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
            die('<xml>
				   <return_code><![CDATA[FAIL]]></return_code>
				   <return_msg><![CDATA[更新订单失败]]></return_msg>
				</xml>');
        }
        die('<xml>
			   <return_code><![CDATA[SUCCESS]]></return_code>
			   <return_msg><![CDATA[OK]]></return_msg>
			</xml>');
    }

    /**
     * [order 订单]
     * @param  [type] $userPayLog [description]
     * @return [type]             [description]
     */
    private function order($xml, $userPayLog) {
        $order = Order::with('goods')->find($userPayLog->order_id);
        if (!$order) {
            die('<xml>
					   <return_code><![CDATA[FAIL]]></return_code>
					   <return_msg><![CDATA[找不到订单]]></return_msg>
					</xml>');
        }

        if ($order->pay_status == 1) {
            die('<xml>
					   <return_code><![CDATA[FAIL]]></return_code>
					   <return_msg><![CDATA[订单已支付]]></return_msg>
					</xml>');
        }
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
        /* if ($order->status != ORDER_STATUS_WAIT_PAY) {
          die('<xml>
          <return_code><![CDATA[FAIL]]></return_code>
          <return_msg><![CDATA[订单不能支付]]></return_msg>
          </xml>');
          } */

        $userPayLog->pay_account = $xml['openid'];
        $userPayLog->pay_time = UTC_TIME;
        $userPayLog->pay_day = UTC_DAY;
        $userPayLog->status = 1;
        $userPayLog->trade_no = $xml['transaction_id'];

        DB::beginTransaction();
        try {
            $status = $userPayLog->save();
            if ($status) {
                $endTime = UTC_TIME + (int) \YiZan\Services\SystemConfigService::getConfigByCode('system_seller_order_confirm') * 60;
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

                    //写入日志
                    SellerMoneyLogService::createLog(
                            $order->seller_id, 'order_pay', $order->id, $order->pay_fee, '订单支付:' . $order->sn, 1
                    );
                }
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
            die('<xml>
				   <return_code><![CDATA[SUCCESS]]></return_code>
				   <return_msg><![CDATA[OK]]></return_msg>
				</xml>');
        } else {
            die('<xml>
				   <return_code><![CDATA[FAIL]]></return_code>
				   <return_msg><![CDATA[更新订单失败]]></return_msg>
				</xml>');
        }
    }

    /**
     * [activity 活动]
     * @param  [type] $userPayLog [description]
     * @return [type]             [description]
     */
    private function activity($xml, $userPayLog) {
        $userPayLog->pay_account = $xml['openid'];
        $userPayLog->pay_time = UTC_TIME;
        $userPayLog->pay_day = UTC_DAY;
        $userPayLog->status = 1;
        $userPayLog->trade_no = $xml['transaction_id'];

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
            die('<xml>
				   <return_code><![CDATA[SUCCESS]]></return_code>
				   <return_msg><![CDATA[OK]]></return_msg>
				</xml>');
        } else {
            die('<xml>
				   <return_code><![CDATA[FAIL]]></return_code>
				   <return_msg><![CDATA[更新订单失败]]></return_msg>
				</xml>');
        }
    }

    /**
     * [recharge 充值]
     * @param  [type] $request    [description]
     * @param  [type] $userPayLog [description]
     * @return [type]             [description]
     */
    private function recharge($xml, $userPayLog) {
        if ($userPayLog->status == 1) {
            die('<xml>
				   <return_code><![CDATA[FAIL]]></return_code>
				   <return_msg><![CDATA[已充值]]></return_msg>
				</xml>');
        }
        $user = User::find($userPayLog->user_id);
        if (!$user) {
            die('<xml>
				   <return_code><![CDATA[FAIL]]></return_code>
				   <return_msg><![CDATA[找不到会员]]></return_msg>
				</xml>');
        }

        $userPayLog->pay_account = $xml['openid'];
        $userPayLog->pay_time = UTC_TIME;
        $userPayLog->pay_day = UTC_DAY;
        $userPayLog->status = 1;
        $userPayLog->trade_no = $xml['transaction_id'];
        $userPayLog->balance = $user->balance + $userPayLog->money;

        DB::beginTransaction();
        try {
            $userPayLog->save();
            User::where('id', $userPayLog->user_id)->update([
                'balance' => new Expression("balance + " . $userPayLog->money),
                'total_money' => new Expression("total_money + " . $userPayLog->money),
            ]);
            DB::commit();
            $status = true;
        } catch (Exception $e) {
            DB::rollback();
            $status = false;
        }
        if ($status) {
            die('<xml>
				   <return_code><![CDATA[SUCCESS]]></return_code>
				   <return_msg><![CDATA[OK]]></return_msg>
				</xml>');
        } else {
            die('<xml>
				   <return_code><![CDATA[FAIL]]></return_code>
				   <return_msg><![CDATA[充值失败]]></return_msg>
				</xml>');
        }
    }

}
