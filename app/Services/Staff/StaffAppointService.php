<?php namespace YiZan\Services\Staff;
use YiZan\Models\Staff\StaffAppoint;
use YiZan\Utils\Time;
use Lang, DB;
class StaffAppointService extends \YiZan\Services\BaseService {

    /**
     * [getStaffDayList 获取员工某一天日程列表]
     * @param  [int] $staffId [员工编号]
     * @param  [int] $date [日期]
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
            ->select('appoint_time', 'status', 'is_leave')
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
                        'hour'	=> $hour,
                        'status' 	=> $value->status,
                        'isLeave'   => $value->is_leave
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
                            'hour'	=> $hour,
                            'status' 	=> StaffAppoint::ACCEPT_APPOINT_STATUS,
                            'isLeave'   => '0'
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
    public static function updateStaffStatus($staffId, $hours, $status)
    {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg' => Lang::get('api_system.success.schedule_update')
        );

        if (count($hours) < 1 || !is_array($hours)) {
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
                    // 有订单或请假不允许更改的
                    if($appoint->status == StaffAppoint::HAVING_APPOINT_STATUS || $appoint->is_leave = '1')
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
