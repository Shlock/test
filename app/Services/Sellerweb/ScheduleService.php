<?php 
namespace YiZan\Services\Sellerweb;

use YiZan\Models\Seller\Order;
use YiZan\Models\Seller\SellerAppointHour;
    use YiZan\Models\Sellerweb\StaffAppoint;

use YiZan\Services\SellerService;

use YiZan\Utils\Time;
use DB, Lang;

class ScheduleService extends \YiZan\Services\BaseService {
	

	
	/**
	 * [getList 卖家最近4天日程列表]
	 * @param  [int] $sellerId [卖家编号]
	 * @return [array] $list          [description]
	 */
	public static function getFourDayList($sellerId) {
		$list = array();
		$time = UTC_DAY;
		$end_time = UTC_DAY + 4 * 24 * 60 * 60 - 1;
		$weekday = array('日','一','二','三','四','五','六');
		$appoint_hour = SellerAppointHour::where('seller_id', $sellerId)
					->where('day', '>=', $time)
					->where('day', '<=', $end_time)
					->get();
		//获取近4天的订单列表			
		$order_fields = array('id','appoint_day','appoint_hour','goods_name','user_name','mobile','address');
		$order_lists = Order::where('seller_id', $sellerId)
					->where('appoint_day', '>=', $time)
					->where('appoint_day', '<=', $end_time)
					->get($order_fields)->toArray();
		foreach($order_lists as $key=>$val) {
			$order_list[$val['appointDay']][$val['appointHour']] = $val;
		}
		//卖家预约时间数据有的时候,返回表中数据
		foreach ($appoint_hour as $k=>$v) {
			$day = Time::toDate($v->day, 'Ymd');
			$list[$day] = array(
				'day' => Time::toDate($v->day, 'Ymd'),
				'week' => $weekday[\YiZan\Utils\Time::toDate($v->day, "w")],
			);
			for ($i = 0; $i <= 23; $i++) {
				$hour = $i.':00';
				$field = 'hour'.$i;
				$list[$day]['hour'][$i] = array(
					'hour'		=> $hour,
					'status' 	=> $v->$field,
					'goodName' 	=> NULL,
					'userName' 	=> NULL,
					'mobile' 	=> NULL,
					'address' 	=> NULL,
					'orderId' 	=> NULL
				);

				//有预约的时候匹配订单消息
				if ($v->$field == '1' && isset($order_list[$v->day][$i])) {
					$order_info = $order_list[$v->day][$i];
					$list[$day]['hour'][$i]['goodName'] = $order_info['goodsName'];
					$list[$day]['hour'][$i]['userName'] = $order_info['userName'];
					$list[$day]['hour'][$i]['mobile'] = $order_info['mobile'];
					$list[$day]['hour'][$i]['address'] = $order_info['address'];
					$list[$day]['hour'][$i]['orderId'] = $order_info['id'];
				}
				
			}
		}
		//当表中无预约时间数据,返回默认数据
		for ($time; $time <= $end_time; $time += 86400) {
			$day = Time::toDate($time, 'Ymd');
			if (!isset($list[$day])) {
				$list[$day] = array(
					'day' => Time::toDate($time, 'Ymd'),
					'week' => $weekday[\YiZan\Utils\Time::toDate($time, "w")],
				);
				for ($i = 0; $i <= 23; $i++) {
					$hour = $i.':00';
					$list[$day]['hour'][$i] = array(
						'hour'		=> $hour,
						'status' 	=> 0,
						'goodName' 	=> NULL,
						'userName' 	=> NULL,
						'mobile' 	=> NULL,
						'address' 	=> NULL,
						'orderId' 	=> NULL
					);
				}
			}
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
	public static function updateStatus($hours, $status, $sellerId) {;
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
		try {
			foreach ($hours as $v) {
				$daytime = Time::toTime(substr($v, 0, 8));
				$hour = 'hour'.(int)substr($v, 8, 2);
				$appoint_hour = SellerService::getAppointHourByDay($sellerId,$daytime);
				$appoint_hour->$hour = $status;
				$appoint_hour->save();
			}

			DB::commit();
			return $result;
		} catch (Exception $e) {
			DB::rollback();
			$result['code'] = 40003;
    		return $result;
		}

	}

    /**
     * [getStaffDayList 获取员工某一天日程列表]
     * @param  [int] $staffId [员工编号]
     * @param  [int] $date [员工编号]
     * @return [array] $list          [description]
     */
    public static function getStaffDayList($staffId, $date)
    {
        if ($staffId < 1)
        {
            return [];
        }

        $hours = [];

        $beginTime = Time::toTime($date);

        $endTime = $beginTime + 24 * 60 * 60 - 1;

        $appoint = StaffAppoint::where('staff_id', $staffId)
            ->whereBetween('appoint_time', [$beginTime, $endTime])
            ->select('appoint_time', 'status')
            ->get();

        foreach($appoint as $value)
        {
            $hour = Time::toDate($value->appoint_time, 'H:i');

            $iHour = (int)Time::toDate($value->appoint_time, 'H');

            if($iHour * 3600 >= StaffAppoint::DEFAULT_BEGIN_ORDER_DATE &&
                $iHour * 3600 <= StaffAppoint::DEFAULT_END_ORDER_DATE)
            {
                $hours[$hour] =
                    [
                        'hour'		=> $hour,
                        'status' 	=> $value->status
                    ];
            }
        }

        //当表中无预约时间数据,返回默认数据
        for (; $beginTime <= $endTime; $beginTime += StaffAppoint::SERVICE_SPAN)
        {
            $iHour = (int)Time::toDate($beginTime, 'H');

            if($iHour * 3600 >= StaffAppoint::DEFAULT_BEGIN_ORDER_DATE &&
                $iHour * 3600 <= StaffAppoint::DEFAULT_END_ORDER_DATE)
            {
                $hour = Time::toDate($beginTime, 'H:i');

                if(array_key_exists($hour, $hours) == false)
                {
                    $hours[$hour] =
                        [
                            'hour'		=> $hour,
                            'status' 	=> StaffAppoint::ACCEPT_APPOINT_STATUS
                        ];
                }
            }
        }

        ksort($hours);

        return ['day' => Time::toTime($date), 'hours' => array_values($hours)];
    }

    /**
     * [updateStaffStatus 更新员工接单状态]
     * @param  [array] $hours  [小时列表]
     * @param  [int] $status [状态]
     * @param  [int] $staffId [员工编号]
     * @return [array] $result        [结果]
     */
    public static function updateStaffStatus($hours, $status, $staffId)
    {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg' => Lang::get('api_system.success.schedule_update')
        );
        if ($staffId < 1) {
            $result['code'] = 50301;
            return $result;
        }
        if (empty($hours)) {
            $result['code'] = 50303;
            return $result;
        }
        if ($status != 0 && $status != '-1' && count($hours) != count($status)) {
            $result['code'] = 50302;
            return $result;
        }

        DB::beginTransaction();

        try
        {
            foreach ($hours as $k=>$v)
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

                        $result['code'] = 50304;

                        return $result;
                    }

                    StaffAppoint::where('staff_id', $staffId)
                        ->where("appoint_time", $daytime)
                        ->update(["status"=>$status[$k]]);
                }
                else /* 新增 */
                {
                    $appoint = new StaffAppoint();

                    $appoint->staff_id = $staffId;
                    $appoint->appoint_day = $day;
                    $appoint->appoint_time = $daytime;
                    $appoint->status = $status[$k];

                    $appoint->save();
                }
            }

            DB::commit();

            return $result;
        }
        catch (Exception $e)
        {
            DB::rollback();
            $result['code'] = 50304;
            return $result;
        }




    }
}
