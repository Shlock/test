<?php 
namespace YiZan\Services\System;

use YiZan\Models\SellerStaff;
use YiZan\Services\SystemConfigService;
use YiZan\Models\System\Order;
use YiZan\Models\Seller;
use YiZan\Models\StaffAppoint;
use YiZan\Models\StaffServiceTime;
use YiZan\Models\StaffLeave;
use YiZan\Models\Goods;
use YiZan\Models\PromotionSn;
use YiZan\Models\UserPayLog;
use YiZan\Models\OrderPromotion;
use YiZan\Services\System\PushMessageService;

use YiZan\Services\SellerMoneyLogService;
use YiZan\Models\SellerMoneyLog;
use YiZan\Models\SellerExtend;
use YiZan\Models\User;

use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use Exception, DB, Lang, Validator, App,Rand;

class OrderService extends \YiZan\Services\OrderService 
{
    /**
     * 订单不存在
     */
    const ORDER_NOT_EXIST = 50101;
    /**
     * 订单状态错误
     */
    const ORDER_STATUS_ERROR = 50103;
    /**
     * 该订单不允许删除
     */
    const ORDER_NOT_DELETE = 50104;

/**
 * 订单列表
 * @param  string $sn 订单号 
 * @param  string $userMobile 会员手机号
 * @param  string $sellerMobile 服务人员手机号
 * @param  int $beginTime 开始时间
 * @param  int $endTime 结束时间
 * @param  int $status 订单状态
 * @param  int $payStatus 支付状态
 * @param  int $sellerName 商家名称
 * @param  int $page 页码
 * @param  int $pageSize 每页数
 * @return array          订单列表
 */
public static function getSystemList($orderType, $sn, $mobile, $beginTime, $endTime, $payStatus, $status, $sellerName, $page, $pageSize)
{
    self::endOrder();
    $list = Order::orderBy('id', 'desc')->where('status','!=', ORDER_STATUS_ADMIN_DELETE);
    if ($orderType > 0) {
        $list->where('order_type', $orderType);
    }
    if($sn != '') {
        $list->where('sn', $sn);
    }
    if($mobile != '') {
        $list->where('mobile', $mobile);
    }
    if($beginTime > 0)
    {
        $list->where('create_time', '>=', $beginTime);
    }

    if($endTime > 0)
    {
        $list->where('create_time', '<=', $endTime);
    }

    if($payStatus  > '-1')
    {
        $list->where('pay_status',  $payStatus);
    }

    if ($sellerName != '') {
        $sellerName = empty($sellerName) ? '' : String::strToUnicode($sellerName,'+');
        $list->whereIn('seller_id', function($query) use ($sellerName){
            $query->select('id')
                ->from('seller')
                ->whereRaw("MATCH(name_match) AGAINST('{$sellerName}' IN BOOLEAN MODE)");
        });
    }

    switch ($status) {
        case '1': //待发货  102 110
            $list->whereIn('status',
                [
                    ORDER_STATUS_BEGIN_USER,
                    ORDER_STATUS_PAY_SUCCESS,
                    ORDER_STATUS_PAY_DELIVERY,
                    ORDER_STATUS_AFFIRM_SELLER
                ]);
            break;
        case '2'://待完成 106 107
            $list->whereIn('status',
                [
                    ORDER_STATUS_FINISH_STAFF
                ]) ;
            break;
        case '3':// 已完成
            $list->whereIn('status',
                [
                    ORDER_STATUS_FINISH_SYSTEM,
                    ORDER_STATUS_FINISH_USER,
                    ORDER_STATUS_USER_DELETE,
                    ORDER_STATUS_SELLER_DELETE
                ])->where(function($query){
                    $query->where('buyer_finish_time', '>', 0)
                        ->orWhereBetween('auto_finish_time', [1,UTC_TIME]);
                });
            break;
        case '4'://拒绝 301 302 303 400 401 402 403 404 311 312
            $list->whereIn('status',
                [
                    ORDER_STATUS_CANCEL_USER,
                    ORDER_STATUS_CANCEL_AUTO,
                    ORDER_STATUS_CANCEL_SELLER,
                    ORDER_STATUS_CANCEL_ADMIN
                ]) ;
            break;
        case '5'://退款
            $list->whereIn('status',
                [
                    ORDER_STATUS_REFUND_AUDITING,
                    ORDER_STATUS_REFUND_HANDLE,
                    ORDER_STATUS_REFUND_FAIL,
                    ORDER_STATUS_REFUND_SUCCESS
                ]) ;
            break;
        default:
            break;
    }
    $totalCount = $list->count();
    $list = $list->skip(($page - 1) * $pageSize)
    ->take($pageSize)
    ->with('user','seller','staff')
    ->get()
    ->toArray();
    return ["list"=>$list, "totalCount"=>$totalCount];
    
}
	/**
     * 获取订单
     * @param  int $id 订单id
     * @return array   订单
     */
	public static function getSystemOrderById($id) 
    {
        self::endOrder();
        $data = Order::where('id', $id)->with('user', 'seller', 'staff', 'orderGoods','payment')->first();
        return $data;
	}
	
