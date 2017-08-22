<?php 
namespace YiZan\Services\Staff;

use YiZan\Models\GoodsStaff;
use YiZan\Services\SystemConfigService;
use YiZan\Models\Staff\Order;
use YiZan\Models\SellerStaff;
use YiZan\Models\Goods;
use YiZan\Models\PromotionSn;
use YiZan\Models\SellerExtend;
use YiZan\Models\UserPayLog;
use YiZan\Models\SellerMoneyLog;
use YiZan\Services\PushMessageService;

use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use Exception, DB, Lang, Validator, App;

class  OrderService extends \YiZan\Services\OrderService
{

    /**
     * 订单列表
     * @param int $sellerId 商家编号
     * @param int $staffId 员工编号
     * @param int $status 订单状态 1:新订单 2:进行中 3:已完成 4:已取消
     * @param string $date 日期(格式 20151028)
     * @param string $keywords 搜索关键字
     * @param int $page 页码
     */
    public static function getList($sellerId, $staffId, $status, $date, $keywords, $page) {
        self::endOrder();
        $data = [
            'count' => 0,
            'ingCount' => 0,
            'amount' => 0,
            'orders' => []
        ];
        //新订单状态
        $newStatus = [
            ORDER_STATUS_BEGIN_USER,
            ORDER_STATUS_PAY_SUCCESS,
            ORDER_STATUS_PAY_DELIVERY,
            ORDER_STATUS_AFFIRM_SELLER
        ];
        //配送/服务中的订单状态
        $ingStatus = [
			ORDER_STATUS_START_SERVICE,
            ORDER_STATUS_PAY_SUCCESS,
            ORDER_STATUS_PAY_DELIVERY,
            ORDER_STATUS_AFFIRM_SELLER
        ];

        $list = Order::orderBy('id', 'desc');
        if ($sellerId > 0) {
            $list->where('seller_id', $sellerId);
            if ($status == 1) {
                $newStatus = [
                    ORDER_STATUS_BEGIN_USER,
                    ORDER_STATUS_PAY_SUCCESS,
                    ORDER_STATUS_PAY_DELIVERY
                ];
            }
        } elseif ($staffId > 0) {
            $list->where('seller_staff_id', $staffId);
        }

        //日期搜索
        if ($date != '') {
            $beginTime = Time::toTime($date);
            $endTime = $beginTime + 24 * 3600 - 1;
            $list->whereBetween('create_time', [$beginTime, $endTime]);
        }
        //关键字搜索
        if ($keywords != '') {
            $list->where(function($query) use ($keywords){
                $name = String::strToUnicode($keywords,'+');
                $query->orWhere('sn', $keywords)
                    ->orWhereRaw('MATCH(name_match) AGAINST(\'' . $name . '\' IN BOOLEAN MODE)')
                    ->orWhere('mobile', $keywords)
                    ->orWhereIn('id', function($child) use ($keywords){
                        $child->select('order_id')
                            ->from('order_goods')
                            ->where('goods_name', 'like', '%'.$keywords.'%');
                    });
            });
        }

        $ingObj = clone $list;
        $countObj = clone $list;
        //$cancelObj = clone $list;

        $data['ingCount'] = $ingObj->whereIn('status', $ingStatus)->count();
        $data['count'] = $countObj->whereNotIn('status', [
            ORDER_STATUS_SELLER_DELETE,
            ORDER_STATUS_ADMIN_DELETE
        ])->count();
        //$data['finishCount'] = $finishObj->whereIn('status', $finishStatus)->count();
        //$data['cancelCount'] = $cancelObj->whereIn('status', $cancelStatus)->count();

        if ($status == 1) {
            $list->whereIn('status', $ingStatus);
        } elseif ($status == 2) {
            $list->whereIn('status', $newStatus);
            $data['count'] = $list->count();
        } else {
            $list->whereNotIn('status', [
                ORDER_STATUS_SELLER_DELETE,
                ORDER_STATUS_ADMIN_DELETE
            ]);
        } /*elseif ($status == 4) {
            $list->whereIn('status', $cancelStatus);
        }*/


        //$data['count'] = $list->count();
        $data['amount'] = $list->sum('total_fee');
        $list = $list->with('seller','orderGoods')->skip(($page - 1) * 8)->take(8)->get()->toArray();

        foreach ($list as $key => $val) {
            $data['orders'][$key] = [
                'id' => $val['id'],
                'sn' => $val['sn'],
                'orderStatusStr' => $val['orderStatusStr'],
                'totalFee' => $val['totalFee'],
                'sellerFee' => $val['sellerFee'],
                'payFee' => $val['payFee'],
                'discountFee' => $val['discountFee'],
                'drawnFee' => $val['drawnFee'],
                'freight' => $val['freight'],
                'createTime' => $val['createTime'],
                'isFinished' => $val['isFinished'],
                'shopName' => $val['seller']['name'],
                'payStatusStr' => $val['payStatusStr'],
            ];
            foreach($val['orderGoods'] as $goods){
                $data['orders'][$key]['images'][] = $goods['goodsImages'];
                $data['orders'][$key]['num'] += $goods['num'];
            }
        }
        return  $data;
    }
    /**
     * 订单列表(old)
     * @param  int $staffId 卖家
     * @param  int $status 订单状态
     * @param  int $page 页码
     * @return array          订单列表
     */
    /*public static function getStaffList($staffId, $page, $pageSize = 20)
    {
        $list = Order::orderBy('status', 'desc')
              ->where('seller_staff_id', $staffId)
              ->with('cartSellers')
              ->where("status",ORDER_STATUS_AFFIRM_SELLER)
              ->skip(($page - 1) * $pageSize)
              ->take($pageSize)
              ->get()
              ->toArray();
        $data =[];
        foreach ( $list as $key => $val){
          $data[$key]['id'] =  $val['id'];
          $data[$key]['serviceName'] =  $val['cartSellers'][0]['goodsName'];
          $data[$key]['address'] =  $val['address'];
          $data[$key]['orderStatusStr'] =  $val['orderStatusStr'];
          $data[$key]['price'] = $val['totalFee'];
          $data[$key]['appTime'] =  $val['appTime'];
          $data[$key]['duration'] = $val['duration'];
          $data[$key]['name'] =  $val['name'];
          $data[$key]['mobile'] = $val['mobile'];
          $data[$key]['mapPoint'] = $val['mapPoint'];
          $data[$key]['isCanFinishService'] = $val['isCanFinishService'];
        }
        return $data;
    }*/

