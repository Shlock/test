<?php

namespace YiZan\Services\Sellerweb;

use YiZan\Models\Sellerweb\Order;
use YiZan\Services\SystemConfigService;
use YiZan\Models\User;
use YiZan\Models\Seller;
use YiZan\Models\Goods;
use YiZan\Models\PromotionSn;
use YiZan\Models\UserPayLog;
use YiZan\Models\OrderPromotion;
use YiZan\Models\SellerStaff;
use YiZan\Models\StaffLeave;
use YiZan\Models\SellerExtend;
use YiZan\Services\System\PushMessageService;
use YiZan\Services\PromotionService;
use YiZan\Services\SellerMoneyLogService;
use YiZan\Models\SellerMoneyLog;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use Exception,
    DB,
    Lang,
    Validator,
    App;

class OrderService extends \YiZan\Services\OrderService {

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
     * @param  int $sellerId 卖家
     * @param  int $status 订单状态
     * @param  int $page 页码
     * @return array          订单列表
     */
    public static function getSellerList($sellerId, $sn, $orderType, $provinceId, $cityId, $areaId, $status, $beginTime, $endTime, $mobile, $staffName, $name, $page, $pageSize = 20) {
        self::endOrder();
        $list = Order::orderBy('id', 'desc');
        $list->where("seller_id", $sellerId)->with('staff');
        $list->where("status", '<>', ORDER_STATUS_SELLER_DELETE);
        $list->where("status", '<>', ORDER_STATUS_ADMIN_DELETE);

        if (!empty($orderType) > 0) {
            $list->where('order_type', $orderType);
        }
        if (!empty($sn)) {
            $list->where('sn', $sn);
        }
        if ($beginTime == true) {
            $list->where('create_time', '>=', $beginTime);
        }

        if ($endTime == true) {
            $list->where('create_time', '<=', $endTime);
        }
        if ($mobile != '') {
//             $list->where('mobile', $mobile);
            $list->where('mobile', 'like', '%' . $mobile . '%');
        }

        if ($staffName != '') {
            $staffName = empty($staffName) ? '' : String::strToUnicode($staffName, '+');
            $list->whereIn('seller_staff_id', function($query) use ($staffName) {
                $query->select('id')
                        ->from('seller_staff')
                        ->whereRaw("MATCH(name_match) AGAINST('{$staffName}' IN BOOLEAN MODE)");
            });
        }

        if ($name != '') {
            $list->where('name', 'like', '%' . $name . '%');
        }

        /* if(!empty($provinceId)){
          $list->whereIn('id',function($query) use ($sellerId)
          {
          $query->select('id')
          ->from('seller')
          ->where('province_id', $provinceId)
          ->where('city_id', $cityId)
          ->where('area_id', $areaId);
          });
          } */
        switch ($status) {
            case '1': //待发货  102 110
                $list->whereIn('status', [
                    ORDER_STATUS_BEGIN_USER,
                    ORDER_STATUS_PAY_SUCCESS,
                    ORDER_STATUS_PAY_DELIVERY,
                    ORDER_STATUS_AFFIRM_SELLER
                ]);
                break;
            case '2'://待完成 106 107
                $list->whereIn('status', [
                    ORDER_STATUS_START_SERVICE,
                    ORDER_STATUS_FINISH_STAFF
                ]);
                break;
            case '3':// 已完成
                $list->whereIn('status', [
                    ORDER_STATUS_FINISH_SYSTEM,
                    ORDER_STATUS_FINISH_USER,
                    ORDER_STATUS_USER_DELETE
                ])->where(function($query) {
                    $query->where('buyer_finish_time', '>', 0)
                            ->orWhereBetween('auto_finish_time', [1, UTC_TIME]);
                });
                break;
            case '4'://拒绝 301 302 303 400 401 402 403 404 311 312
                $list->whereIn('status', [
                    ORDER_STATUS_CANCEL_USER,
                    ORDER_STATUS_CANCEL_AUTO,
                    ORDER_STATUS_CANCEL_SELLER,
                    ORDER_STATUS_CANCEL_ADMIN
                ]);
                break;
            case '5'://退款
                $list->whereIn('status', [
                    ORDER_STATUS_REFUND_AUDITING,
                    ORDER_STATUS_REFUND_HANDLE,
                    ORDER_STATUS_REFUND_FAIL,
                    ORDER_STATUS_REFUND_SUCCESS
                ]);
                break;
            default:
                break;
        }
        $result['totalCount'] = $list->count();
        $result['list'] = $list->skip(($page - 1) * $pageSize)
                ->take($pageSize)
                ->get()
                ->toArray();
        return $result;
    }

