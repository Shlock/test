<?php namespace YiZan\Services\Seller;

use YiZan\Models\Seller\Order;
use YiZan\Models\Seller\SellerAppoint;

use YiZan\Services\SellerService;

use YiZan\Utils\Time;
use DB, Lang;

class ScheduleService extends \YiZan\Services\BaseService 
{
	/**
	 * [getList 卖家最近4天日程列表]
	 * @param  [int] $sellerId [卖家编号]
	 * @return [array] $list          [description]
	 */
	public static function getFourDayList($sellerId)
    {
		$list = [];
        
		$beginTime = UTC_DAY;
        
		$endTime = $beginTime + 4 * 24 * 60 * 60 - 1;
        
		$weekday = array('日','一','二','三','四','五','六');
        
		$appoint = SellerAppoint::where('seller_appoint.seller_id', $sellerId)
            ->leftJoin('order', 'order.id', '=', 'seller_appoint.order_id')
            ->leftJoin('user', 'user.id', '=', 'order.user_id')
		    ->whereBetween('seller_appoint.appoint_time', [$beginTime, $endTime])
            ->select('user.name AS user_name', 'order.goods_name', 'order.mobile', 'order.address', 'seller_appoint.order_id', 'seller_appoint.appoint_time', 'seller_appoint.status')
		    ->get();
        
        foreach($appoint as $value)
        {
            $iHour = (int)Time::toDate($value->appoint_time, 'H');
            
            if($iHour * 3600 >= SellerAppoint::DEFAULT_BEGIN_ORDER_DATE &&
                $iHour * 3600 <= SellerAppoint::DEFAULT_END_ORDER_DATE)
            {
                $day = Time::toDate($value->appoint_time, 'Ymd');
                
                if(array_key_exists($day, $list) == false)
                {
                    $list[$day] = 
                    [
                        'day' => $day,
                        'week' => $weekday[\YiZan\Utils\Time::toDate($value->appoint_time, "w")],
                        'hour' => []
                    ];
                }
                
                $hour = Time::toDate($value->appoint_time, 'H:i');
                
                if($value->status == SellerAppoint::HAVING_APPOINT_STATUS)
                {
                    $list[$day]['hour'][$hour] = 
                    [
                        'hour'		=> $hour,
                        'status' 	=> $value->status,
                        'goodName' 	=> $value->goods_name,
                        'userName' 	=> $value->user_name,
                        'mobile' 	=> $value->mobile,
                        'address' 	=> $value->address,
                        'orderId' 	=> $value->order_id
                    ];
                }
                else
                {
                    $list[$day]['hour'][$hour] = 
                    [
                        'hour'		=> $hour,
                        'status' 	=> $value->status
                    ];
                }
            }
        }

		//当表中无预约时间数据,返回默认数据
		for (; $beginTime <= $endTime; $beginTime += SellerAppoint::SERVICE_SPAN)
        {
            $iHour = (int)Time::toDate($beginTime, 'H');
            
            if($iHour * 3600 >= SellerAppoint::DEFAULT_BEGIN_ORDER_DATE &&
                $iHour * 3600 <= SellerAppoint::DEFAULT_END_ORDER_DATE)
            {
                $day = Time::toDate($beginTime, 'Ymd');
                
                if(array_key_exists($day, $list) == false)
                {
                    $list[$day] = 
                    [
                        'day' => $day,
                        'week' => $weekday[\YiZan\Utils\Time::toDate($beginTime, "w")],
                        'hour' => []
                    ];
                }
                
                $hour = Time::toDate($beginTime, 'H:i');
                
                if(array_key_exists($hour, $list[$day]['hour']) == false)
                {
                    $list[$day]['hour'][$hour] = 
                    [
                        'hour'		=> $hour,
                        'status' 	=> SellerAppoint::ACCEPT_APPOINT_STATUS
                    ];
                }
            }
        }
        
        foreach($list as $day=>$value)
        {
            ksort($list[$day]['hour']);
            
            $list[$day]['hour'] = array_values($list[$day]['hour']);
        }
        
		ksort($list);
        
		return array_values($list);
	}


	/**
	 * [updateStatus 更新接单状态]
	 * @param  [array] $hours  [小时列表]
	 * @param  [int] $status [状态]
	 *  * @param  [int] $sellerId [卖家编号]
	 * @return [array] $result        [结果]
	 */
	public static function updateStatus($hours, $status, $sellerId) 
    {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg' => Lang::get('api_seller.success.schedule_update')
		);
		if (empty($hours)) {
			$result['code'] = 40002; 
			return $result;
		}
        
        $status -= 1;
        
		if ($status != 0 && $status != -1) 
        {
			$result['code'] = 40001; 
			return $result;
		}
        
		DB::beginTransaction();
        
		try 
        {
			foreach ($hours as $v) 
            {
                $day = Time::toTime(substr($v, 0, 8));
                
				$hour = (int)substr($v, 8, 2);
                
                $daytime = $day + $hour * 3600;
                
                $appoint = SellerAppoint::where('seller_id', $sellerId)
                    ->where("appoint_time", $daytime)
                    ->first();
                
                // 更新
                if($appoint == true)
                {
                    // 有订单不允许更改的
                    if($appoint->status == SellerAppoint::HAVING_APPOINT_STATUS)
                    {
                        DB::rollback();
                        
                        $result['code'] = 40003;
                        
                        return $result;
                    }
                    
                    SellerAppoint::where('seller_id', $sellerId)
                        ->where("appoint_time", $daytime)
                        ->update(["status"=>$status]);
                }
                else /* 新增 */
                {
                    $appoint = new SellerAppoint();
                    
                    $appoint->seller_id = $sellerId;
                    $appoint->appoint_day = $day;
                    $appoint->appoint_time = $daytime;
                    $appoint->status = $status;
                    
                    $appoint->save();
                }
			}

			DB::commit();
            
			return $result;
		} 
        catch (Exception $e) 
        {
			DB::rollback();
			$result['code'] = 40003;
    		return $result;
		}
	}
}