	/**
	 * 更新订单
	 * @param  int $id 订单id
	 * @param  int $status 状态
	 * @param  string $content 处理结果
	 * @return array   更新结果
	 */
	/*public static function updateSystemOrder($id, $status, $content)
	{
	    $result =
	    [
	        'code'	=> 0,
	        'data'	=> null,
	        'msg'	=> Lang::get('api.success.update_info')
	    ];
	
	    if($status != ORDER_STATUS_REFUND_HANDLE &&
	        $status != ORDER_USER_CANCEL_SERVICE &&
	        $status != ORDER_STATUS_AFFIRM_SERVICE &&
	        $status != ORDER_ADMIN_REFUSE_SERVICE)
	    {
	        $result['code'] = self::ORDER_STATUS_ERROR;
	
	        return $result;
	    }
	
	    $order = Order::where('id', $id)->first();
	
	    //没有订单
	    if ($order == false)
	    {
	        $result['code'] = self::ORDER_NOT_EXIST;
	
	        return $result;
	    }
	
	    if($order->getIsCanRefundAttribute() == false &&
	        $order->getIsCanCancelAttribute() == false &&
	        $order->getIsCanAcceptAttribute() == false)
	    {
	        $result['code'] = self::ORDER_STATUS_ERROR;
	
	        return $result;
	    }
	
	    if($status == ORDER_USER_CANCEL_SERVICE)
	    {
	        $order->status = ORDER_USER_CANCEL_SERVICE;
	
	        $order->buyer_cancel_time = UTC_TIME;
	
	        //有优惠券，则退回优惠券
	        $return_promotion = PromotionService::returnPromotion($order);
	
	        if(!$return_promotion){
	            $result['code'] = 50115;
	            return $result;
	        }
	    }
	    else if($status == ORDER_STATUS_REFUND_HANDLE)
	    {
	        $order->status = ORDER_STATUS_REFUND_HANDLE;
	
	        $order->refund_time = UTC_TIME;
	
	        $order->deposit_refund_time = UTC_TIME;
	
	        //有优惠券，则退回优惠券
	        $return_promotion = PromotionService::returnPromotion($order);
	
	        if(!$return_promotion){
	            $result['code'] = 50115;
	            return $result;
	        }
	    }
	    else if($status == ORDER_STATUS_AFFIRM_SERVICE ||
	        $status == ORDER_ADMIN_REFUSE_SERVICE)
	    {
	        $order->status = $status;
	
	        $order->seller_confirm_time = UTC_TIME;
	
	        if($status == ORDER_ADMIN_REFUSE_SERVICE){
	
	            //有优惠券，则退回优惠券
	            $return_promotion = PromotionService::returnPromotion($order);
	
	            if(!$return_promotion){
	                $result['code'] = 50115;
	                return $result;
	            }
	        }
	
	    }
	
	    $order->save();
	
	    $data = self::getSystemOrderById($id)->toArray();
	    //当状态为接受订单或者拒绝订单的时候,推送消息
	    if ($status == ORDER_STATUS_AFFIRM_SERVICE ||
	        $status == ORDER_ADMIN_REFUSE_SERVICE) {
	            $noticeTpe = $status == ORDER_STATUS_AFFIRM_SERVICE ? 'order.accept' : 'order.refund';
	            PushMessageService::notice($data['userId'],$data['mobile'], $noticeTpe,['sn' => $data['sn']],['sms','app'],'buyer',4,$id);
	        }
	        return $result;
	}*/
    