    /**
     * 订单列表
     * @param  int $staffId 员工
     * @param  int $status 订单状态
     * @param  int $page 页码
     * @return array          订单列表
     */
    public static function getCarList($staffId, $status, $page, $pageSize = 20) {
        $list = Order::orderBy('id', 'desc');

        $list->where('seller_staff_id', $staffId);

        return $list->skip(($page - 1) * $pageSize)
                        ->take($pageSize)
                        ->with('seller', 'OrderGoods.goods', 'user.restaurant')
                        ->get()
                        ->toArray();
    }

    /**
     * 获取订单
     * @param  int $id 订单id
     * @return array   订单
     */
    public static function getSellerOrderById($id) {
        self::endOrder();
        $goods = Order::where('id', $id)->with('OrderGoods', 'user', 'staff')->first();
        return $goods;
    }

    /**
     * 获取订单
     * @param  int $sellerId 商家编号
     * @param  int $id 订单id
     * @return array   订单
     */
    public static function getSellerOrderDetail($sellerId, $id) {
        self::endOrder();
        $goods = Order::where('id', $id)
                        ->where('seller_id', $sellerId)
                        ->with('OrderGoods', 'user', 'staff')
                        ->first()->toArray();
        if (!empty($goods)) {
            $goods['staffList'] = [];
            $staff = SellerStaff::where('seller_id', $sellerId)
                    ->whereIn('type', [0, 3, $goods['orderType']])
                    ->where('status', 1)
                    ->whereNotIn('id', function($query) use ($sellerId) {
                $query->select('staff_id')
                ->from('staff_leave')
                ->where('begin_time', '<=', UTC_TIME)
                ->where('end_time', '>=', UTC_TIME)
                ->where('is_agree', 1)
                ->where('status', 1);
            });
            if ($goods['orderType'] == 2) {
                $goodsId = $goods['OrderGoods'][0]['goodsId'];
                $staff->whereIn('id', function($query) use ($goodsId) {
                    $query->select('staff_id')
                            ->from('goods_staff')
                            ->where('goods_id', $goodsId);
                });
            }
            $staff = $staff->get();
            if ($staff) {
                $goods['staffList'] = $staff->toArray();
            }
        }
        return $goods;
    }

    /**
     * 获取餐厅订单的详情
     * @param  int $id 订单id
     * @return array   订单
     */
    public static function getRestaurantOrderById($restaurantId, $id) {
        $order = Order::where('id', $id)->where("restaurant_id", $restaurantId)->first();
        if ($order->order_type >= 3) {
            $goods = Order::where('id', $id)->where("restaurant_id", $restaurantId)->with('goods.extend', 'user', 'restaurant')->first();
        } else {
            $goods = Order::where('id', $id)->where("restaurant_id", $restaurantId)->with('OrderGoods.goods', 'user', 'restaurant')->first();
        }
        return $goods;
    }

