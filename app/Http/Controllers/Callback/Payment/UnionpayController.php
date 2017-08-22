<?php
namespace YiZan\Http\Controllers\Callback\Payment;

use YiZan\Http\Controllers\Callback\BaseController;
use YiZan\Models\UserPayLog;
use YiZan\Models\SellerPayLog;
use YiZan\Models\Order;
use YiZan\Models\User;
use YiZan\Models\Payment;
use YiZan\Models\SellerExtend;
use YiZan\Models\SellerMoneyLog;

use YiZan\Services\SellerService;
use YiZan\Services\SellerMoneyLogService;
use YiZan\Services\PushMessageService;
use YiZan\Services\ActivityService;
use YiZan\Services\OrderService;
use Illuminate\Database\Query\Expression;

use DB, Exception, Input, Redirect;

/**
 * 银联支付
 */
class UnionpayController extends BaseController {

    /**
     * 通知
     */
    public function notify(){
        require_once base_path().'/vendor/unionpay/utf8/func/common.php';
        require_once base_path().'/vendor/unionpay/utf8/func/secureUtil.php';
        $request = Input::all();
        //file_put_contents('/mnt/test/sq/storage/logs/notify.log', var_export($request, true), FILE_APPEND);
        if (isset ( $request ['signature'] )) {
            if(verify($request)){
                $userPayLog = UserPayLog::where('sn', $request['orderId'])->first();
                if (!$userPayLog) {
                    die('找不到支付日志');
                } 
                if($userPayLog->status != 0 && $userPayLog->order_id > 0){
                    Redirect::to(u('wap#Order/detail',['id'=>$userPayLog->order_id]))->send();
                } else if($userPayLog->status != 0 && $userPayLog->order_id < 1) {
                    die('订单状态错误');
                }

                if ($userPayLog->order_id < 1) {
                    self::recharge($request, $userPayLog); //充值
                } else {
                    self::order($request, $userPayLog);
                }
            } else {
                die('交易失败！');
            }
        } else {
            die('交易失败！');
        }
    }

    /**
     * 处理订单
     */
    public function order($request, $userPayLog){
        $order = Order::find($userPayLog->order_id);

        if (!$order) {
            die('找不到订单');
        }

        if ($order->pay_status == 1) {
            Redirect::to(u('wap#Order/detail',['id'=>$userPayLog->order_id]))->send();
        }

        $userPayLog->pay_account = '商户号：'.$request['merId'];
        $userPayLog->pay_time 	 = UTC_TIME;
        $userPayLog->pay_day 	 = UTC_DAY;
        $userPayLog->status 	 = 1;
        $userPayLog->trade_no 	 = $request['queryId'];

        DB::beginTransaction();
        try {
            $status = $userPayLog->save();
            //修改状态 flag 为是否自提
            $isDiy = ($order['fre_time'] == 0 && $order['app_time'] == 0) ? ORDER_STATUS_FINISH_USER : ORDER_STATUS_PAY_SUCCESS;
            $status = Order::where('id', $userPayLog->order_id)->update([
                'pay_time' 		            => UTC_TIME,
                'pay_status'	            => ORDER_PAY_STATUS_YES,
                'status'                    => $isDiy,
                'pay_type'                  => $userPayLog->payment_type
            ]);

            if ($status) {
                //更新服务人员待到帐金额
                SellerService::incrementExtend($order->seller_id, 'wait_confirm_money', $order->seller_fee);

                // 写入日志
                SellerMoneyLogService::createLog(
                    $order->seller_id,
                    TYPE_ORDER_PAY,
                    $order->id,
                    $order->pay_fee,
                    '订单支付:'.$order->sn,
                    1
                );
            }
            DB::commit();
        }
        catch (Exception $e)
        {
            DB::rollback();
            $status = false;
        }


        if ($status) {
            $order = OrderService::getOrderById($order->user_id, $order->id);
            try {
                PushMessageService::notice( $order['seller']['userId'],  $order['seller']['mobile'], 'order.pay',  $order,['sms', 'app'],'seller','3',$order['id'], "pay.caf");
                if($order['staff'] && $order['seller']['userId'] != $order['staff']['userId']){
                    PushMessageService::notice( $order['staff']['userId'],  $order['staff']['mobile'], 'order.pay',  $order,['sms', 'app'],'staff','3',$order['id'], "pay.caf");
                }
            } catch (Exception $e) {

            }

            die('success');
        } else {
            die('更新订单失败');
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
        $userPayLog->pay_account = '商户号：'.$request['merId'];
        $userPayLog->pay_time    = UTC_TIME;
        $userPayLog->pay_day     = UTC_DAY;
        $userPayLog->status      = 1;
        $userPayLog->trade_no    = $request['queryId'];
        $userPayLog->balance     = $user->balance + $userPayLog->money;

        DB::beginTransaction();
        try {
            $userPayLog->save();
            User::where('id', $userPayLog->user_id)->update([
                    'balance'       => new Expression("balance + " . $userPayLog->money), 
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
