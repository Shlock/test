<?php 
namespace YiZan\Services\System;

use YiZan\Models\System\Order;

use YiZan\Utils\Time;
use DB, Lang;

class OrderCountService extends \YiZan\Services\OrderService 
{

    /**
     * [total 订单统计概况]
     * @param  [int] $type [类型 1:今天 2:昨天 3:本周 4:本月]
     * @return [type]       [description]
     */
    public static function total($type) {
        //开始时间与结束时间
        switch ($type) {
            case '2'://昨天
                $end_time = UTC_DAY;
                $begin_time = $end_time - 24 * 3600;
                break;

            case '3'://本周
                $begin_time = Time::getWeekFirstDay();
                $end_time = Time::getWeekLastDay()+1;
                break;

            case '4'://本月
                $begin_time = Time::getMonthFirstDay();
                $end_time = Time::getMonthLastDay()+1;
                break;

            default://默认今天
                $begin_time = UTC_DAY;
                $end_time = $begin_time + 24 * 3600;
                break;
        }

        $list = array();
        if ($type >= 0 && $type < 3) {//查询的时间为今天或者昨天的数据
            $result = DB::table('order')->where('create_time', '>=', $begin_time)
                ->where('create_time', '<', $end_time)
                ->select(DB::raw('count(id) as total,sum(pay_fee) as money,FROM_UNIXTIME(app_time + 8*3600,"%H") as appoint_hour'))
                ->groupBy('appoint_hour')
                ->get();

            $total_num = 0;
            $money_num = 0;
            for ($i = 0; $i <= 23; $i++) {
                $time[$i] = $i < 10 ? '0'.$i.':00' : $i.':00';
                $total[$i] = 0;
                $money[$i] = 0;
            }

            foreach ($result as $k=>$v) {
                $appHour = (int)$v->appoint_hour;
                $total[$appHour] = $v->total;
                $money[$appHour] = $v->money;
                $total_num += $v->total;
                $money_num += $v->money;
            }

        } else {//本周或本月的统计数据

            $result = DB::table('order')->where('create_time', '>=', $begin_time)
                ->where('create_time', '<', $end_time)
                ->groupBy('create_day')
                ->select(DB::raw('count(id) as total,sum(pay_fee) as money'),'create_day')
                ->get();

            $total_num = 0;
            $money_num = 0;

            $max_num = $type == 4 ? Time::toDate($begin_time,'t') : 7;
            for ($i = 0; $i < $max_num; $i++) {
                $day = $begin_time + $i * 24 * 3600;
                $time[$i] = Time::toDate($day, 'Y-m-d');
                $day_time = Time::toDate($day, 'd');
                $total[$day_time] = 0;
                $money[$day_time] = 0;
            }

            foreach ($result as $k=>$v) {
                $day = Time::toDate($v->create_day, 'd');
                $total[$day] = $v->total;
                $money[$day] = $v->money;
                $total_num += $v->total;
                $money_num += $v->money;
            }

        }
        $list = array(
            'time' => $time,
            'data' => array(
                array('name'=>'订单金额','total'=>$money_num,'val'=>$money),
                array('name'=>'订单数','total'=>$total_num,'val'=>$total),
            ),
        );
        return $list;
    }
    /**
     * [getOrderNumTotal 订单数量统计]
     * @param  [type] $beginTime [开始时间]
     * @param  [type] $endTime   [结束时间]
     * @return [type]            [description]
     */
    public static function getOrderNumTotal($beginTime, $endTime) {
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
        $not_total_num = 0; //未成交订单初始数值
        $total_num = 0; //已成交订单初始数值

        for ($i = 0; $i < $diff_day; $i++) {
            $day = $begin_time + $i * 24 * 3600;
            $time[$i] = Time::toDate($day, 'Y-m-d');
            $day_time = Time::toDate($day, 'd');
            $total[$day_time] = 0;
            $not_total[$day_time] = 0;
        }
        //已完成订单数
        $result = DB::table('order')->where('create_time', '>=', $begin_time)
                        ->where('create_time', '<', $end_time)
                        ->whereIn('status', [
                            ORDER_STATUS_FINISH_SYSTEM,
                            ORDER_STATUS_FINISH_USER,
                            ORDER_STATUS_REFUND_FAIL,
                            ORDER_STATUS_REFUND_SUCCESS,
                            ORDER_STATUS_CANCEL_ADMIN,
                            ORDER_STATUS_USER_DELETE,
                            ORDER_STATUS_CANCEL_SELLER,
                            ORDER_STATUS_CANCEL_AUTO,
                            ORDER_STATUS_CANCEL_USER
                        ])->groupBy('create_day')
                        ->select(DB::raw('count(id) as total'),'create_day')
                        ->get();
        foreach ($result as $v) {
            $day = Time::toDate($v->create_day, 'd');
            $total[$day] = $v->total;
            $total_num += $v->total;
        }

        //未完成订单数
        $result = DB::table('order')->where('create_time', '>=', $begin_time)
                        ->where('create_time', '<', $end_time)
                        ->whereIn('status',  [
                            ORDER_STATUS_BEGIN_USER,
                            ORDER_STATUS_PAY_SUCCESS,
                            ORDER_STATUS_PAY_DELIVERY,
                            ORDER_STATUS_AFFIRM_SELLER,
                            ORDER_STATUS_FINISH_STAFF,
                            ORDER_STATUS_REFUND_AUDITING,
                            ORDER_STATUS_CANCEL_REFUNDING,
                            ORDER_STATUS_REFUND_HANDLE
                        ])->groupBy('create_day')
                        ->select(DB::raw('count(id) as total'),'create_day')
                        ->get();
        foreach ($result as $v) {
             $day = Time::toDate($v->create_day, 'd');
            $not_total[$day] = $v->total;
            $not_total_num += $v->total;
        }
        //今日订单数
        $today_num = DB::table('order')->where('create_time', '>=', UTC_DAY)
                        ->where('create_time', '<', UTC_DAY + 24 * 3600)
                        ->count();
        //历史订单数
        $total_num_all = DB::table('order')->count();

        $data['data'] = array(
            'todayNum' => $today_num,
            'totalNum' => $total_num_all,
            'time' => $time,
            'data' => array(
                    array('name'=>'未完成订单数','total'=>$not_total_num,'val'=>$not_total),
                    array('name'=>'完成订单数','total'=>$total_num,'val'=>$total),
                ),
        );
        return $data;

    }
}
