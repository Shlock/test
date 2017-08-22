<?php namespace YiZan\Services\System;

use YiZan\Models\System\SellerAppoint;
use YiZan\Models\System\StaffAppoint;
use YiZan\Services\SellerService;

use YiZan\Utils\Time;
use DB, Lang;

class ScheduleService extends \YiZan\Services\BaseService 
{
	/**
	 * [getList 获取卖家某一天日程列表]
	 * @param  [int] $sellerId [卖家编号]
	 * @return [array] $list          [description]
	 */
	public static function getDayList($sellerId, $date) 
    {
		if ($sellerId < 1) 
        {
			return [];
		}
        
        $hours = [];
        
		$beginTime = Time::toTime($date);
        
		$endTime = $beginTime + 24 * 60 * 60 - 1;
                
		$appoint = SellerAppoint::where('seller_id', $sellerId)
		    ->whereBetween('appoint_time', [$beginTime, $endTime])
            ->select('appoint_time', 'status')
		    ->get();
        
        foreach($appoint as $value)
        {
            $hour = Time::toDate($value->appoint_time, 'H:i');
            
            $iHour = (int)Time::toDate($value->appoint_time, 'H');
            
            if($iHour * 3600 >= SellerAppoint::DEFAULT_BEGIN_ORDER_DATE &&
                $iHour * 3600 <= SellerAppoint::DEFAULT_END_ORDER_DATE)
            {
                $hours[$hour] = 
                [
                    'hour'		=> $hour,
                    'status' 	=> $value->status
                ];
            }
        }
        
		//当表中无预约时间数据,返回默认数据
		for (; $beginTime <= $endTime; $beginTime += SellerAppoint::SERVICE_SPAN)
        {
            $iHour = (int)Time::toDate($beginTime, 'H');
            
            if($iHour * 3600 >= SellerAppoint::DEFAULT_BEGIN_ORDER_DATE &&
                $iHour * 3600 <= SellerAppoint::DEFAULT_END_ORDER_DATE)
            {
                $hour = Time::toDate($beginTime, 'H:i');
                
                if(array_key_exists($hour, $hours) == false)
                {
                    $hours[$hour] = 
                    [
                        'hour'		=> $hour,
                        'status' 	=> SellerAppoint::ACCEPT_APPOINT_STATUS
                    ];
                }
            }
		}

		ksort($hours);
        
		return ['day' => Time::toTime($date), 'hours' => array_values($hours)];
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
			'msg' => Lang::get('api_system.success.schedule_update')
		);
		if ($sellerId < 1) {
			$result['code'] = 40101; 
			return $result;
		}
		if (empty($hours)) {
			$result['code'] = 40103; 
			return $result;
		}
		if ($status != 0 && $status != '-1' && count($hours) != count($status)) {
			$result['code'] = 40102; 
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
                        ->update(["status"=>$status[$k]]);
                }
                else /* 新增 */
                {
                    $appoint = new SellerAppoint();
                    
                    $appoint->seller_id = $sellerId;
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
			$result['code'] = 40003;
    		return $result;
		}




	}


    /**
     * [getStaffDayList 获取员工某一天日程列表]
     * @param  [int] $staffId [员工编号]
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
