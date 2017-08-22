<?php 
namespace YiZan\Services\System;

use YiZan\Models\System\Seller;

use YiZan\Utils\Time;
use DB, Lang;

class SellerCountService extends \YiZan\Services\SellerService 
{

    /**
     * [total 服务人员统计]
     * @param  [type] $beginTime [开始时间]
     * @param  [type] $endTime   [结束时间]
     * @return [type]            [description]
     */
    public static function total($beginTime, $endTime) {
        $data = array(
            'code' => 0,
            'data' => array(),
            'msg' => ''
        );
        $begin_time = !empty($beginTime) ? Time::toTime($beginTime) : UTC_DAY - 14 * 24 * 3600;
        $end_time = !empty($endTime) ? Time::toTime($endTime) + 24 * 3600 : UTC_DAY + 24 * 3600;
        $diff_day =($end_time - $begin_time) / 86400;
        if ($diff_day > 15 || $diff_day < 1) {
           $data['code'] = 19999;
           $data['msg'] = '时间段必须为1-15天'; 
           return $data;
        }
        $not_total_num = 0; //待审核人数
        $total_num = 0; //已审核人数

        for ($i = 0; $i < $diff_day; $i++) {
            $day = $begin_time + $i * 24 * 3600;
            $time[$i] = Time::toDate($day, 'Y-m-d');
            $day_time = Time::toDate($day, 'd');
            $total[$day_time] = 0;
            $not_total[$day_time] = 0;
        }
        //通过审核人数
        $result = DB::table('seller')->where('create_time', '>=', $begin_time)
                        ->where('create_time', '<', $end_time)
                        ->where('status', '1')
                        ->groupBy('create_day')
                        ->select(DB::raw('count(id) as total'),'create_day')
                        ->get();
        foreach ($result as $v) {
            $day = Time::toDate($v->create_day, 'd');
            $total[$day] = $v->total;
            $total_num += $v->total;
        }

        //申请人数
        $result = DB::table('seller')->where('create_time', '>=', $begin_time)
                        ->where('create_time', '<', $end_time)
                        ->where('status', '0')
                        ->groupBy('create_day')
                        ->select(DB::raw('count(id) as total'),'create_day')
                        ->get();
        foreach ($result as $v) {
            $day = Time::toDate($v->create_day, 'd');
            $not_total[$day] = $v->total;
            $not_total_num += $v->total;
        }
        //今日申请人数
        $today_num = DB::table('seller')->where('create_time', '>=', UTC_DAY)
                        ->where('create_time', '<', UTC_DAY + 24 * 3600)
                        ->where('status', '0')
                        ->count();
        //历史申请人数
        $total_num_all = DB::table('seller')->count();

        $data['data'] = array(
            'todayNum' => $today_num,
            'totalNum' => $total_num_all,
            'time' => $time,
            'data' => array(
                    array('name'=>'申请人数','total'=>$not_total_num,'val'=>$not_total),
                    array('name'=>'通过审核人数','total'=>$total_num,'val'=>$total),
                ),
        );
        return $data;

    }
}