    /**
     * 更新订单
     * @param  int $id 订单id
     * @param  int $sellerId 商家
     * @param  int $status 状态
     * @param  int $content 内容
     * @return array   更新结果

      public static function updateSellerOrder($id, $sellerId, $status, $content = "")
      {
      $result =
      [
      'code'	=> 0,
      'data'	=> null,
      'msg'	=> Lang::get('api.success.update_info')
      ];

      if( $status != ORDER_STATUS_START_SERVICE &&
      $status != ORDER_STATUS_FINISH_SERVICE &&
      $status != ORDER_STATUS_REFUND_HANDLE &&
      $status != ORDER_USER_CANCEL_SERVICE &&
      $status != ORDER_STATUS_SELLER_ACCEPT &&
      $status != ORDER_STATUS_SELLER_REFUSE)
      {
      $result['code'] = self::ORDER_STATUS_ERROR; // 订单状态不合法

      return $result;
      }

      $order = Order::where('id', $id)->where('seller_id', $sellerId)->first();

      //没有订单
      if ($order == false)
      {
      $result['code'] = self::ORDER_NOT_EXIST; // 没有找到相关订单

      return $result;
      }

      if(($order->status != ORDER_STATUS_SELLER_ACCEPT ||
      $order->status != ORDER_STATUS_STAFF_ACCEPT ||
      $order->status != ORDER_STATUS_STAFF_SETOUT) &&
      $status == ORDER_STATUS_START_SERVICE)
      {
      $result['code'] = self::ORDER_STATUS_ERROR; // 订单状态不合法

      return $result;
      }

      if($order->status != ORDER_STATUS_START_SERVICE &&
      $status == ORDER_STATUS_FINISH_SERVICE)
      {
      $result['code'] = self::ORDER_STATUS_ERROR; // 订单状态不合法

      return $result;
      }

      if($status == ORDER_STATUS_START_SERVICE)
      {
      $order->status = ORDER_STATUS_START_SERVICE;

      $order->service_start_time = UTC_TIME;
      }
      else if ($status == ORDER_STATUS_FINISH_SERVICE)
      {
      $order->status = ORDER_STATUS_FINISH_SERVICE;

      $order->service_finish_time = UTC_TIME;
      }
      else
      {
      if( $order->getIsCanCancelAttribute() == false &&
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
      else if($status == ORDER_STATUS_SELLER_ACCEPT ||
      $status == ORDER_STATUS_SELLER_REFUSE)
      {
      $order->status = $status;

      $order->seller_confirm_time = UTC_TIME;

      if($status == ORDER_STATUS_SELLER_REFUSE){

      //有优惠券，则退回优惠券
      $return_promotion = PromotionService::returnPromotion($order);

      if(!$return_promotion){
      $result['code'] = 50115;
      return $result;
      }
      }
      }
      }

      $order->save();
      $data = self::getSellerOrderById($id)->toArray();

      //当状态为接受订单或者拒绝订单的时候,推送消息
      if ($status == ORDER_STATUS_SELLER_ACCEPT ||
      $status == ORDER_STATUS_SELLER_REFUSE) {
      $noticeTpe = $status == ORDER_STATUS_SELLER_ACCEPT ? 'order.accept' : 'order.refund';
      PushMessageService::notice($data['userId'],$data['mobile'], $noticeTpe,['sn' => $data['sn']],['sms','app'],'buyer',4,$id);
      }

      $result["data"] = $data;

      return $result;
      } */

