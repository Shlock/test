<?php namespace YiZan\Services\Staff;

use YiZan\Models\Staff\Order;
use YiZan\Models\Staff\StaffAppoint;

use YiZan\Services\StaffService;

use YiZan\Utils\Time;
use DB, Lang;

class ScheduleService extends \YiZan\Services\BaseService 
{
	/**
	 * [getList 卖家最近4天日程列表]
	 * @param  [int] $staffId [卖家编号]
	 * @return [array] $list          [description]
	 */
	public static function getFourDayList($staffId)
    {
		$list = [];
        
		$beginTime = UTC_DAY;
        
		$endTime = $beginTime + 4 * 24 * 60 * 60 - 1;
        
		$weekday = array('日','一','二','三','四','五','六');
        
		$appoint = StaffAppoint::where('staff_appoint.staff_id', $staffId)
            ->leftJoin('order', 'order.id', '=', 'staff_appoint.order_id')
            ->leftJoin('user', 'user.id', '=', 'order.user_id')
		    ->whereBetween('staff_appoint.appoint_time', [$beginTime, $endTime])
            ->select('user.name AS user_name', 'order.goods_name', 'order.mobile', 'order.address', 'staff_appoint.order_id', 'staff_appoint.appoint_time', 'staff_appoint.status')
		    ->get();
        
        foreach($appoint as $value)
        {
            $iHour = (int)Time::toDate($value->appoint_time, 'H');
            
            if($iHour * 3600 >= StaffAppoint::DEFAULT_BEGIN_ORDER_DATE &&
                $iHour * 3600 <= StaffAppoint::DEFAULT_END_ORDER_DATE)
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
                
                if($value->status == StaffAppoint::HAVING_APPOINT_STATUS)
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
		for (; $beginTime <= $endTime; $beginTime += StaffAppoint::SERVICE_SPAN)
        {
            $iHour = (int)Time::toDate($beginTime, 'H');
            
            if($iHour * 3600 >= StaffAppoint::DEFAULT_BEGIN_ORDER_DATE &&
                $iHour * 3600 <= StaffAppoint::DEFAULT_END_ORDER_DATE)
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
                        'status' 	=> StaffAppoint::ACCEPT_APPOINT_STATUS
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
	 *  * @param  [int] $staffId [卖家编号]
	 * @return [array] $result        [结果]
	 */
	public static function updateStatus($hours, $status, $staffId) 
    {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg' => Lang::get('api_staff.success.schedule_update')
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
                
                $appoint = StaffAppoint::where('staff_id', $staffId)
                    ->where("appoint_time", $daytime)
                    ->first();
                
                // 更新
                if($appoint == true)
                {
                    // 有订单不允许更改的
                    if($appoint->status == StaffAppoint::HAVING_APPOINT_STATUS)
                    {
                        DB::rollback();
                        
                        $result['code'] = 40003;
                        
                        return $result;
                    }
                    
                    StaffAppoint::where('staff_id', $staffId)
                        ->where("appoint_time", $daytime)
                        ->update(["status"=>$status]);
                }
                else /* 新增 */
                {
                    $appoint = new StaffAppoint();
                    
                    $appoint->staff_id = $staffId;
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
