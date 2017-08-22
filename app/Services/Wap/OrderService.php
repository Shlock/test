<?php namespace YiZan\Services\Wap;

use YiZan\Models\Order;
use YiZan\Models\Goods;
use YiZan\Models\SellerStaff;
use YiZan\Models\Seller;
use YiZan\Models\PromotionSn;
use YiZan\Models\UserRefund;
use YiZan\Models\UserPayLog;
use YiZan\Models\OrderPromotion;
use YiZan\Models\SellerMoneyLog;
use YiZan\Models\StaffAppoint;

use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use Exception, DB, Lang, Validator, App;
class OrderService extends \YiZan\Services\OrderService { 
	                            
    /**
	 * WAP端重写订单生成， 非个人版增加预约码生成 
	 * @param  int    $userId      会员编号
	 * @param  int    $goodsId     服务编号
	 * @param  string $promotionSn 优惠券码
	 * @param  string $userName    联系人
	 * @param  string $appointTime 预约时间
	 * @param  string $mobile      联系手机
	 * @param  string $address     地址
	 * @param  string $mapPoint    地图定位坐标
     * @param  string $remark      买家备注
     * @param  int    $duration    时长
     * @param  int    $staffId     员工编号
     * @param  string $isToStore   是否到店服务
	 * @return array               订单信息
	 */
	public static function createOrder($userId, $goodsId, $promotionSn, $userName, 
		$appointTime, $mobile, $address, $mapPoint, $remark, $duration, $staffId, $isToStore = 0) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ''
		);

		$rules = array(
			'userName' => ['required'],
		    'mobile'   => ['required','regex:/^1[0-9]{10}$/'],
		    'address'  => ['required'],
		    'mapPoint' => ['required','point']
		);

		$messages = array(
			'userName.required'	=> '60007',
		    'mobile.required'	=> '60008',
		    'mobile.regex'		=> '60009',
		    'address.required' 	=> '60010',
		    'mapPoint.required' => '60011'
		);

		$validator = Validator::make([
				'userName' => $userName,
				'mobile'   => $mobile,
				'address'  => $address,
				'mapPoint' => $mapPoint
			], $rules, $messages);
		if ($validator->fails()) {//验证信息
	    	$messages = $validator->messages();
	    	$result['code'] = $messages->first();
	    	return $result;
	    }

	    $mapPoint = Helper::foramtMapPoint($mapPoint);
	    if (!$mapPoint) {
	    	$result['code'] = 60011;
	    	return $result;
	    }

	    $goods = Goods::find($goodsId);
	    if (!$goods) {
	    	$result['code'] = 60001;
	    	return $result;
	    }

	    $result = self::moneyCompute($userId, $goodsId, $promotionSn, $duration, $goods);
	    if ($result['code'] != 0) {
	    	return $result; 
	    }
	    $moneys = $result['data'];
	    $result['data'] = null;
	    
	    if ($goods->price_type == Goods::PRICE_TYPE_HOUR) {
	    	if ($duration < StaffAppoint::SERVICE_SPAN) {
				$result['code'] = 60023;
				return $result;
			}
			$goods->duration = $duration;
		}

		$result = self::checkOrder($goodsId, $appointTime, $mapPoint, $duration, $staffId, $goods, false);
		if ($result['code'] != 0) {
	    	return $result; 
	    }
	    $staff = $result['data']['staff'];
	    $result['data'] = null;

		$appointTime = Time::toTime($appointTime);

	    //格式化地图坐标
	    $map_point = $mapPoint;
	    $mapPoint  = str_replace(',', ' ', $mapPoint);

	    $result['msg'] = Lang::get('api.success.user_create_order');
	    $order 					= new Order;
	    $order->seller_id 		= $staff->seller_id;
	    $order->seller_staff_id = $staff->id;
	    $order->user_id 		= $userId;
	    $order->goods_id 		= $goodsId;
	    $order->goods_name 		= $goods->name;
	    $order->goods_img 		= $goods->image;
	    $order->goods_duration 	= $goods->duration;
	    $order->total_fee 		= $moneys['totalMoeny'];
	    $order->discount_fee 	= $moneys['promotionMoney'];
	    $order->pay_fee 		= $moneys['payMoney'];

	    //服务人员可获得的费用
	    $order->seller_fee		= $moneys['totalMoeny'];

	    //当有优惠时
	    if ($order->discount_fee > 0) {
	    	$promotionSn = PromotionSn::with('promotion')->where('user_id', $userId)->where('sn', $promotionSn)->first();
	    	$order->promotion_info = $promotionSn->promotion->name.' '.$promotionSn->promotion->brief;
	    	//当优惠券为服务人员发布时
	    	if ($promotionSn->promotion->seller_id > 0) {
	    		$order->seller_fee = $order->seller_fee > $moneys['promotionMoney'] ? $order->seller_fee > $moneys['promotionMoney'] : 0;
	    		//服务人员或机构优惠金额
	    		$order->seller_discount_fee = $moneys['promotionMoney'];
	    	} else {
	    		//系统优惠金额
	    		$order->site_discount_fee 	= $moneys['promotionMoney'];
	    	}
	    }

	    $order->service_fee 	= 0;
	    $system_order_fee = (float)SystemConfigService::getConfigByCode('system_order_fee');
	    if ($system_order_fee > 0) {//服务费
	    	$order->service_fee = $order->seller_fee * ($system_order_fee / 100);
	    }

	    //服务人员提成
	    if ($goods->deduct_type > 0) {
	    	$staff_deduct_type  = (int)$goods->deduct_type;
	    	$staff_deduct_value = (float)$goods->deduct_value;
	    } else {
	    	$staff_deduct_type  = (int)SystemConfigService::getConfigByCode('staff_deduct_type');
	   		$staff_deduct_value = (float)SystemConfigService::getConfigByCode('staff_deduct_value');
	    }
	    if ($staff_deduct_type == 1) {
	    	$order->deduct_amount = $staff_deduct_value;
	    } elseif ($staff_deduct_type == 2) {
	    	$order->deduct_amount = $order->seller_fee * $staff_deduct_value / 100;
	    } 

	    //预约时间
	    $order->appoint_time = $appointTime;
	    $order->appoint_hour = Time::toDate($appointTime, 'H');
	    $order->appoint_day  = Time::toDayTime($appointTime);
	    //服务结束时间
	    $order->service_end_time = $appointTime + $goods->duration;
	    $order->service_end_hour = Time::toDate($order->service_end_time, 'H');
	    $order->service_end_day  = Time::toDayTime($order->service_end_time);
	    //联系信息
	    $order->user_name 		= $userName;
	    $order->mobile 			= $mobile;
	    $order->address 		= $address;
	    $order->map_point 		= DB::raw("GeomFromText('POINT({$mapPoint})')");
	    $order->map_point_str 	= $map_point;
	    $order->buy_remark 		= $remark;
	    $order->name_match 		= String::strToUnicode($userName . $mobile);
        
        // 订单状态
        $order->status = ORDER_STATUS_WAIT_PAY;
        
	    //当支付金额为0时
	    if ($order->pay_fee <= 0) {
	    	$order->pay_status 	= ORDER_PAY_STATUS_YES;//已支付
            $order->status = ORDER_STATUS_PAY_SUCCESS;
	    	$order->pay_time 	= UTC_TIME;
            $order->seller_confirm_end_time 	= UTC_TIME + (int)SystemConfigService::getConfigByCode('system_seller_order_confirm') * 60;
	    }
	    $order->pay_end_time 	= UTC_TIME + (int)SystemConfigService::getConfigByCode('system_order_pass') * 60;

	    //订单创建时间
	    $order->create_time = UTC_TIME;
	    $order->create_day 	= UTC_DAY;

	    //是否到店服务
	    if($isToStore){ 
		    //查询服务人员信息
		    $seller = Seller::where('id',$staff->seller_id)->first();

		    //填充预约码
			if(OPERATION_VERSION != 'personal' && $seller->type > 1 )
            {
	    		$order->is_to_store = $isToStore;
	    		do{ 
					$code = Helper::getCode(0,8);
					$order_info = Order::where('reservation_code',$code)->first();
				}while($order_info);
	    		$order->reservation_code = $code;
	    	}
	    }
	    DB::beginTransaction();
	    do {
	    	try {
	    		$order->sn = Helper::getSn();
	    		$order->save();
	    		$bln = true;
	    	} catch (Exception $e) {
	    		$bln = false;
	    	}
	    } while(!$bln);

	    try {
	    	//更新服务人员的已预约时间
	    	SellerStaffService::setAppointHour($staff->id, $staff->seller_id, $appointTime, $goods->duration, $order->id);

	    	//更新服务人员的订单数量
	    	SellerStaffService::incrementExtend($staff->id, 'order_count');

	    	//更新服务销售数量 
	    	GoodsService::incrementField($goods->id, 'sales_volume');

	    	//如果有优惠
	    	if ($order->discount_fee > 0) {
	    		//将优惠券设为已使用
		    	$promotionSn->use_time = UTC_TIME;
		    	$promotionSn->status   = 1;
		    	$promotionSn->save();

		    	//写入订单优惠
	    		$orderPromotion = new OrderPromotion;
	    		$orderPromotion->order_id 		 = $order->id;
	    		$orderPromotion->seller_id 		 = $order->seller_id;
	    		$orderPromotion->user_id 		 = $order->user_id;
	    		$orderPromotion->promotion_id 	 = $promotionSn->promotion_id;
	    		$orderPromotion->promotion_sn_id = $promotionSn->id;
	    		$orderPromotion->discount_fee 	 = $order->discount_fee;
	    		$orderPromotion->promotion_name  = $order->promotion_info;
	    		$orderPromotion->save();
		    }

	    	DB::commit();
	    } catch (Exception $e) {
    		DB::rollback();
    		$result['code'] = 60013;
    	}
    	$result['data'] = $order->with('staff', 'seller', 'goods', 'promotion')->find($order->id)->toArray();
	    return $result;
	}
 

}