    /**
     * 更新订单
     * @param  int $id 订单id
     * @param  int $status 状态
     * @param  string $content 处理结果
     * @return array   更新结果
     */
    public static function updateSellerOrder($id, $sellerId, $status, $refuseContent) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.update_info')
        ];
        if (
                $status == ORDER_STATUS_AFFIRM_SELLER &&
                $status == ORDER_STATUS_CANCEL_SELLER &&
                $status == ORDER_REFUND_SELLER_REFUSE &&
                $status == ORDER_REFUND_SELLER_AGREE
        ) {
            $result['code'] = self::ORDER_STATUS_ERROR;

            return $result;
        }
        $order = Order::where('id', $id)->where('seller_id', $sellerId)->first();

        //没有订单
        if ($order == false) {
            $result['code'] = self::ORDER_NOT_EXIST;

            return $result;
        }

        if ($order->isCanRefundSeller) {
            $result['code'] = self::ORDER_STATUS_ERROR;

            return $result;
        }

        if ($order->status == ORDER_REFUND_SELLER_AGREE || $order->status == ORDER_REFUND_SELLER_REFUSE) {
            $result['code'] = self::ORDER_STATUS_ERROR;

            return $result;
        }

        if ($status == ORDER_STATUS_CANCEL_SELLER) {
            if ($refuseContent == "") {
                $result['code'] = 50201;
                return $result;
            } else {
                if ($order->pay_fee != 0 && $order->isCashOnDelivery() === false && $order->pay_status == ORDER_PAY_STATUS_YES) {
                    $order->status = ORDER_STATUS_CANCEL_SELLER;
                    $order->refund_time = UTC_TIME;
                    $order->cancel_time = UTC_TIME;
                } else {
                    $order->status = ORDER_STATUS_CANCEL_SELLER;
                    $order->cancel_time = UTC_TIME;
                    $order->cancel_remark = $refuseContent;
                }
            }
        } else if ($status == ORDER_STATUS_AFFIRM_SELLER) {
            $order->status = ORDER_STATUS_AFFIRM_SELLER;
            $order->seller_confirm_time = UTC_TIME;
        } else if ($status == ORDER_REFUND_SELLER_AGREE) {
            $order->status = ORDER_REFUND_SELLER_AGREE;
            $order->dispose_refund_seller_time = UTC_TIME;
        } else if ($status == ORDER_REFUND_SELLER_REFUSE) {
            $order->status = ORDER_REFUND_SELLER_REFUSE;
            $order->dispose_refund_seller_time = UTC_TIME;
            $order->dispose_refund_seller_remark = $refuseContent;
        } else if ($status == ORDER_STATUS_START_SERVICE) {
            $order->status = ORDER_STATUS_START_SERVICE;
        } else if ($status == ORDER_STATUS_FINISH_STAFF) {
            $order->status = ORDER_STATUS_FINISH_STAFF;
            $order->staff_finish_time = UTC_TIME;
            $autoFinishDay = SystemConfigService::getConfigByCode('system_buyer_order_confirm');
            $autoFinishTime = $autoFinishDay * 2 * 3600 + UTC_TIME;
            $order->auto_finish_time = $autoFinishTime;
        } else {
            $result['code'] = self::ORDER_NOT_EXIST;
            return $result;
        }
        try {
            $order->save();
            $bl = true;
            DB::commit();
        } catch (Exception $e) {
            $bl = true;
            DB::rollback();
        }
        if ($bl) {
            $data = self::getSellerOrderById($id)->toArray();

            if ($status == ORDER_STATUS_CANCEL_SELLER) {
                self::cancelOrderStock($id);

                if ((int) $data['promotionSnId'] > 0) {
                    PromotionSn::where('id', $data['promotionSnId'])->update(['use_time' => 0]);
                }

                //如果是货到付款则退还商家支付的抽成金额
                if ($data['isCashOnDelivery'] === true) {
                    $sellerMoneyLog = SellerMoneyLog::where('related_id', $id)
                            ->where('type', SellerMoneyLog::TYPE_DELIVERY_MONEY)
                            ->first();
                    if ($sellerMoneyLog) {
                        //增加商家金额
                        SellerExtend::where('seller_id', $order->seller_id)
                                ->increment('money', abs($sellerMoneyLog->money));
                        //写入增加金额日志
                        SellerMoneyLogService::createLog(
                                $order->seller_id, SellerMoneyLog::TYPE_DELIVERY_MONEY, $id, $order->drawn_fee, '现金支付订单' . $order->sn . '取消，佣金返还', 1
                        );
                    }
                }
            }



            if (
                    $status == ORDER_STATUS_CANCEL_SELLER &&
                    $data['payFee'] >= 0.0001 &&
                    $data['payStatus'] == ORDER_PAY_STATUS_YES &&
                    $data['isCashOnDelivery'] === false
            ) {

                $pay_type = $order->getPayType();
                //返还支付金额给会员余额
                $user = User::find($order->user_id);
                $user->balance = $user->balance + abs($order->pay_money);
                $user->save();
                //创建退款日志
                $balancePayLog = new UserPayLog;
                $balancePayLog->payment_type = 'balancePay';
                $balancePayLog->pay_type = 3; //退款
                $balancePayLog->user_id = $order->user_id;
                $balancePayLog->order_id = $order->id;
                $balancePayLog->activity_id = $order->activity_id > 0 ? $order->activity_id : 0;
                $balancePayLog->seller_id = $order->seller_id;
                $balancePayLog->money = $order->pay_money;
                $balancePayLog->balance = $user->balance;
                $balancePayLog->content = '会员取消订单退款';
                $balancePayLog->create_time = UTC_TIME;
                $balancePayLog->create_day = UTC_DAY;
                $balancePayLog->status = 1;
                $balancePayLog->sn = Helper::getSn();
                $balancePayLog->save();
                //统一退款
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
                                "content" => "商家取消",
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
                                "content" => "商家取消",
                                "create_time" => UTC_TIME,
                                "create_day" => UTC_DAY,
                                "status" => 0
                            ];
                        }
                    }
                    DB::table('refund')->insert($userRefundLog);

                    SellerMoneyLogService::createLog(
                            $order->seller_id, SellerMoneyLog::TYPE_ORDER_REFUND, $id, $order->pay_fee, '订单取消，退款：' . $order->sn
                    );
                }

                //更新商家扩展表
                \YiZan\Services\SellerService::decrementExtend($order->seller_id, 'wait_confirm_money', $order->seller_fee);
            }
            //当状态为接受订单或者拒绝订单的时候,推送消息
            if ($status == ORDER_STATUS_CANCEL_SELLER || $status == ORDER_STATUS_AFFIRM_SELLER) {
                $noticeTpe = $status == ORDER_STATUS_AFFIRM_SELLER ? 'order.accept' : 'order.refund';
                PushMessageService::notice($data['user']['id'], $data['user']['mobile'], $noticeTpe, ['sn' => $data['sn']], ['sms', 'app'], 'buyer', '3', $id, $status == ORDER_STATUS_AFFIRM_SELLER ? "music2.caf" : "");
            }
        }

        return $result;
    }

    /**
     * 指定指派人员
     * @param array $orderIds 订单编号数组
     * @param int $staffId 员工编号
     */
    public static function designate($orderId, $staffId, $sellerId) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api_system.success.update_info')
        ];

        //允许更改的订单状态
        $order_list = Order::where('id', $orderId)->where('status', '<=', ORDER_STATUS_FINISH_STAFF)->first();
        if (!$order_list) {
            $result['code'] = 80107; // 不能指派
            return $result;
        }
        $rules = array(
            'orderId' => ['required'],
            'staffId' => ['required'],
        );
        $messages = array(
            'orderId.required' => '8010911',
            'staffId.required' => '8010912',
        );
        $validator = Validator::make([
                    'orderId' => $orderId,
                    'staffId' => $staffId,
                        ], $rules, $messages);

        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        $check_staff = SellerStaff::where('id', $staffId)->where('status', 1)->where("seller_id", $sellerId)->first();
        if (!$check_staff) {
            $result['code'] = 80102; // 服务人员不存在
            return $result;
        }

        if (!in_array($check_staff->type, ['0', '3', $order_list->order_type])) {
            $result['code'] = 80114; //服务人员类型错误
            return $result;
        }

        $check_leave = StaffLeave::where('staff_id', $staffId)
                ->where('begin_time', '<=', UTC_TIME)
                ->where('end_time', '>=', UTC_TIME)
                ->where('is_agree', 1)
                ->where('status', 1)
                ->first();

        if ($check_leave) {
            $result['code'] = 80115; //服务人员在请假期间,不能指派
            return $result;
        }

        DB::beginTransaction();
        try {
            Order::where('id', $orderId)->update([
                'seller_staff_id' => $staffId
            ]);
            $data = self::getSellerOrderById($orderId)->toArray();
            PushMessageService::notice($data['staff']['userId'], $data['staff']['mobile'], 'order.designate', $data, ['sms', 'app'], 'staff', 3, $orderId);
            $result['code'] = 80000;
            DB::commit();
        } catch (Exception $e) {
            $result['code'] = 80104;
            DB::rollback();
        }
        return $result;
    }

    /**
     * 随机指派人员
     * @param $orderId 订单编号数组
     */
    public static function ranUpdate($orderId, $serviceContent, $money) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api_system.success.update_info')
        ];

        //允许更改的订单状态
        $allow_status = [ORDER_STATUS_BEGIN_SERVICE, ORDER_STATUS_PAY_SUCCESS, ORDER_STATUS_AFFIRM_SERVICE, ORDER_PAY_STATUS_YES];
        $order_list = Order::where('id', $orderId)->whereIn('status', $allow_status)->first();
        if (!$order_list) {
            $result['code'] = ORDER_NOT_EXIST; // 订单不存在
            return $result;
        }
        if (!$order_list) {
            $result['code'] = 80107; // 不能指派
            return $result;
        }
        if ($order_list->order_type > 2) {
            $rules = array(
                'orderId' => ['required'],
                'serviceContent' => ['required'],
                'money' => ['required', 'regex:/^-?\d+$/'],
            );

            $messages = array(
                'orderId.required' => '8010911',
                'serviceContent.required' => '80108',
                'money.required' => '80109',
                'money.regex' => '80113',
            );
            $validator = Validator::make([
                        'orderId' => $orderId,
                        'serviceContent' => $serviceContent,
                        'money' => $money,
                            ], $rules, $messages);
        } else {

            $rules = array(
                'orderId' => ['required'],
            );

            $messages = array(
                'orderId.required' => '8010911',
            );
            $validator = Validator::make([
                        'orderId' => $orderId,
                            ], $rules, $messages);
        }
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        $check_staff = SellerStaff::where('status', 1)->get()->toArray();
        $staff = $check_staff[array_rand($check_staff, 1)];
        if (!$staff) {
            $result['code'] = 80106; // 服务人员不存在
            return $result;
        }
        DB::beginTransaction();
        try {

            if ($order_list->order_type > 2) {
                //更新订单表
                Order::where('id', $orderId)->update([
                    'seller_staff_id' => $staff['id'],
                    'seller_id' => $staff['seller_id'],
                    'status' => ORDER_STATUS_AFFIRM_ASSIGN_SERVICE,
                    'total_fee' => $money,
                    'service_content' => $serviceContent,
                    'seller_confirm_time' => UTC_TIME,
                ]);
            } else {

                //更新订单表
                Order::where('id', $orderId)->update([
                    'seller_staff_id' => $staff['id'],
                    'seller_id' => $staff['seller_id'],
                    'status' => ORDER_STATUS_AFFIRM_ASSIGN_SERVICE,
                    'seller_confirm_time' => UTC_TIME,
                ]);
            }
            $result['code'] = 80000;
            DB::commit();
        } catch (Exception $e) {
            $result['code'] = 80104;
            DB::rollback();
        }
        return $result;
    }

    /**
     * 更新订单
     * @param  int $id 订单id
     * @param  int $sellerId 商家
     * @param  int $status 状态
     * @param  int $content 内容
     * @return array   更新结果
     */
    public static function updateRestaurantOrder($id, $restaurantId, $status, $refuseContent) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.update_info')
        ];


        $order = Order::where('id', $id)->where('restaurant_id', $restaurantId)->first();

        //没有订单
        if ($order == false) {
            $result['code'] = self::ORDER_NOT_EXIST; // 没有找到相关订单

            return $result;
        }

        if ($order->status == ORDER_STATUS_AFFIRM_SERVICE) {
            $result['code'] = 50200; // 订单已接受
            return $result;
        }

        if ($status == ORDER_STATUS_AFFIRM_SERVICE) {
            $order->status = ORDER_STATUS_AFFIRM_SERVICE;

            $order->buyer_confirm_time = UTC_TIME;
        } else if ($status == ORDER_RESTAURANT_REFUSE_SERVICE) {
            if ($refuseContent == "") {
                $result['code'] = 50201;
                return $result;
            } else {
                $order->status = ORDER_RESTAURANT_REFUSE_SERVICE;
                $order->refuse_content = $refuseContent;
                $order->seller_confirm_time = UTC_TIME;
            }
        }
        /* else
          {
          if( $order->getIsCanCancelAttribute() == false &&
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
          else if($status == ORDER_RESTAURANT_REFUSE_SERVICE ||
          $status == ORDER_RESTAURANT_REFUSE_SERVICE)
          {
          $order->status = $status;

          $order->buyer_confirm_end_time = UTC_TIME;

          if($status == ORDER_STATUS_SELLER_REFUSE){

          //有优惠券，则退回优惠券
          $return_promotion = PromotionService::returnPromotion($order);

          if(!$return_promotion){
          $result['code'] = 50115;
          return $result;
          }
          }
          }
          } */

        $order->save();
        $data = self::getRestaurantOrderById($restaurantId, $id)->toArray();

        //当状态为接受订单或者拒绝订单的时候,推送消息
        // if ($status == ORDER_STATUS_AFFIRM_SERVICE ||