    /**
     * 更新订单
     * @param  int $id 订单id
     * @param  int $status 状态
     * @param  string $content 处理结果
     * @return array   更新结果
     */
	public static function updateSystemOrder($id, $status, $refuseContent) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.update_info')
		];

        if(($status != ORDER_STATUS_AFFIRM_SELLER &&
                $status != ORDER_STATUS_CANCEL_ADMIN && 
                    $status != ORDER_STATUS_START_SERVICE && 
                        $status != ORDER_STATUS_FINISH_STAFF  ))
        {
            $result['code'] = self::ORDER_STATUS_ERROR;
	    	return $result;
        }
        
        $order = Order::where('id', $id)->first();
        //没有订单
		if ($order == false) 
        {
			$result['code'] = self::ORDER_NOT_EXIST;
            
	    	return $result;
		}

        if(( $order->isCanAccept == false &&
                $order->isCanCancel == false &&
                    $order->isCanStartService == false &&
                        $order->isCanFinish == false) )
        {
            $result['code'] = self::ORDER_STATUS_ERROR;
            
	    	return $result;
        }
        
        if($status == ORDER_STATUS_CANCEL_ADMIN)
        {
            //如果取消订单支付金额大于0则订单状态为取消且退款中
            /*if($order->pay_status == 1 && $order->pay_fee >= 0.0001 && $order->isCashOnDelivery === false){
                $order->status = ORDER_STATUS_CANCEL_REFUNDING;
            }else{*/
                $order->status = ORDER_STATUS_CANCEL_ADMIN;
           // }
            
            $order->cancel_time = UTC_TIME;

            $order->cancel_remark = $refuseContent;
        }
        else if($status == ORDER_STATUS_AFFIRM_SELLER)
        {
            $order->status = ORDER_STATUS_AFFIRM_SELLER;
            
            $order->seller_confirm_time = UTC_TIME;
        }
        else if($status == ORDER_STATUS_START_SERVICE){
            $order->status = ORDER_STATUS_START_SERVICE;
        } else if($status == ORDER_STATUS_FINISH_STAFF){
            $order->status = ORDER_STATUS_FINISH_STAFF;
            $order->staff_finish_time = UTC_TIME; 
            $autoFinishDay = SystemConfigService::getConfigByCode('system_buyer_order_confirm');
            $autoFinishTime = $autoFinishDay * 24 * 3600 + UTC_TIME;
            $order->auto_finish_time = $autoFinishTime; 
        }

        
        $order->save();
        $data = self::getSystemOrderById($id)->toArray();
        if ($status == ORDER_STATUS_CANCEL_ADMIN) {
            self::cancelOrderStock($id);
            //取消返还优惠券
            if ((int)$data['promotionSnId'] > 0) {
                PromotionSn::where('id', $data['promotionSnId'])->update(['use_time'=>0]);
            }
        }


        //当状态为接受订单或者拒绝订单的时候,推送消息
        if ($status == ORDER_STATUS_AFFIRM_SELLER ||
            $status == ORDER_STATUS_CANCEL_ADMIN) {
            $noticeTpe = $status == ORDER_STATUS_AFFIRM_SELLER ? 'order.accept' : 'order.refund';
            PushMessageService::notice($data['userId'],$data['user']['mobile'], $noticeTpe, $data,['sms','app'],'buyer',3, $id);
        }

        if ($status == ORDER_STATUS_CANCEL_ADMIN && $order->isCashOnDelivery()){
            //如果是货到付款则退还商家支付的抽成金额
            $sellerMoneyLog = SellerMoneyLog::where('related_id', $order->id)
                                            ->where('type', SellerMoneyLog::TYPE_DELIVERY_MONEY)
                                            ->first(); 
            //增加商家金额
           if($sellerMoneyLog){ 
               //增加商家金额
               SellerExtend::where('seller_id', $data['sellerId'])
                   ->increment('money', abs($sellerMoneyLog->money));
               //写入增加金额日志
               SellerMoneyLogService::createLog(
                   $data['sellerId'],
                   SellerMoneyLog::TYPE_DELIVERY_MONEY,
                   $id,
                   $data['drawnFee'],
                   '现金支付订单' . $order->sn . '取消，佣金返还',
                   1
               );
           }
        }

        //当已支付且支付金额大于0且状态为取消订单时退款
         if ($status == ORDER_STATUS_CANCEL_ADMIN &&
            $order->pay_status == 1 &&
            $order->pay_fee >= 0.0001 &&
            $order->isCashOnDelivery === false
        ) {
            $pay_type = $order->getPayType();
            $sellerMoneyLogStatus = 0;
            if($order->pay_money > 0){ 
                 //返还支付金额给会员余额
                $user = User::find($order->user_id);
                $user->balance = $user->balance + abs($order->pay_money);
                $user->save(); 
                //创建退款日志
                $balancePayLog = new UserPayLog;
                $balancePayLog->payment_type   = 'balancePay';
                $balancePayLog->pay_type       = 3;//退款
                $balancePayLog->user_id        = $order->user_id;
                $balancePayLog->order_id       = $order->id;
                $balancePayLog->activity_id    = $order->activity_id > 0 ? $order->activity_id : 0;
                $balancePayLog->seller_id      = $order->seller_id;
                $balancePayLog->money          = $order->pay_money;
                $balancePayLog->balance        = $user->balance;
                $balancePayLog->content        = '会员取消订单退款';
                $balancePayLog->create_time    = UTC_TIME;
                $balancePayLog->create_day     = UTC_DAY;
                $balancePayLog->status         = 1;
                $balancePayLog->sn = Helper::getSn();
                $balancePayLog->save();
                $sellerMoneyLogStatus = 1;
            }

             $userPayLogs = UserPayLog::where('order_id', $order->id)
                 ->where('pay_type', 1)
                 ->where('status', 1)
                 ->get()
                 ->toArray();

             if (count($userPayLogs) > 0) {
                 $userRefundLog = [];
                 foreach($userPayLogs as $k=>$v) {
                     if ($v['paymentType'] == 'balancePay') {
                         $userRefundLog[] = [
                             "sn" => $order->sn,
                             "user_id" => $order->user_id,
                             "order_id" => $order->id,
                             "trade_no" => $v['tradeNo'],
                             "seller_id" => $order->seller_id,
                             "payment_type" => $v['paymentType'],
                             "money" => $v['money'],
                             "content" => "平台取消",
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
                             "content" => "平台取消",
                             "create_time" => UTC_TIME,
                             "create_day" => UTC_DAY,
                             "status" => 0
                         ];
                     }

                 }
                 DB::table('refund')->insert($userRefundLog);

                SellerMoneyLogService::createLog(
                    $order->seller_id,
                    SellerMoneyLog::TYPE_ORDER_REFUND,
                    $id,
                    $order->pay_fee,
                    '订单取消退款：'.$order->sn,
                    $sellerMoneyLogStatus
                );
            }

            if ((int)$order->buyer_finish_time > 0 || ((int)$order->auto_finish_time > 0 && (int)$order->auto_finish_time <= UTC_TIME)) {
                \YiZan\Services\SellerService::decrementExtend($order->seller_id, 'total_money', $order->seller_fee);
                \YiZan\Services\SellerService::decrementExtend($order->seller_id, 'money', $order->seller_fee);
            } else {
                \YiZan\Services\SellerService::decrementExtend($order->seller_id, 'wait_confirm_money', $order->seller_fee);
            }
        }
		return $result;
    }
    
	/**
     * 删除订单
     * @param int  $id 订单id
     * @return array   删除结果
     */
	public static function deleteSystemOrder($id) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.delete_info')
		];

		$order = Order::where('id', $id)->first();
        //没有订单
		if ($order == false)
        {
			$result['code'] = self::ORDER_NOT_EXIST;
            
	    	return $result;
		}

		//当订单状态不为卖家删除,订单不能删除
		if ($order->status != ORDER_STATUS_SELLER_DELETE)
        {
			$result['code'] = self::ORDER_NOT_DELETE;
            
	    	return $result;
		}
        
		Order::where('id', $id)->update(['status' => ORDER_STATUS_ADMIN_DELETE]);
        
		return $result;
	}

    /**
     * 更改订单服务日期
     * @param  int $id 订单编号
     * @param int $beginTime 开始时间
     * @param int $endTime 结束时间
     */
    public static function updateDate($id, $beginTime, $endTime) {
        $result = [
                'code'	=> 0,
                'data'	=> null,
                'msg'	=> Lang::get('api_system.success.update_info')
            ];
        $check = Order::where('id', $id)->with('goods')->first();
        if (!$check) {
            $result['code'] = 80001; //订单是否存在
            return $result;
        }
        if ($check->goods->price_type == 2 && ($endTime - $beginTime) != $check->goods_duration) {
            $result['code'] = 80005; //按时收费 ,时长必须与下单时的时长相同
            return $result;
        }
        if ((int)$beginTime < 1 || (int)$endTime < 1) {
            $result['code'] = 80002; //开始时间和结束时间不能为空
            return $result;
        }
        if ($endTime <= $beginTime) {
            $result['code'] = 80003; //结束时间必须大于开始时间
            return $result;
        }

        if (Time::toDate($beginTime,'Ymd') != Time::toDate($endTime, 'Ymd')) {
            $result['code'] = 80008; //开始时间和结束时间必须在同一天
            return $result;
        }

        $check_stime = StaffServiceTime::where('begin_time', '<=', Time::toDate($beginTime,'H:i'))
                                         ->where('end_time', '>=', Time::toDate($endTime, 'H:i'))
                                         ->where('staff_id', $check->staff_id)
                                         ->where('week', Time::toDate($beginTime, 'w'))
                                         ->count();
        if ($check_stime < 1) {
            $result['code'] = 80007; //时间段不在服务人员服务时间段内
            return $result;
        }

        $check_appoint = StaffAppoint::where('appoint_time', '>=' ,$beginTime)
                                        ->where('appoint_time', '<=', $endTime)
                                        ->where(function($query) use ($id) {
                                                $query->whereNotIn('order_id', [0,$id])
                                                      ->orWhere('is_leave', 1);
                                        })->where('staff_id', $check->seller_staff_id)
                                        ->first();
        if ($check_appoint) {
            $result['code'] = 80006; //此段时间是否有预约或者请假
            return $result;
        }
        DB::beginTransaction();
        try {
            //更改订单时间
            Order::where('id', $id)->update([
                'appoint_time' => $beginTime,
                'service_end_time' => $endTime,
                'appoint_hour' => Time::toDate($beginTime,'H'),
                'appoint_day' => Time::toDayTime($beginTime),
                'service_end_hour' => Time::toDate($endTime,'H'),
                'service_end_day' => Time::toDayTime($endTime),
                'goods_duration' => ($endTime - $beginTime)
            ]);
            //更新预约时间表
            StaffAppoint::where('staff_id', $check->seller_staff_id)->where('order_id',$id)->update(['order_id' => 0,'status' => 0]);
            $btime = $beginTime % 1800 == 0 ? $beginTime : ceil($beginTime/1800) * 1800;
            $etime = $endTime % 1800 == 0 ? $endTime : ceil($endTime/1800) * 1800;
            $appoint_data = [];
            for ($i = $btime; $i < $etime; $i += 1800) {
                $appoint = StaffAppoint::where('appoint_time',$i)
                    ->where('staff_id', $check->seller_staff_id)
                    ->first();
                if ($appoint) {
                    StaffAppoint::where('staff_id',$check->seller_staff_id)
                        ->where('appoint_time', $i)
                        ->update([
                            'appoint_week' => Time::toDate($i,'w'),
                            'order_id' => $id,
                            'status' => 1
                        ]);
                } else {
                    $appoint_data[] = [
                        'staff_id' => $check->seller_staff_id,
                        'seller_id' => $check->seller_id,
                        'appoint_time' => $i,
                        'appoint_day' => Time::toDayTime($i),
                        'appoint_week' => Time::toDate($i,'w'),
                        'order_id' => $id,
                        'status' => 1
                    ];

                }
            }
            if (count($appoint_data) > 0) {
                StaffAppoint::insert($appoint_data);
            }
            DB::commit();
            return $result;
        } catch(Exception $e) {
            $result['code'] = 80004;
            DB::rollback();
            return $result;
        }

    }

    /**
     * 指定指派人员
     * @param array $orderIds 订单编号数组
     * @param int $staffId 员工编号
     */
    public static function updateStaff($orderIds, $staffId) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg'  => Lang::get('api_system.success.update_info')
        ];
        //允许更改的订单状态
        $allow_status = [ORDER_STATUS_WAIT_PAY,ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_AFFIRM_SERVICE,ORDER_STATUS_STAFF_ACCEPT];

        $order_count = Order::whereIn('id', $orderIds)->whereIn('status', $allow_status)->count();
        if ($order_count != count($orderIds) || $order_count < 1) {
            $result['code'] = 80101; //订单编号不合法
            return $result;
        }
        $check_staff = SellerStaff::where('id', $staffId)->where('status', 1)->first();
        if (!$check_staff) {
            $result['code'] = 80102; // 服务人员不存在
            return  $result;
        }

        $order_list = Order::whereIn('id', $orderIds)->whereIn('status', $allow_status)->select('id', 'appoint_time','service_end_time')->get()->toArray();
        $appoint = StaffAppoint::where('staff_id', $staffId)->where('order_id',0)->where('is_leave',0);
        $service = StaffServiceTime::where('staff_id', $staffId);
        foreach ($order_list as $key=>$val) {
            $btime = $val['appointTime'];
            $etime = $val['serviceEndTime'];
            $week = Time::toDate($val['appointTime'], 'w');
            $bhour = Time::toDate($val['appointTime'], 'H:i');
            $ehour = Time::toDate($val['serviceEndTime'], 'H:i');
            $appoint->where(function($query) use ($btime, $etime){
                $query->where('appoint_time', '>=', $btime)
                    ->where('appoint_time', '<=', $etime);
            });

            $service->where(function($query) use ($week,$bhour, $ehour){
                $query->where('appoint_time', '>=', $bhour)
                    ->where('appoint_time', '<=', $ehour)
                    ->where('week', $week);
            });
        }
        $appoint_count = $appoint->count();
        $service_count = $service->count();
        if ($appoint_count > 0 && $service_count < 1) {
            $result['code'] = 80103; //不在服务人员服务时间内
            return $result;
        }

        DB::beginTransaction();
        try {
            //更新订单表
            Order::whereIn('id', $orderIds)->update([
                'seller_staff_id' => $staffId,
                'seller_id' => $check_staff->selle_id,
                'designate_type' => 1
            ]);

            //更新预约表
            $appoint_data = [];
            foreach ($order_list as $k=>$v) {
                $btime = $val['appointTime'] % 1800 == 0 ? $val['appointTime'] : ceil($val['appointTime']/1800) * 1800;
                $etime = $val['serviceEndTime'] % 1800 == 0 ? $val['serviceEndTime'] : ceil($val['serviceEndTime']/1800) * 1800;
                for ($i = $btime; $i < $etime; $i += 1800) {
                    $appoint = StaffAppoint::where('appoint_time',$i)
                        ->where('staff_id', $staffId)
                        ->first();
                    if ($appoint) {
                        StaffAppoint::where('staff_id',$staffId)
                            ->where('appoint_time', $i)
                            ->update([
                                'appoint_week' => Time::toDate($i,'w'),
                                'order_id' => $v['id'],
                                'status' => 1
                            ]);
                    } else {
                        $appoint_data[] = [
                            'staff_id' => $staffId,
                            'seller_id' => $check_staff->seller_id,
                            'appoint_time' => $i,
                            'appoint_day' => Time::toDayTime($i),
                            'appoint_week' => Time::toDate($i,'w'),
                            'order_id' => $v['id'],
                            'status' => 1
                        ];

                    }
                }
            }
            if (count($appoint_data) > 0) {
                StaffAppoint::insert($appoint_data);
            }
            DB::commit();
            return $result;
        } catch(Exception $e) {
            $result['code'] = 80104;
            DB::rollback();
            return $result;
        }
    }

    /**
     * 指定指派人员
     * @param array $orderIds 订单编号数组
     * @param int $staffId 员工编号
     */
    public static function designate($orderId, $staffId,$serviceContent,$money) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg'  => Lang::get('api_system.success.update_info')
        ];
        
        //允许更改的订单状态
        $allow_status = [ORDER_STATUS_BEGIN_SERVICE,ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_AFFIRM_SERVICE,ORDER_PAY_STATUS_YES];
        $order_list = Order::where('id', $orderId)->whereIn('status', $allow_status)->first();
        if (!$order_list) {
            $result['code'] = 80107; // 不能指派
            return  $result;
        }
        if($order_list->order_type < 2){
            $rules = array(
                'orderId'	      => ['required'],
                'staffId'	      => ['required'],
            );
            
            $messages = array(
                'orderId.required'	=> '8010911',
                'staffId.required'	=> '8010912',
            );
            $validator = Validator::make([
                'orderId'     =>$orderId,
                'staffId'     =>$staffId,
            ], $rules, $messages);
        }else{
            $rules = array(
                'orderId'	      => ['required'],
                'staffId'	      => ['required'],
                'serviceContent'  => ['required'],
                'money'	          => ['required','regex:/^-?\d+$/'],
            );
            
            $messages = array(
                'orderId.required'	=> '8010911',
                'staffId.required'	=> '8010912',
                'serviceContent.required'	 => '80108',
                'money.required'	         => '80109',
                'money.regex'	             => '80113',
            );
            $validator = Validator::make([
                'orderId'     =>$orderId,
                'staffId'     =>$staffId,
                'serviceContent'=>$serviceContent,
                'money'=>$money,
            ], $rules, $messages);
        }
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }  
        $check_staff = SellerStaff::where('id', $staffId)->where('status', 1)->first();
        if (!$check_staff) {
            $result['code'] = 80106; // 服务人员不存在
            return  $result;
        }
        DB::beginTransaction();
        try {
          
            if($order_list->order_type < 2){
                //更新订单表
                Order::where('id', $orderId)->update([
                    'seller_staff_id' => $staffId,
                    'status'    => ORDER_STATUS_ASSIGN_SERVICE,
                    'seller_confirm_time' => UTC_TIME,
                ]);
            }else{               
                //更新订单表
                Order::where('id', $orderId)->update([
                    'seller_staff_id' => $staffId,
                    'seller_id' => $check_staff->seller->id,
                    'status'    => ORDER_STATUS_ASSIGN_SERVICE,
                    'total_fee' =>$money,
                    'service_content'=>$serviceContent,
                    'seller_confirm_time' => UTC_TIME,
                ]);
            }
            $result['code'] = 80000;
            DB::commit();
        } catch(Exception $e) {
            $result['code'] = 80104;
            DB::rollback();
        }
        return $result;
    }
    
    /**
     * 随机指派人员
     * @param $orderId 订单编号数组
     */
    public static function ranUpdate($orderId,$serviceContent,$money) {
     $result = [
            'code' => 0,
            'data' => null,
            'msg'  => Lang::get('api_system.success.update_info')
        ];
        
        //允许更改的订单状态
        $allow_status = [ORDER_STATUS_BEGIN_SERVICE,ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_AFFIRM_SERVICE,ORDER_PAY_STATUS_YES];
        $order_list = Order::where('id', $orderId)->whereIn('status', $allow_status)->first();
        if (!$order_list) {
            $result['code'] = ORDER_NOT_EXIST; // 订单不存在
            return  $result;
        }
        if (!$order_list) {
            $result['code'] = 80107; // 不能指派
            return  $result;
        }
        if($order_list->order_type < 2){
            $rules = array(
                'orderId'	      => ['required'],
            );
            
            $messages = array(
                'orderId.required'	=> '8010911',
            );
            $validator = Validator::make([
                'orderId'     =>$orderId,
            ], $rules, $messages);
        }else{
            $rules = array(
                'orderId'	      => ['required'],
                'serviceContent'  => ['required'],
                'money'	          => ['required','regex:/^-?\d+$/'],
            );
            
            $messages = array(
                'orderId.required'	=> '8010911',
                'serviceContent.required'	 => '80108',
                'money.required'	         => '80109',
                'money.regex'	             => '80113',
            );
            $validator = Validator::make([
                'orderId'     =>$orderId,
                'serviceContent'=>$serviceContent,
                'money'=>$money,
            ], $rules, $messages);
        }
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }  
        
        $check_staff = SellerStaff::where('status',1)->get()->toArray();
        $staff = $check_staff[array_rand($check_staff,1)];
        if (!$staff) {
            $result['code'] = 80106; // 服务人员不存在
            return  $result;
        }
        DB::beginTransaction();
        try {
        
            if($order_list->order_type < 2){
                //更新订单表
                Order::where('id', $orderId)->update([
                    'seller_staff_id' => $staff['id'],
                    'seller_id' =>$staff['seller_id'],
                    'status'    => ORDER_STATUS_ASSIGN_SERVICE,
                    'seller_confirm_time' => UTC_TIME,
                ]);
            }else{
                //更新订单表
                Order::where('id', $orderId)->update([
                    'seller_staff_id' =>$staff['id'],
                    'seller_id' => $staff['seller_id'],
                    'status'    => ORDER_STATUS_ASSIGN_SERVICE,
                    'total_fee' =>$money,
                    'service_content'=>$serviceContent,
                    'seller_confirm_time' => UTC_TIME,
                ]);
            }
            $result['code'] = 80000;
            DB::commit();
        } catch(Exception $e) {
            $result['code'] = 80104;
            DB::rollback();
        }
        return $result;
    }
    /**
     * 随机指派人员
     * @param int $id 请假记录编号
     * @param array $orderIds 订单编号数组
     */
    public static function ranUpdateStaff($id, $orderIds) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg'  => Lang::get('api_system.success.update_info')
        ];

        $staff_id = StaffLeave::where('id', $id)->pluck('staff_id');
        if ((int)$staff_id < 1) {
            $result['code'] = 50601; //请假记录不存在
            return $result;
        }
        //允许更改的订单状态
        $allow_status = [ORDER_STATUS_WAIT_PAY,ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_AFFIRM_SERVICE,ORDER_STATUS_STAFF_ACCEPT];
        $order_count = Order::whereIn('id', $orderIds)->whereIn('status', $allow_status)->count();
        if ($order_count != count($orderIds) || $order_count < 1) {
            $result['code'] = 80101; //订单编号不合法
            return $result;
        }

        $order_list = Order::whereIn('id', $orderIds)->whereIn('status', $allow_status)->select('id','appoint_time','service_end_time')->get()->toArray();

        $com_sql = 'select staff_id from (';

        foreach ($order_list as $key => $val) {

            $query = 'select staff_id from '.env('DB_PREFIX').'staff_service_time where staff_id not in(';

            $query .= 'select staff_id from '.env('DB_PREFIX').'staff_appoint where appoint_time >= ' . $val['appointTime'] . ' and appoint_time <= '. $val['serviceEndTime'] . ' and (order_id != 0 or is_leave = 1) and appoint_week = '.Time::toDate($val['appointTime'], 'w');

            $query .= ') and week = ' . Time::toDate($val['appointTime'], 'w') . ' and begin_time <= "' . Time::toDate($val['appointTime'], 'H:i') . '" and end_time >= "' . Time::toDate($val['serviceEndTime'], 'H:i') . '" and staff_id != '. $staff_id;

            if ($key == 0) {
                $com_sql .= $query;
            } else {
                $com_sql .= ' UNION ALL '.$query;
            }
        }

        $com_sql .= ') as stids group by staff_id HAVING COUNT(staff_id) = '.$order_count;

        $staff_ids = DB::select($com_sql);

        if (count($staff_ids) < 1) {
            $result['code'] = 80105;
            return $result;
        } else {
            foreach ($staff_ids as $key=>$val) {
                $staff_ids[$key] = $val->staff_id;
            }
            $staffId = $staff_ids[rand(0,count($staff_ids)-1)];
            $staff = SellerStaff::where('id', $staffId)->first();
            DB::beginTransaction();
            try {
                //更新订单表
                Order::whereIn('id', $orderIds)->update([
                    'seller_staff_id' => $staffId,
                    'seller_id' => $staff->selle_id,
                    'designate_type' => 1
                ]);

                //更新预约表
                $appoint_data = [];
                foreach ($order_list as $k=>$v) {
                    $btime = $val['appointTime'] % 1800 == 0 ? $val['appointTime'] : ceil($val['appointTime']/1800) * 1800;
                    $etime = $val['serviceEndTime'] % 1800 == 0 ? $val['serviceEndTime'] : ceil($val['serviceEndTime']/1800) * 1800;
                    for ($i = $btime; $i < $etime; $i += 1800) {
                        $appoint = StaffAppoint::where('appoint_time',$i)
                            ->where('staff_id', $staffId)
                            ->first();
                        if ($appoint) {
                            StaffAppoint::where('staff_id',$staffId)
                                ->where('appoint_time', $i)
                                ->update([
                                    'appoint_week' => Time::toDate($i,'w'),
                                    'order_id' => $v['id'],
                                    'status' => 1
                                ]);
                        } else {
                            $appoint_data[] = [
                                'staff_id' => $staffId,
                                'seller_id' => $staff->selle_id,
                                'appoint_time' => $i,
                                'appoint_day' => Time::toDayTime($i),
                                'appoint_week' => Time::toDate($i,'w'),
                                'order_id' => $v['id'],
                                'status' => 1
                            ];

                        }
                    }
                }
                if (count($appoint_data) > 0) {
                    StaffAppoint::insert($appoint_data);
                }
                $result['msg'] = '已指派给'.$staff->name;
                DB::commit();
                return $result;
            } catch(Exception $e) {
                $result['code'] = 80104;
                DB::rollback();
                return $result;
            }
        } 
    }


    /**
     * 退款处理
     * @param int $adminId 操作管理员编号
     * @param int $id 订单编号
     * @param int $status 退款状态
     * @param string $remark 退款备注
     */
    public static function refund($adminId, $id, $status, $remark) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg'  => Lang::get('api_system.success.handle')
        ];

        $check = Order::where('id', $id)->first();
        if (!$check) {
            $result['code'] = '50101';
            return $result;
        }
        //需要修改的订单状态及订单当前状态是否正确
        if ($status != ORDER_REFUND_ADMIN_AGREE &&
            $status != ORDER_REFUND_ADMIN_REFUSE &&
            $check->status != ORDER_STATUS_REFUND_AUDITING &&
            $check->status != ORDER_REFUND_SELLER_AGREE &&
            $check->pay_status != 1 && $check->pay_fee <= 0)
        {
            $result['code'] = '50103';
            return $result;
        }

        //当为拒绝退款时,必须输入备注
        if ($status == ORDER_REFUND_ADMIN_REFUSE && $remark == '') {
            $result['code'] = '50102';
            return $result;
        }

        $res = Order::where('id', $id)->update([
            'status' => $status,
            'dispose_refund_admin' => $adminId,
            'dispose_refund_time' => UTC_TIME,
            'dispose_refund_remark' => $remark
        ]);
        
        if($status == ORDER_REFUND_ADMIN_AGREE &&
            $check->pay_fee >= 0.0001 && 
            $check->pay_status == ORDER_PAY_STATUS_YES)
        {
            $userPayLog = DB::table('user_pay_log')
                ->where("order_id", $check->id)
                ->where("status", 1)
                ->frist();
            
            if($userPayLog != null)
            {
                DB::table('refund')->insert(
                    [
                        "sn"=>$check->sn,
                        "user_id"=>$check->user_id,
                        "order_id"=>$check->id,
                        "trade_no"=>$userPayLog->trade_no,
                        "seller_id"=>$check->seller_id,
                        "payment_type"=>$userPayLog->payment_type,
                        "money"=>$check->pay_fee,
                        "content"=>"申请退款",
                        "create_time"=>UTC_TIME,
                        "create_day"=>UTC_DAY,
                        "status"=>0
                    ]);

                SellerMoneyLogService::createLog(
                    $check->seller_id,
                    SellerMoneyLog::TYPE_ORDER_REFUND,
                    $check->id,
                    $check->pay_fee,
                    '订单退款：'.$check->sn
                );
            }
        }
        

        //操作失败
        $result = [
            'code' => '-1',
            'data' => null,
            'msg'  => Lang::get('api_system.error.handle')
        ];

        return  $result;
    }
}