    /**
     * 获取订单详情
     * @param int $sellerId 商家编号
     * @param int $staffId 服务人员编号
     * @param int $orderId 订单编号
     * @return array
     */
    public static function getOrderById($sellerId, $staffId, $orderId)
    {
        self::endOrder();
      if ($sellerId > 0) {
          $data = Order::where('id', $orderId)->where('seller_id', $sellerId)->with('orderGoods', 'seller','staff')->first();
      } elseif($staffId > 0) {
          $data = Order::where('id', $orderId)->where('seller_staff_id', $staffId)->with('orderGoods', 'seller','staff')->first();
      }
      if ($data) {
          $data = $data->toArray();
          $data['distance'] = self::getdistance($data['mapPoint'], $data['seller']['mapPoint']);
          $data['sellerName'] =  $data['seller']['name'];
//          $data['isCanChangeStaff'] = $staffId > 0 ? false : $data['isCanChangeStaff'];
//          $data['isCanCancel'] = $staffId > 0 ? false :$data['isCanCancel'];
          unset($data['seller']);
      }
      return $data;
    }


    /**
     *求两个已知经纬度之间的距离,单位为千米
     *@return int 距离，单位千米
     **/
    public static function getdistance($mapPointBegin,$mapPointEnd){
        //将角度转为狐度
        $radLat1 = deg2rad($mapPointBegin['y']);//deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($mapPointEnd['y']);
        $radLng1 = deg2rad($mapPointBegin['x']);
        $radLng2 = deg2rad($mapPointEnd['x']);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s= 2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137*1000;
        return round($s/1000);
    }
    /**
     * 订单状态改变
     * @param int $sellerId 商家编号
     * @param int $staffId 员工编号
     * @param int $id 订单编号
     * @param int $status 订单更改状态 1:取消订单 2:确认订单 3:订单完成
     * @param string $remark 取消订单备注
     */
    public static function updateOrder($sellerId, $staffId, $id, $status, $remark){
        $result = [
                'code'  => 0,
                'data'  => null,
                'msg'   => Lang::get('api.success.update_info')
            ];

        if ($sellerId == 0 && $staffId > 0) {
            if ($status == 1 || $status == 2) {
                $result['code'] = 99995;
                return $result;
            }
            $order = Order::where('id', $id)->where('seller_staff_id', $staffId)->with('user', 'seller', 'orderGoods', 'staff')->first();
        } else {
            $order = Order::where('id', $id)->where('seller_id', $sellerId)->with('user', 'seller', 'orderGoods', 'staff')->first();
        }
        $updateStatus = [
            '1' => ORDER_STATUS_CANCEL_SELLER,
            '2' => ORDER_STATUS_AFFIRM_SELLER,
            '3' => ORDER_STATUS_FINISH_STAFF,
            '4' => ORDER_STATUS_START_SERVICE
        ];

        //没有订单
        if ($order == false)
        {
            $result['code'] = 20001; // 没有找到相关订单
            return $result;
        }

        if (!in_array($status, array_keys($updateStatus))) {
            $result['code'] = 20002;
            return $result;
        }

        if ($status == 1) {
            //当为取消订单的时候,订单状态不合法
            if (!$order->isCanCancel) {
                $result['code'] = 20002;
                return $result;
            }
           /* if ($remark == '') {
                $result['code'] = 20008;
                return $result;
            }*/
            $data = [
                'status' => $updateStatus[$status],
                'cancel_time' => UTC_TIME,
                'cancel_remark' => $remark
            ];
            //取消订单且为已支付的时候,退款
            /*if( $order->pay_status == ORDER_PAY_STATUS_YES && $order->pay_fee >= 0.0001 && $order->isCashOnDelivery === false){
                $data['status'] = ORDER_STATUS_CANCEL_REFUNDING;
                $data['refund_time'] = UTC_TIME;
            }*/

        }

        if ($status == 2) {
            //当为接单的时候,订单状态不合法
            if (!$order->isCanAccept) {
                $result['code'] = 20002;
                return $result;
            }

            $data = [
                'status' => $updateStatus[$status],
                'seller_confirm_time' => UTC_TIME,
            ];

        }

        if ($status == 3) {
            //当为完成订单的时候,订单状态不合法
            if (!$order->isCanFinish) {
                $result['code'] = 20002;
                return $result;
            }
            $autoFinishDay = SystemConfigService::getConfigByCode('system_buyer_order_confirm');
            $autoFinishTime = $autoFinishDay * 2 * 3600 + UTC_TIME;
            $data = [
                'status' => $updateStatus[$status],
                'staff_finish_time' => UTC_TIME,
                'auto_finish_time' => $autoFinishTime
            ];

        }
        
        if ($status == 4)
        {
            //当为开始服务的时候,订单状态不合法
            if (!$order->isCanStartService) {
                $result['code'] = 20002;
                return $result;
            }
            
            $data = [
                'status' => $updateStatus[$status]
            ];
        }
        
        $ble = Order::where('id', $id)->update($data);
        if ($ble) {
            $order = $order->toArray();
            if ($status == 1 || $status == 2) {

                //还原库存
                if ($status == 1) {
                    self::cancelOrderStock($id);
                    //如果是货到付款则退还商家支付的抽成金额
                    $sellerMoneyLog = SellerMoneyLog::where('related_id', $id)
                        ->where('type', SellerMoneyLog::TYPE_DELIVERY_MONEY)
                        ->first();
                    if($sellerMoneyLog){
                        $sellerMoneyLog->status = 2;
                        $sellerMoneyLog->save();
                        //增加商家金额
                        SellerExtend::where('seller_id', $order['sellerId'])
                            ->increment('money', $sellerMoneyLog->money);
                        //写入增加金额日志
                        \YiZan\Services\SellerMoneyLogService::createLog(
                            $order['sellerId'],
                            SellerMoneyLog::TYPE_DELIVERY_MONEY,
                            $id,
                            $order['drawnFee'],
                            '现金支付订单' . $order->sn . '取消，佣金返还',
                            1
                        );
                    }
                    if ((int)$order['promotionSnId']) {
                        PromotionSn::where('id', $order['promotionSnId'])->update(['use_time'=>0]);
                    }
                }

                //通知会员
                $noticeTpe = $updateStatus[$status] == ORDER_STATUS_AFFIRM_SELLER ? 'order.accept' : 'order.refund';
                $sound = $updateStatus[$status] == ORDER_STATUS_AFFIRM_SELLER ? 'music2.caf' : '';
                PushMessageService::notice($order['user']['id'],$order['user']['mobile'], $noticeTpe,$order,['sms','app'],'buyer',"3", $id, $sound);

                //当为取消且支付,退款金额大于0的时候写入退款日志
                if ($status == 1 &&
                    $order['payStatus'] == ORDER_PAY_STATUS_YES &&
                    $order['payFee'] >= 0.0001 &&
                    $order['isCashOnDelivery'] === false
                ){
                    $userPayLogs = UserPayLog::where('order_id', $order['id'])
                        ->where('pay_type', 1)
                        ->where('status', 1)
                        ->get()
                        ->toArray();

                    if (count($userPayLogs) > 0) {
                        $userRefundLog = [];
                        foreach($userPayLogs as $k=>$v) {
                            if ($v['paymentType'] == 'balancePay') {
                                $userRefundLog[] = [
                                    "sn" => $order['sn'],
                                    "user_id" => $order['userId'],
                                    "order_id" => $order['id'],
                                    "trade_no" => $v['tradeNo'],
                                    "seller_id" => $order['sellerId'],
                                    "payment_type" => $v['paymentType'],
                                    "money" => $v['money'],
                                    "content" => "商家取消",
                                    "create_time" => UTC_TIME,
                                    "create_day" => UTC_DAY,
                                    "status" => 1
                                ];
                            } else {
                                $userRefundLog[] = [
                                    "sn" => $order['sn'],
                                    "user_id" => $order['userId'],
                                    "order_id" => $order['id'],
                                    "trade_no" => $v['tradeNo'],
                                    "seller_id" => $order['sellerId'],
                                    "payment_type" => $v['paymentType'],
                                    "money" => $v['money'],
                                    "content" => "商家取消",
                                    "create_time" => UTC_TIME,
                                    "create_day" => UTC_DAY,
                                    "status" => 0
                                ];
                            }

                        }
                        DB::table('refund')->insert($userRefundLog);
                        
                        \YiZan\Services\SellerMoneyLogService::createLog(
                                $order['sellerId'],
                                \YiZan\Models\SellerMoneyLog::TYPE_ORDER_REFUND,
                                $id,
                                $order['payFee'],
                                '订单取消退款：'.$order->sn
                            );
                    }

                    \YiZan\Services\SellerService::decrementExtend($order['sellerId'], 'wait_confirm_money', $order['sellerFee']);
                }

            } else {
                PushMessageService::notice($order['user']['id'], $order['user']['mobile'], 'order.staff', $order, ['sms','app'], 'buyer', '3', $id , $updateStatus[$status] == ORDER_STATUS_START_SERVICE ? "music3.caf" : "");
            }
            $result['data'] = self::getOrderById($sellerId, $staffId, $id);
        } else {
            $result['code'] = 20002;
            return $result;
        }

        return  $result;
    }
    /**
     * 服务人员完成订单(old)
     * @param  int $id 订单id
     * @param  int $staffId 商家员工
     * @param  int $status 状态
     * @return array   更新结果
     */
    /*public static function updateStaffOrder($id, $staffId, $status)
    {
        $result = 
        [
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.update_info')
        ];
        
        $order = Order::where('id', $id)->where('seller_staff_id', $staffId)->with('user')->first();
        
        //没有订单
        if ($order == false) 
        {
            $result['code'] = 20001; // 没有找到相关订单
            
            return $result;
        }

        if($order->status != ORDER_STATUS_AFFIRM_SELLER && $order->status == ORDER_STATUS_FINISH_STAFF)
        {
            $result['code'] = 20002; // 订单状态不合法
            
            return $result;
        }
        
        if($status == ORDER_STATUS_FINISH_STAFF)
        {
            $autoFinishDay = SystemConfigService::getConfigByCode('system_buyer_order_confirm');
            $autoFinishTime = $autoFinishDay * 24 * 3600 + UTC_TIME;
            Order::where('id', $id)
                ->where('seller_staff_id', $staffId)
                ->update([
                    'status' => $status,
                    'staff_finish_time' => UTC_TIME,
                    'auto_finish_time' => $autoFinishTime
                ]);
            $result["data"] = self::getStaffOrderById($staffId,$id);
            $ble = true;
        }else{
            $ble = false;
            $result['code'] = 20002; // 订单状态不合法
        }        
        if($ble == true){
            //通知服务人员
            $order = $order->toArray();
            PushMessageService::notice($order['userId'], $order['user']['mobile'], 'order.staff', $order, ['sms','app'], 'buyer', '3', $id);
        }
        return $result;
    }*/
    
