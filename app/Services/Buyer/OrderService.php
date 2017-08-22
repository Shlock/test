<?php

namespace YiZan\Services\Buyer;

use YiZan\Models\GoodsNorms;
use YiZan\Models\SellerCateRelated;
use YiZan\Models\Promotion;
use YiZan\Services\SystemConfigService;
use YiZan\Services\SellerService;
use YiZan\Models\Order;
use YiZan\Models\ShoppingCart;
use YiZan\Models\UserAddress;
use YiZan\Models\Seller;
use YiZan\Models\SellerExtend;
use YiZan\Models\GoodsExtend;
use YiZan\Models\Goods;
use YiZan\Models\UserCard;
use YiZan\Models\OrderGoods;
use YiZan\Models\SellerStaff;
use YiZan\Models\GoodsStaff;
use YiZan\Models\SellerMoneyLog;
use YiZan\Models\PromotionSn;
use YiZan\Models\PromotionSellerCate;
use YiZan\Models\PromotionUnableDate;
use YiZan\Models\Refund;
use YiZan\Models\SellerDeliveryTime;
use Illuminate\Support\Facades\Lang;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Services\SellerMoneyLogService;
use YiZan\Services\PushMessageService;
use DB;

class OrderService extends \YiZan\Services\OrderService {

    /**
     * 催单
     * @param int $userId   会员编号
     * @param int $id       订单编号
     */
    public static function urgeOrder($userId, $id) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => ''
        ];
        $order = Order::where('user_id', $userId)
                ->where('id', $id)
                ->with('seller', 'staff', 'user')
                ->first();
        $order = $order ? $order->toArray() : [];
        try {
            if ($order['seller']) {
                PushMessageService::notice($order['seller']['userId'], $order['seller']['mobile'], 'order.urge', $order, ['sms', 'app'], 'seller', '3', $order['id']);
            }
            if ($order['staff'] && $order['seller']['mobile'] != $order['staff']['mobile']) {
                PushMessageService::notice($order['staff']['userId'], $order['staff']['mobile'], 'order.urge', $order, ['sms', 'app'], 'staff', '3', $order['id']);
            }
        } catch (Exception $e) {
            $result['code'] = 99999;
        }
        return $result;
    }

    /**
     * 下单
     * @param int $userId 用户编号
     * @param array $cartIds 购物车编号
     * @param int $addressId 地址编号
     * @param string $giftContent 贺卡内容
     * @param string $invoiceTitle 发票抬头
     * @param string $buyRemark 购物备注
     * @param string $appTime 配送时间(商品)
     * @param int $payment 支付方式 1:在线支付 0:货到付款
     * @param string $promotionSn 优惠券编号
     */
    public static function create($userId, $cartIds, $addressId, $giftContent, $invoiceTitle, $buyRemark, $appTime, $payment, $promotionSnId) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => ''
        ];
        $payment = $payment !== '' ? (int) $payment : $payment;

        //没有选择购物车的商品
        if (count($cartIds) < 1) {
            $result['code'] = '60501';
            return $result;
        }


        $address = UserAddress::where('user_id', $userId)->where('id', $addressId)->first();
        //地址信息不存在
        if (!$address) {
            $result['code'] = '60506';
            return $result;
        }

        //配送时间不能小于当前时间
        if ($appTime + 0 > 0) {
            $appTime = Time::toTime($appTime);
            if ($appTime < UTC_TIME) {
                $result['code'] = '60508';
                return $result;
            }
        }
        //一次只能下一个单
        $checkOnlySeller = ShoppingCart::where('user_id', $userId)
                        ->whereIn('id', $cartIds)
                        ->groupBy('seller_id')
                        ->select('seller_id')
                        ->get()->toArray();
        //存在多商家商品
        if (count($checkOnlySeller) > 1) {
            $result['code'] = '60507';
            return $result;
        }

        $checkOnlyGoods = ShoppingCart::where('user_id', $userId)
                        ->whereIn('id', $cartIds)
                        ->groupBy('type')
                        ->select('type')
                        ->get()->toArray();
        //服务类和商品类同时存在
        if (count($checkOnlyGoods) > 1) {
            $result['code'] = '60507';
            return $result;
        }

        $checkOnlyService = ShoppingCart::where('user_id', $userId)
                        ->whereIn('id', $cartIds)
                        ->where('type', '2')
                        ->select('id')
                        ->get()->toArray();
        //一个单只能下一个服务
        if (count($checkOnlyService) > 1) {
            $result['code'] = '60507';
            return $result;
        }


        $carts = ShoppingCart::where('user_id', $userId)
                        ->whereIn('id', $cartIds)
                        ->with('goods', 'norms')
                        ->get()->toArray();
        //有不存在的购物车信息
        if (count($cartIds) != count($carts)) {
            $result['code'] = '60502';
            return $result;
        }


        $checkSeller = Seller::where('id', $carts[0]['sellerId'])
                ->where('status', 1)
                ->with('extend')
                ->first();
        //商家信息错误
        if (!$checkSeller) {
            $result['code'] = '60509';
            return $result;
        }

        //商家是否在配送时间内
        if ($appTime + 0 > 0) {
            //商家是否在营业时间内
            $isCanBusiness = SellerService::isCanBusiness($checkSeller->id);
            if (!$isCanBusiness) {
                $result['code'] = '60514';
                return $result;
            }
            $appHours = Time::toDate($appTime, 'H:i');
            $isCanDelivery = SellerDeliveryTime::where('seller_id', $checkSeller->id)
                    ->whereRaw("HOUR(stime) <= HOUR('{$appHours}')")
                    ->whereRaw("HOUR(etime) > HOUR('{$appHours}')")
                    ->first();
            if (!$isCanDelivery) {
                $result['code'] = '60517';
                $result['data'] = $appHours . ' ' . $checkSeller->id;
                return $result;
            }
        }

        $goodsData = [];
        $goodsFee = 0;
        $weight = 0;
        $num = 0;
        $totalDuration = 0;
        $orderType = 1;
        //检测结算的商品信息是否处于正常状态,库存,购买限制
        foreach ($carts as $key => $val) {

            //商品是否处于上架状态
            if ($val['goods']['status'] != 1) {
                $result['code'] = '60505';
                $result['msg'] = $val['goods']['name'] . ' 已下架 ';
                return $result;
            }

            //购买限制
            if ($val['num'] > $val['goods']['buyLimit'] && $val['goods']['buyLimit'] != 0) {
                $result['code'] = '60503';
                //$result['msg'] = $val['goods']['name'].' 限购数量为 '.$val['goods']['buyLimit'];
                return $result;
            }

            if ($val['goods']['type'] == 1) {
                if ($val['normsId'] > 0) {
                    //当有规格编号的时候,检测规格的库存
                    if ($val['num'] > $val['norms']['stock']) {
                        $result['code'] = '60504';
                        //$result['msg'] = $val['goods']['name'].$val['norms']['name'].' 库存已不足';
                        return $result;
                    }
                } else {
                    //无规格的时候 检测商品的库存
                    if ($val['num'] > $val['goods']['stock']) {
                        $result['code'] = '60504';
                        //$result['msg'] = $val['goods']['name'].$val['norms']['name'].' 库存已不足';
                        return $result;
                    }
                }
            } else {//下单为服务类型时,提成比例计算
                $deduct = [
                    'type' => $val['goods']['deduct_type'],
                    'val' => $val['goods']['deduct_val']
                ];
            }

            $duration = $val['goods']['unit'] == 1 ? $val['goods']['duration'] * 60 : $val['goods']['duration'];
            $goodsData[$key] = [
                'seller_id' => $val['sellerId'],
                'goods_name' => $val['goods']['name'],
                'goods_duration' => $duration,
                'goods_images' => $val['goods']['image'],
                'goods_norms' => $val['norms']['name'],
                'goods_id' => $val['goodsId'],
                'goods_norms_id' => $val['normsId'],
                'price' => $val['normsId'] > 0 ? $val['norms']['price'] : $val['goods']['price'],
                'num' => $val['num']
            ];
            $goodsFee += $goodsData[$key]['price'] * $val['num'];
            //$weight += $val['weight'] * $val['num'];
            if ($val['norms']) {
                $weight += $val['norms']['weight'] * $val['num'];
            } else {
                $weight += $val['goods']['weight'] * $val['num'];
            }
            $num += $val['num'];
            $totalDuration += $duration * $val['num'];
            //$appTime = $val['goods']['type'] == 2 ? $val['serviceTime'] : $appTime;
            $orderType = $val['goods']['type'];
        }
        //随机选择服务人员
        $sellerId = $checkSeller->id;
        if ($appTime + 0 == 0) {
            $staff = new \stdClass();
            $staff->id = 0;
            $totalFee = $goodsFee;
        } else {
            $goodsFee += self::weight($weight, $goodsFee);
            //未达到商家的起送费
            if ($goodsFee < $checkSeller->service_fee) {
                $result['code'] = '60510';
                return $result;
            }
            $staffs = SellerStaff::where('seller_id', $checkSeller->id)
                    ->where('order_status', 1)
                    ->whereNotIn('id', function($query) {
                $query->select('staff_id')
                ->from('staff_leave')
                ->where('begin_time', '<=', UTC_TIME)
                ->where('end_time', '>=', UTC_TIME)
                ->where('is_agree', 1)
                ->where('status', 1);
            });
            if ($orderType == 2) {
                $goodsId = $goodsData[0]['goods_id'];
                $staffs->whereIn('type', [0, 2, 3])
                        ->whereIn('id', function($query) use ($goodsId) {
                            $query->select('staff_id')
                            ->from('goods_staff')
                            ->where('goods_id', $goodsId);
                        });
            } else {
                $staffs->whereIn('type', [0, 1, 3]);
            }
            $staff = $staffs->orderBy(DB::raw('RAND()'))->first();
            $checkSeller->delivery_fee = $orderType == 1 ? $checkSeller->delivery_fee : 0;
            $totalFee = $goodsFee + $checkSeller->delivery_fee; //订单总金额
        }
        //优惠券是否可用
        if ($promotionSnId > 0 && $totalFee > 0.001) {
            $checkPro = PromotionSn::where('id', $promotionSnId)
                            ->where('user_id', $userId)
                            ->where('use_time', 0)
                            ->where('begin_time', '<=', UTC_TIME)
                            ->where('expire_time', '>=', UTC_TIME)
                            ->with('promotion')->first();

            //是否为不可用日期
            $isAbleDate = PromotionUnableDate::where('date_time', UTC_DAY)
                    ->where('promotion_id', $checkPro->promotion->id)
                    ->first();

            //是否为指定商家
            if ($checkPro->promotion->use_type == 3) {
                $isAble = $checkPro->promotion->seller_id == $sellerId;
            }
            //是否为指定分类
            if ($checkPro->promotion->use_type == 2) {
                $isAble = PromotionSellerCate::where('promotion_id', $checkPro->promotion->id)
                                ->whereIn('seller_cate_id', function($query) use ($sellerId) {
                                    $query->select('cate_id')
                                    ->from('seller_cate_related')
                                    ->where('seller_id', $sellerId);
                                })->first();
            }

            if ($checkPro->promotion->use_type == 1) {
                $isAble = true;
            }
            //if (!$checkPro || $isAbleDate || !$isAble || ($checkPro->promotion->limit_money > 0 && $checkPro->promotion->limit_money > $totalFee)) {
            if (!$checkPro || $isAbleDate || !$isAble || ($checkPro->promotion->limit_money > 0 && $checkPro->promotion->limit_money > $totalFee)) {
                $result['code'] = 60516;
                return $result;
            }
            $discountFee = $checkPro->money;
        } else {
            $discountFee = 0;
        }

        $payFee = $totalFee - $discountFee > 0 ? $totalFee - $discountFee : 0; //支付金额
        $drawnFee = (double) round(($totalFee * ($checkSeller->deduct / 100)), 2); // 平台抽成金额
        //员工提成,配送人员暂无提成
        if ($orderType == 2) {
            $staffFee = $deduct['type'] == 1 ? $deduct['val'] : (double) round((($deduct['val'] / 100) * $totalFee), 2);
        } else {
            $staffFee = 0;
        }

        $systemOrderPass = SystemConfigService::getConfigByCode('system_order_pass');
        $autoCancelTime = $systemOrderPass + UTC_TIME;

        $orderData = [
            'sn' => Helper::getSn(),
            'seller_id' => $checkSeller->id,
            'user_id' => $userId,
            'order_type' => $userId,
            'name' => $address->name,
            'name_match' => String::strToUnicode($address->name),
            'mobile' => $address->mobile,
            'address_id' => $address->id,
            // 'address' => $address->address.$address->doorplate,
            'address' => $address->address,
            'map_point' => $address->map_point_str,
            'buy_remark' => $buyRemark,
            'invoice_remark' => $invoiceTitle,
            'gift_remark' => $giftContent,
            'app_time' => $appTime,
            'app_day' => $appTime + 0 == 0 ? Time::toDayTime(time()) : Time::toDayTime($appTime),
            'create_time' => UTC_TIME,
            'create_day' => UTC_DAY,
            'fre_time' => $appTime,
            'auto_cancel_time' => $autoCancelTime,
            'duration' => $totalDuration,
            'order_type' => $orderType,
            'total_fee' => $totalFee,
            'goods_fee' => $goodsFee,
            'seller_fee' => $totalFee - $drawnFee,
            'drawn_fee' => $drawnFee,
            'discount_fee' => $discountFee,
            'freight' => $checkSeller->delivery_fee,
            'count' => $num,
            'seller_staff_id' => (int) $staff->id,
            'status' => ORDER_STATUS_BEGIN_USER,
            'pay_fee' => $payFee,
            'staff_fee' => $staffFee,
            'promotion_sn_id' => $promotionSnId
        ];
        DB::beginTransaction();
        if ($payFee == 0 || $payment === 0) {
            $orderData['pay_type'] = $payFee == 0 ? 'free' : 'cashOnDelivery';
            $orderData['pay_time'] = UTC_TIME;
            $orderData['pay_status'] = ORDER_PAY_STATUS_YES;
            $orderData['status'] = $payFee == 0 ? ORDER_STATUS_PAY_SUCCESS : ORDER_STATUS_PAY_DELIVERY;
            //$orderData['seller_confirm_time'] = UTC_TIME;
            //平台抽成,扣除余额
            if ($drawnFee > 0.001 && $payment === 0) {
                if ($checkSeller->is_cash_on_delivery !== 1) {
                    $result['code'] = '60515';
                    return $result;
                }
                $deduction = SellerExtend::where('seller_id', $checkSeller->id)
                        ->where('money', '>=', $drawnFee)
                        ->decrement('money', $drawnFee);
                if (!$deduction) {//余额不足
                    $result['code'] = '60515';
                    return $result;
                }
            }
        }
        $bln = false;
        $orderId = Order::insertGetId($orderData);
        if ((int) $orderId > 0) {
            //下单成功扣除
            if ($orderData['pay_type'] == 'cashOnDelivery') {
                //写入扣款日志
                SellerMoneyLogService::createLog(
                        $checkSeller->id, SellerMoneyLog::TYPE_DELIVERY_MONEY, $orderId, -$drawnFee, '现金支付订单' . $orderData['sn'] . '，佣金扣款', 1
                );
            }
            try {
                foreach ($goodsData as $key => $val) {
                    $goodsData[$key]['order_id'] = $orderId;
                    //更新商品扩展表销量
                    GoodsExtend::where('goods_id', $val['goods_id'])->increment('sales_volume', $val['num']);
                    if ($orderType == 1) {
                        if ($val['goods_norms_id'] > 0) {
                            //更新商品规格表库存
                            GoodsNorms::where('id', $val['goods_norms_id'])->decrement('stock', $val['num']);
                        } else {
                            //更新商品表库存
                            Goods::where('id', $val['goods_id'])->decrement('stock', $val['num']);
                        }
                    }
                }
                //插入订单商品明细表
                OrderGoods::insert($goodsData);
                //更新商家扩展表
                SellerService::incrementExtend($checkSeller->id, 'order_count'); //增加商家销量
                if ($orderData['seller_fee'] > 0.001 && $orderData['pay_status'] == 1 && $orderData['pay_type'] != 'cashOnDelivery') {
                    SellerService::incrementExtend($checkSeller->id, 'wait_confirm_money', $orderData['seller_fee']);
                }

                //删除已下单的购物车
                ShoppingCart::whereIn('id', $cartIds)->delete();

                //已使用的优惠券更新
                if ((int) $promotionSnId > 0) {
                    PromotionSn::where('id', $promotionSnId)->update(['use_time' => UTC_TIME]);
                }

                $result['msg'] = Lang::get('api.success.user_create_order');
                $result['data'] = self::getOrderById($userId, $orderId);
                $bln = true;
                DB::commit();
            } catch (Exception $e) {
                $result['code'] = '60013';
                DB::rollback();
            }
        }

        if ($bln && $orderData['pay_status'] == ORDER_PAY_STATUS_YES) {
            try {
                PushMessageService::notice($result['data']['seller']['userId'], $result['data']['seller']['mobile'], 'order.create', $result['data'], ['sms', 'app'], 'seller', '3', $orderId, "neworder.caf");
                if ($result['data']['staff'] && $result['data']['seller']['userId'] != $result['data']['staff']['userId']) {
                    PushMessageService::notice($result['data']['staff']['userId'], $result['data']['staff']['mobile'], 'order.create', $result['data'], ['sms', 'app'], 'staff', '3', $orderId, "neworder.caf");
                }
            } catch (Exception $e) {
                
            }
        }
        return $result;
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
        $res = Order::where('user_id', $userId)->where('id', $orderId)->with('cartSellers', 'seller', 'staff', 'user', 'refundCount')->first();
        if (!$res) {
            return false;
            $res = $res->toArray();
        }
        $res['invoiceTitle'] = $res['invoiceRemark'];
        $res['price'] = $res['totalFee'];
        $res['giftContent'] = $res['giftRemark'];
        $res['sellerName'] = $res['seller']['name'];
        $res['shopName'] = $res['seller']['name'];
        $res['sellerTel'] = $res['seller']['serviceTel'];
        $res['staffName'] = $res['staff']['name'];
        $res['staffMobile'] = $res['staff']['mobile'];
        $res['countGoods'] = Goods::where('type', 1)->where('seller_id', $res['seller']['id'])->where('status', 1)->count('id');
        $res['countService'] = Goods::where('type', 2)->where('seller_id', $res['seller']['id'])->where('status', 1)->count('id');
        // unset($res['seller'],$res['staff']);
        return $res;
    }

    public static function weight($weight, $price) {
        $prices = [39, 49, 59, 69, 79, 89, 99];
        $deliver = [6, 5, 4, 3, 2, 1, 0];
        $index = 6;
        foreach ($prices as $key => $item) {
            if ($item > $price) {
                $index = $key;
                break;
            }
        }
        if ($weight < 3000) {
            return $deliver[$index];
        } else {
            $weight -= 3000;
            return $deliver[$index] + 0.5 * $weight / 1000;
        }
    }

    /**
     * 订单计算
     * @param int       $userId       会员编号
     * @param int       $cartIds      购物车编号
     * @param string    $promotionSn  优惠券编号
     */
    public static function orderCompute($userId, $cartIds, $promotionSnId) {

        $check = ShoppingCart::where('user_id', $userId)
                ->whereIn('id', $cartIds);

        $checkService = $check->where('type', 2)
                ->count();

        if ($checkService > 1) {
            $result['code'] = 60513;
            return $result;
        }

        $data = ShoppingCart::where('user_id', $userId)
                ->whereIn('id', $cartIds)
                ->with('goods', 'norms')
                ->get()
                ->toArray();
        $totalFee = 0;
        $goodsFee = 0;
        $discountFee = 0;
        $seller = Seller::where('id', $data[0]['seller_id'])->first();
        $seller['extend'] = SellerExtend::where('seller_id', $data[0]['seller_id'])->first();
        $payFee = 0;
        $isCashOnDelivery = (int) $seller['isCashOnDelivery'];
        $isShowPromotion = 0;

        //优惠券金额
        $pro = PromotionSn::where('user_id', $userId)
                ->where('id', $promotionSnId)
                ->where('use_time', 0)
                ->where('begin_time', '<=', UTC_TIME)
                ->where('expire_time', '>=', UTC_TIME)
                ->where('is_del', 0)
                ->first();
        $weight = 0;
        $isCard = 0;
        $cateIds = [];
        foreach ($data as $key => $value) {
            if ($value['goods']['id'] == '75837' || $value['goods']['id'] == '75844') {
                $isCard = 1;
            }
            $cateIds[] = $value['goods']['cateId'];
            if ($value['norms']) {
                $goodsFee += $value['norms']['price'] * $value['num'];
                $weight += $value['norms']['weight'] * $value['num'];
            } else {
                $goodsFee += $value['goods']['price'] * $value['num'];
                $weight += $value['goods']['weight'] * $value['num'];
            }
        }
        $freight = self::weight($weight, $goodsFee);
        //可用优惠券数量
        $sellerId = $seller['id'];
        $totalMoney = $goodsFee + $freight;

        $promotionWids = PromotionSellerCate::whereIn('seller_cate_id', function($query) use ($sellerId) {
                    $query->select('cate_id')
                            ->from('seller_cate_related')
                            ->where('seller_id', $sellerId);
                })->groupBy('promotion_id')->lists('promotion_id');


        $promotionIds = Promotion::where('limit_money', '<=', $totalMoney)->lists('id');
//        $promotionIds = Promotion::where('limit_money', '<=', $totalMoney)
//                        ->where(function($query) use ($promotionWids, $sellerId) {
//                            $query->where(function($queryOne) use ($promotionWids) {
//                                $queryOne->where('use_type', 2)
//                                ->whereIn('id', $promotionWids);
//                            })->orWhere(function($queryTwo) use ($sellerId) {
//                                $queryTwo->where('use_type', 3)
//                                ->where('seller_id', $sellerId);
//                            })->orWhere('use_type', 1);
//                        })->whereNotIn('id', function($query) {
//                    $query->select('promotion_id')
//                            ->from('promotion_unable_date')
//                            ->where('date_time', UTC_DAY);
//                })->lists('id');
        $proSnCount = PromotionSn::where('user_id', $userId)
                ->where('use_time', 0)
                ->where('begin_time', '<=', UTC_TIME)
                ->where('expire_time', '>=', UTC_TIME)
                ->where('is_del', 0)
                ->whereIn('promotion_id', $promotionIds)
                ->count();

        $todayUsed = PromotionSn::where('user_id', $userId)
                        ->where('use_time', '>=', mktime(0, 0, 0, date('m'), date('d'), date('Y')))->count();
        $cateIds = array_unique($cateIds);
        //return ['code'=>0,$cateIds];
        $day0 = self::day0($userId, $goodsFee, $cateIds);
        if ($day0 == 1) {
            $todayUsed = 1;
        } elseif ($day0 == 2) {
            $todayUsed = 1;
        } elseif ($day0 == 3) {
            $todayUsed = 0;
        }
        if ($todayUsed == 0) {
            if ($pro || $proSnCount > 0) {
                $discountFee = $pro->promotion->type == 'offset' ? ($goodsFee + $freight) : (double) $pro->promotion->money;
                $isShowPromotion = ($goodsFee + $freight) > 0.001 ? 1 : 0;
            }
        }

        //总金额等于商品金额加上配送费减去优惠金额
        $totalFee = $goodsFee + $freight - $discountFee;
        $totalFee = $totalFee > 0 ? $totalFee : 0;

        //本次下单需要扣除的平台抽成
        $fee = $totalFee * $seller['deduct'] / 100;

        $payFee = $totalFee;

        //如果商家余额大于等于平台抽成
        if ($fee > $seller['extend']['money']) {
            $isCashOnDelivery = 0;
        }
        $isBind = 0;
        if ($isCard == 1) {
            $isBind = UserCard::where('user_id', $userId)
                    ->count();
        }
        $data = [
            'isBind' => $isBind,
            'isCard' => $isCard,
            'totalFee' => (double) round($totalFee, 2),
            'goodsFee' => (double) round($goodsFee, 2),
            'discountFee' => $discountFee,
            'freight' => $freight,
            'payFee' => (double) round($payFee, 2),
            'isCashOnDelivery' => $isCashOnDelivery,
            'isShowPromotion' => 1,
            'promotionCount' => $proSnCount,
            'sellerId' => $sellerId,
            'totalMoney' => (double) round($totalMoney, 2),
            'todayUsed' => $todayUsed > 0 ? 'true' : 'false'
        ];

        return $data;
    }

    public static function day0($userId, $total, $cateIds) {
        $cardNum = UserCard::where('user_id', $userId)
                ->count();
        if ($cardNum == 1) {
            $day = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 8 * 3600;
            $orders = Order::where('user_id', $userId)
                            ->whereIn('status', [ORDER_STATUS_BEGIN_USER, ORDER_STATUS_PAY_SUCCESS, ORDER_STATUS_START_SERVICE, ORDER_STATUS_PAY_DELIVERY, ORDER_STATUS_AFFIRM_SELLER, ORDER_STATUS_FINISH_STAFF, ORDER_STATUS_FINISH_SYSTEM, ORDER_STATUS_FINISH_USER])
                            ->where('create_time','>', $day)->with('goods')->get()->toArray();
            $goods_cateIds = [];
            foreach ($orders as $order) {
                foreach ($order['goods'] as $goods) {
                    $order_goods = Goods::where('id', $goods['goodsId'])->first();
                    if ($order_goods) {
                        if ($order_goods['id'] != '75837' && $order_goods['id'] != '75844') {
                            $total +=$order_goods['price'];
                            $goods_cateIds[] = $order_goods['cate_id'];
                        }
                    }
                }
            }
            $goods_cateIds = array_unique($goods_cateIds);
            $cates = array_intersect($cateIds, $goods_cateIds);
            if (count($cates) > 0) {
                return 2;
            }
            if ($total > 200) {
                return 1;
            }
            return 3;
        }
        return 4;
    }

    /**
     * 退款详情
     * @param int $userId 用户编号
     * @param int $orderId 订单编号
     */
    public function refundDetail($userId, $orderId) {
        $data = [
            'money' => 0,
            'time' => '',
            'payment' => '',
            'status' => '成功',
            'stepOne' => [
                'status' => 0,
                'name' => '退款申请',
                'brief' => '',
                'time' => ''
            ],
            'stepTwo' => [
                'status' => 0,
                'name' => '平台审核通过',
                'brief' => '',
                'time' => ''
            ],
            'stepThree' => [
                'status' => 0,
                'name' => '资金到账',
                'brief' => '',
                'time' => ''
            ]
        ];
        $order = Order::where('user_id', $userId)->where('id', $orderId)->with('userRefund', 'refundCount')->first();
        if ($order) {
            $order = $order->toArray();
            if ($order['refundCount'] > 0) {
                $data['time'] = Time::toDate($order['cancelTime'], 'Y-m-d H:i:s');
                $data['stepOne'] = [
                    'status' => 1,
                    'name' => '退款申请',
                    'brief' => $order['userRefund'][0]['content'],
                    'time' => Time::toDate($order['userRefund'][0]['createTime'], 'Y-m-d H:i:s')
                ];
            }
            foreach ($order['userRefund'] as $k => $v) {
                $payment = Lang::get('admin.payments.' . $v['paymentType']);
                $data['money'] = (double) round(($v['money'] + $data['money']), 2);
                if ($v['paymentType'] != 'balancePay') {
                    $data['status'] = $v['status'] == 1 ? '成功' : '退款中';
                }
                if ($order['refundCount'] > 1) {
                    $data['payment'] .= '￥' . $v['money'] . '退回至' . $payment . ' ';
                    if ($v['paymentType'] != 'balancePay' && $v['status'] == 1) {
                        $data['stepTwo'] = [
                            'status' => 1,
                            'name' => '平台审核通过',
                            'brief' => '平台已审核通过，等待资金到账',
                            'time' => Time::toDate($v['createTime'], 'Y-m-d H:i:s')
                        ];
                        $data['stepThree'] = [
                            'status' => 1,
                            'name' => '资金到账',
                            'brief' => '您的资金￥' . $v['money'] . '已退回至' . $payment . '账号',
                            'time' => Time::toDate($v['createTime'], 'Y-m-d H:i:s')
                        ];
                    }
                } else {
                    $data['payment'] = $payment;
                    if ($v['status'] == 1) {
                        $data['stepTwo'] = [
                            'status' => 1,
                            'name' => '平台审核通过',
                            'brief' => '平台已审核通过，等待资金到账',
                            'time' => Time::toDate($v['createTime'], 'Y-m-d H:i:s')
                        ];
                        $data['stepThree'] = [
                            'status' => 1,
                            'name' => '资金到账',
                            'brief' => '您的资金￥' . $v['money'] . '已退回至' . $payment . '账号',
                            'time' => Time::toDate($v['createTime'], 'Y-m-d H:i:s')
                        ];
                    }
                }
            }
        }

        return $data;
    }

    /**
     * 不显示优惠信息
     */
    public function notshow($userId, $orderId) {
        $order = Order::where('user_id', $userId)->where('id', $orderId)->with('userRefund', 'refundCount')->first();
        if ($order) {
            Order::where('user_id', $userId)->where('id', $orderId)->update(['promotion_is_show' => 1]);
        }
    }

}