// 	        $status == ORDER_RESTAURANT_REFUSE_SERVICE) {
// 	            $noticeTpe = $status == ORDER_STATUS_SELLER_ACCEPT ? 'order.accept' : 'order.refund';
// 	            PushMessageService::notice($data['userId'],$data['mobile'], $noticeTpe,['sn' => $data['sn']],['sms','app'],'buyer',4,$id);
// 	        }

        $result["data"] = $data;

        return $result;
    }

    /**
     * 删除订单
     * @param  int $sellerId 卖家
     * @param int  $id 订单id
     * @return array   删除结果
     */
    public static function deleteOrder($sellerId, $id) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.delete_info')
        ];

        $order = Order::where('id', $id)->where('seller_id', $sellerId)->first();

        //没有订单
        if ($order == false) {
            $result['code'] = self::ORDER_NOT_EXIST;

            return $result;
        }

        //当订单状态不为卖家删除,订单不能再删除
        if ($order->status == ORDER_STATUS_ADMIN_DELETE ||
                $order->status == ORDER_STATUS_FINISH_USER ||
                $order->status == ORDER_STATUS_CANCEL_USER ||
                $order->status == ORDER_STATUS_CANCEL_AUTO ||
                $order->status == ORDER_STATUS_CANCEL_SELLER ||
                $order->status == ORDER_STATUS_CANCEL_ADMIN ||
                $order->status == ORDER_STATUS_USER_DELETE ||
                $order->status == ORDER_STATUS_ADMIN_DELETE) {
            $order->status = ORDER_STATUS_SELLER_DELETE;
            $order->save();
        } else {
            $result['code'] = self::ORDER_NOT_DELETE;
            return $result;
        }
        return $result;
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
        $sql = "select d.name,c.cate_id,sum(b.num*b.price) money from {$pre}order a, {$pre}order_goods b, {$pre}goods c, {$pre}goods_cate d where a.status in(" . ORDER_STATUS_FINISH_SYSTEM . "," . ORDER_STATUS_FINISH_USER . ") and a.seller_id={$sellerId} and a.id = b.order_id and c.id = b.goods_id and c.cate_id = d.id and a.create_time>={$beginTime} and a.create_time<={$endTime} group by d.id";
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

    /**
     * 订单金额分类统计， add by skywod 2016-05-05
     * @param $sellerId 商家ID
     * @param $beginTime 订单创建开始时间，unix时间戳
     * @param $endTime  订单创建结束时间，unix时间戳
     */
    public static function statusMoney($sellerId, $beginTime, $endTime, $cateId, $status = false) {
        self::endOrder();
        $status_str = "";
        if ($status) {
            $status_str = " and a.status in(" . implode(',', $status) . ") ";
        }
        $pre = 'yz_';
        $sql = "select d.name,c.cate_id,sum(b.num*b.price) money from {$pre}order a, {$pre}order_goods b, {$pre}goods c, {$pre}goods_cate d where d.id={$cateId} " . $status_str . " and a.seller_id={$sellerId} and a.id = b.order_id and c.id = b.goods_id and c.cate_id = d.id and a.create_time>={$beginTime} and a.create_time<={$endTime} group by d.id";
        $results = DB::select($sql);
        if ($results) {
            return $results;
        }
        return [['money' => 0]];
    }

    public static function getStatusList($sellerId, $cateId, $status, $beginTime, $endTime) {
        self::endOrder();
        switch ($status) {
            case '0': //所有订单
                $status_type = [];
                break;
            case '1':// 已完成
                $status_type = [
                    ORDER_STATUS_FINISH_SYSTEM,
                    ORDER_STATUS_FINISH_USER
                ];
                break;
            case '2': //待发货  102 110
                $status_type = [
                    ORDER_STATUS_BEGIN_USER,
                    ORDER_STATUS_PAY_SUCCESS,
                    ORDER_STATUS_PAY_DELIVERY,
                    ORDER_STATUS_AFFIRM_SELLER
                ];
                break;
            case '3'://待完成 106 107
                $status_type = [
                    ORDER_STATUS_START_SERVICE,
                    ORDER_STATUS_FINISH_STAFF
                ];
                break;
            case '4'://拒绝 301 302 303 400 401 402 403 404 311 312
                $status_type = [
                    ORDER_STATUS_CANCEL_USER,
                    ORDER_STATUS_CANCEL_AUTO,
                    ORDER_STATUS_CANCEL_SELLER,
                    ORDER_STATUS_CANCEL_ADMIN
                ];
                break;
            case '5'://退款
                $status_type = [
                    ORDER_STATUS_REFUND_AUDITING,
                    ORDER_STATUS_REFUND_HANDLE,
                    ORDER_STATUS_REFUND_FAIL,
                    ORDER_STATUS_REFUND_SUCCESS
                ];
                break;
            case '6':// 已删除
                $status_type = [
                    ORDER_STATUS_USER_DELETE
                ];
                break;
            default:
                $status_type = [];
                break;
        }
        $status_str = "";
        if (count($status_type) > 0) {
            $status_str = " and a.status in(" . implode(',', $status_type) . ") ";
        }
        $pre = 'yz_';
        $sql = "select a.id,a.create_time,a.sn,d.name,c.cate_id,sum(b.num*b.price) money from {$pre}order a, {$pre}order_goods b, {$pre}goods c, {$pre}goods_cate d where d.id={$cateId} " . $status_str . " and a.seller_id={$sellerId} and a.id = b.order_id and c.id = b.goods_id and c.cate_id = d.id and a.create_time>={$beginTime} and a.create_time<={$endTime} group by a.id order by a.id desc";
        //return array('code'=>0,$sql);
        $results = DB::select($sql);
        //return array('code'=>0,$results);
        return $results;
    }

}