    /**
     * 服务人员完成订单
     * @param  [type] $userId  [description]
     * @param  [type] $orderId [description]
     * @return [type]          [description]
     */
    public static function completeOrder($staffId, $orderId,$reservationCode) {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.user_confirm_order')
        );
        $order = Order::where('id', $orderId)->where('seller_staff_id', $staffId)->first();
        if (!$order) {//没有订单
            $result['code'] = 60014;
            return $result;
        }
        if($order->reservation_code != $reservationCode){
            $result['code'] = 60115; //预约码不正确
            return $result;
        }       
        //当订单状态为 会员确认,订单已经确认过
        if ($order->status == ORDER_USER_CONFIRM_SERVICE)
        {
            $result['code'] = 60022;
            return $result;
        }
    
       //当订单状态不为 服务完成,订单不能确认
        if ($order->status != ORDER_STATUS_AFFIRM_SERVICE &&
            $order->status != ORDER_STATUS_AFFIRM_ASSIGN_SERVICE &&
            $order->status != ORDER_STATUS_ASSIGN_SERVICE) 
        {
            $result['code'] = 60021;
            return $result;
        }
    
        $order->buyer_confirm_time = UTC_TIME;
        $order->status       = ORDER_USER_CONFIRM_SERVICE;//会员确认

    
    
        DB::beginTransaction();
        try
        {
            $order->save();
//          //更新服务人员余额
//          SellerService::incrementExtend($order->seller_id, 'total_money', $order->seller_fee);
//          SellerService::incrementExtend($order->seller_id, 'money', $order->seller_fee);
//          //减少待到帐金额
//          SellerService::decrementExtend($order->seller_id, 'wait_confirm_money', $order->seller_fee);
    
//          //写入日志
//          SellerMoneyLogService::createLog(
//              $order->seller_id,
//              SellerMoneyLog::TYPE_ORDER_CONFIRM,
//              $orderId,
//              $order->seller_fee,
//              '完成订单:'.$order->sn
//          );
            DB::commit();
            $bln = true;
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
            $bln = false;
        }
    
        if ($bln) {
//           $order = $order->toArray();
//           $result['data'] = $order;
             $result["data"] = self::getOrderById(0, $staffId, $orderId);
//          try {
//              //通知服务人员
//              PushMessageService::notice($order['staff']['userId'], $order['staff']['mobile'], 'order.confirm', $order);
//          } catch (Exception $e) {
//          }
        }
        return $result;
    }


    /**
     * 订单列表
     * @param int $staffId 员工编号
     * @param int $type 订单类型 1:待完成订单 2:历史订单
     * @param int $page 历史订单页码 每页返回5天的数据
     */
    public static function getSchedule($staffId,$type, $page) {
        $lists = [];
        if ($type == 1) {
            //待完成订单状态
            $status = [
                ORDER_STATUS_PAY_SUCCESS,
                ORDER_STATUS_PAY_DELIVERY,
                ORDER_STATUS_AFFIRM_SELLER
            ];
            $data = Order::where('seller_staff_id', $staffId)
                    ->where('order_type', '2')
                    ->whereIn('status', $status)
                    ->with('orderGoods')
                    ->get()->toArray();
        } else {
            $appoint_days = Order::where('seller_staff_id', $staffId)
                            ->where('order_type', '2')
                            ->whereNotIn('status', [
                                ORDER_STATUS_USER_DELETE,
                                ORDER_STATUS_SELLER_DELETE,
                                ORDER_STATUS_ADMIN_DELETE
                            ])->groupBy('app_day')
                            ->orderBy('app_day', 'desc')
                            ->lists('app_day');
            if (count($appoint_days) >= ($page-1)*5) {
                $appoint_days = array_slice($appoint_days,($page-1)*5,5);
                $data = Order::where('seller_staff_id', $staffId)
                    ->where('order_type', '2')
                    ->whereNotIn('status', [
                        ORDER_STATUS_USER_DELETE,
                        ORDER_STATUS_SELLER_DELETE,
                        ORDER_STATUS_ADMIN_DELETE
                    ])->whereIn('app_day', $appoint_days)
                    ->with('orderGoods')->get()->toArray();
            }
        }

        if (count($data) > 0) {
            foreach ($data as $key=>$val) {
                //$isCanMap = $val['map_point'] == '' ? false : true;
                $day = Time::toDate($val['appDay'],'Ymd');
                $list[$day][] = [
                    'id' => $val['id'],
                    'serviceName' => $val['orderGoods'][0]['goodsName'],
                    'name' => $val['name'],
                    'address' => $val['address'],
                    'mobile' => $val['mobile'],
                    'duration' => $val['duration'],
                    'appTime' => Time::toDate($val['appTime'],'Y-m-d H:i:s'),
                    'isCanFinishService' => $val['isCanFinishService'],
                    'orderStatusStr' => $val['orderStatusStr'],
                    'price' => $val['totalFee'],
                    'mapPoint' => $val['map_point']
                ];
            }
            foreach ($list as $k => $v) {
                $lists[] = ['day' => $k, 'list' => $v];
            }


        }
        return $lists;
    }

    /**
     * 订单指派人员
     * @param int $sellerId 商家编号
     * @param int $id 订单编号
     * @param int $staffId 员工编号
     */
    public static function designate($sellerId, $id, $staffId) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg'  => Lang::get('api_staff.success.handle')
        ];

        //允许更改的订单状态
        $order_list = Order::where('id', $id)->where('seller_id', $sellerId)->with('orderGoods')->first();

        if (!$order_list) {
            $result['code'] = 20001;
            return $result;
        }
        $allow_status = [
            ORDER_STATUS_BEGIN_USER,
            ORDER_STATUS_PAY_SUCCESS,
            ORDER_STATUS_PAY_DELIVERY,
            ORDER_STATUS_AFFIRM_SELLER
        ];
        if (!in_array($order_list->status, $allow_status)) {
            $result['code'] = 20004; // 不能指派
            return  $result;
        }
        $check_staff = SellerStaff::where('id', $staffId)->where('status', 1)->where("seller_id", $sellerId)->first();
        if (!$check_staff) {
            $result['code'] = 20005; // 服务人员不存在
            return  $result;
        }
        // 既是服务人员又是配送人员
        if($check_staff->type != 0 && $check_staff->type != 3)
        {
            if ($order_list->order_type != $check_staff->type) {
                $result['code'] = 20006; //服务人员类型错误
                return $result;
            }
        }
        $order = $order_list->toArray();
        if ($order_list->order_type == 2) {
            $goodsIds = GoodsStaff::where('staff_id', $staffId)->lists('goods_id');
            if (!in_array($order['orderGoods'][0]['goods_id'], $goodsIds)) {
                $result['code'] = 20007; //不在服务人员服务范围内
                return $result;
            }
        }

        DB::beginTransaction();
        try {
            $staff = $check_staff->toArray();
            Order::where('id', $id)->update(['seller_staff_id' => $staffId]);
            PushMessageService::notice($staff['userId'], $staff['mobile'], 'order.designate', $order, ['sms','app'], 'staff', 3, $id);
            $result['data'] = self::getOrderById($sellerId, 0, $id);
            DB::commit();
        } catch(Exception $e) {
            $result['code'] = 99999;
            DB::rollback();
        }
        return $result;
    }

    /**
     * 商家经营分析
     * @param int $sellerId 商家编号
     * @param  int $days [类型 最近N天]
     */
    public static function businessStat($sellerId, $days) {
        if ($days > 1) {
            $end_time = UTC_DAY + 24 * 3600 - 1;
            $begin_time = $end_time - (($days - 1) * 24 * 3600);
        } else {
            $begin_time = UTC_DAY;
            $end_time = $begin_time + 24 * 3600 - 1;
        }
        $list = [];
        $status = array(
            ORDER_STATUS_PAY_SUCCESS,
            ORDER_STATUS_START_SERVICE,
            ORDER_STATUS_PAY_DELIVERY,
            ORDER_STATUS_AFFIRM_SELLER,
            ORDER_STATUS_FINISH_STAFF,
            ORDER_STATUS_FINISH_SYSTEM,
            ORDER_STATUS_FINISH_USER
            );
        $result = DB::table('order')->whereBetween('create_time', [$begin_time, $end_time])
                ->whereIn('status', $status)
                ->where('seller_id', $sellerId)
                ->groupBy('create_day')
                // ->select(DB::raw('count(id) as total,sum(seller_fee) as money'),'create_day')
                ->select(DB::raw('count(id) as total,sum(total_fee) as money'),'create_day')
                ->get();

        for ($i = 0; $i < $days; $i++) {
            $day = $begin_time + $i * 24 * 3600;
            $day_time = Time::toDate($day, 'd');
            $list[$day_time] = [
                'date' => Time::toDate($day, 'Y-m-d'),
                'num' => 0,
                'money' => 0
            ];
        }

        foreach ($result as $k=>$v) {
            $day = Time::toDate($v->create_day, 'd');
            $list[$day] = [
                'date' => Time::toDate($v->create_day, 'Y-m-d'),
                'num' => $v->total,
                'money' => $v->money
            ];
        }

        return $list;
    }
}