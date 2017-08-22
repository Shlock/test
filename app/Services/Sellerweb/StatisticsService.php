<?php
namespace YiZan\Services\Sellerweb;
use YiZan\Models\OrderGoods;
use YiZan\Models\Sellerweb\StatisticsDaily;
use YiZan\Models\Sellerweb\StatisticsData;
use YiZan\Services\OrderService;
use YiZan\Utils\Time;
use DB, Lang;
use YiZan\Models\Order;
class StatisticsService extends \YiZan\Services\BaseService {

    /**
     * 订单统计
     * @return [type] [description]
     */
    public static function orderCount($sellerId){
        $result = [];
        //已完成
        $confirm = StatisticsDaily::where('seller_id',$sellerId)
            ->whereIn('status',[
                ORDER_STATUS_FINISH_SYSTEM,
                ORDER_STATUS_FINISH_USER,
                ORDER_STATUS_USER_DELETE
            ])->where(function($query){
                $query->where('buyer_finish_time', '>', 0)
                    ->orWhereBetween('auto_finish_time', [1,UTC_TIME]);
            })
            ->selectRaw("count(*) as num , order_type as type")
            ->groupBy('type')
            ->lists('num','type');
        $result['comfirm'] = [
            '1' => (int)$confirm[1],
            '2' => (int)$confirm[2]
        ];
        //待发货
        $unfinished = StatisticsDaily::where('seller_id',$sellerId)
            ->whereIn('status',
                [
                    ORDER_STATUS_BEGIN_USER,
                    ORDER_STATUS_PAY_SUCCESS,
                    ORDER_STATUS_PAY_DELIVERY,
                    ORDER_STATUS_AFFIRM_SELLER
                ])
            ->selectRaw("count(*) as num , order_type as type")
            ->groupBy('type')
            ->lists('num','type');

        $result['unfinished'] = [
            '1' => (int)$unfinished[1],
            '2' => (int)$unfinished[2]
        ];
        //待完成
        $unpay = StatisticsDaily::where('seller_id',$sellerId)
            ->whereIn('status',[
                ORDER_STATUS_FINISH_STAFF
            ])
            ->selectRaw("count(*) as num , order_type as type")
            ->groupBy('type')
            ->lists('num','type');

        $result['unpay'] = [
            '1' => (int)$unpay[1],
            '2' => (int)$unpay[2]
        ];
        return $result;
    }

    /**
     * 本日营业统计
     * @return [type] [description]
     */
    public static function today($sellerId){
        $today = UTC_DAY;
        $data_comfirm = StatisticsDaily::where('seller_id',$sellerId)
            ->whereIn('status',[
                ORDER_STATUS_FINISH_SYSTEM,
                ORDER_STATUS_FINISH_USER,
                ORDER_STATUS_USER_DELETE
            ])
            ->where(function($query){
                $query->where('buyer_finish_time', '>', 0)
                    ->orWhereBetween('auto_finish_time', [1,UTC_TIME]);
            })
            ->where('create_day',$today)
            ->groupBy('create_day')
            // ->selectRaw("count(*) as num,IFNULL(sum(total_fee),0) as trading ,IFNULL(sum(total_fee - staff_fee),0) as total , IFNULL(sum(duration),0) as durationint ")
            ->selectRaw("count(*) as num,IFNULL(sum(total_fee),0) as trading ,IFNULL(sum(seller_fee),0) as total , IFNULL(sum(duration),0) as durationint ")
            ->first();
        if ($data_comfirm) {
            $hour = (int)($data_comfirm->durationint/60);
            $minute = (int)($data_comfirm->durationint%60);
            $duration = $minute > 0 ? $hour.'小时'.$minute.'分' : $hour.'小时';
            $data_comfirm = $data_comfirm->toArray();
            $data_comfirm['duration'] = $duration;
        }

        $data_unfinished = StatisticsDaily::where('seller_id',$sellerId)
            ->whereIn('status',
                [
                    ORDER_STATUS_BEGIN_USER,
                    ORDER_STATUS_PAY_SUCCESS,
                    ORDER_STATUS_PAY_DELIVERY,
                    ORDER_STATUS_AFFIRM_SELLER,
                    ORDER_STATUS_FINISH_STAFF

                ])
            ->where('create_day',$today)
            ->groupBy('create_day')
            // ->selectRaw("count(*) as num,IFNULL(sum(total_fee),0) as trading ,IFNULL(sum(total_fee - staff_fee),0) as total , IFNULL(sum(duration),0) as durationint ")
            ->selectRaw("count(*) as num,IFNULL(sum(total_fee),0) as trading ,IFNULL(sum(seller_fee),0) as total , IFNULL(sum(duration),0) as durationint ")
            ->first();
        if ($data_unfinished) {
            $hour = (int)($data_unfinished->durationint/60);
            $minute = (int)($data_unfinished->durationint%60);
            $duration = $minute > 0 ? $hour.'小时'.$minute.'分' : $hour.'小时';
            $data_unfinished = $data_unfinished->toArray();
            $data_unfinished['duration'] = $duration;
        }


        $result['comfirm'] = $data_comfirm ? $data_comfirm : ['num'=>0,'trading'=>0,'total'=>0,'duration'=>0, 'durationint' => 0];
        $result['unfinished'] = $data_unfinished ? $data_unfinished : ['num'=>0,'trading'=>0,'total'=>0,'duration'=>0, 'durationint' => 0];

        $data_total = [
            'num' => (int)($result['comfirm']['num'] + $result['unfinished']['num']),
            'trading' => $result['comfirm']['trading'] + $result['unfinished']['trading'],
            'total' => $result['comfirm']['total'] + $result['unfinished']['total']
        ];
        $total_duration = $result['comfirm']['durationint'] + $result['unfinished']['durationint'];
        $hour = (int)($total_duration/60);
        $minute = (int)($total_duration%60);
        $data_total['duration'] = $minute > 0 ? $hour.'小时'.$minute.'分' : $hour.'小时';
        $result['total'] = $data_total;
        return $result;
    }

    /**
     * 收入统计
     * @return [type] [description]
     */
    public static function income($sellerId,$beginDate,$endDate,$type){
        $result = [];			//返回的结果集
        $data = [];  			//未分组的数据
        $data_x = [];			//X轴数据
        $data_y_num = [];		//Y轴订单数量
        $data_y_total = [];		//Y轴订单实际交易额
        $data_y_trading = [];	//Y轴订单账面交易额
        //如果设置了开始时间，结束时间查询这个时间段的所有数据，如果只设置了开始时间或者结束时间 则倒推15天内数据
        if(!empty($beginDate) || !empty($endDate)){
            $beginTime = Time::toTime($beginDate);
            $endTime = Time::toTime($endDate);
            if(empty($endDate) && !empty($beginDate)){
                $endTime = $beginTime + 15 * 3600 * 24;
            } else if(!empty($endDate) && empty($beginDate)){
                $beginTime = $endTime - 15 * 3600 * 24;
            }
            if($beginTime > $endTime){
                $tempTime = $beginTime;
                $beginTime = $endTime;
                $endTime = $beginTime;
            }
            $rs = ($endTime - $beginTime)/(3600 * 24);

            $query_data = StatisticsData::where('seller_id',$sellerId)
                ->whereIn('status',[ORDER_STATUS_FINISH_SYSTEM, ORDER_STATUS_FINISH_USER])
                ->whereBetween('create_day',array($beginTime,$endTime))
                ->groupBy('create_day')
                ->selectRaw("count(*) as num,sum(total_fee) as trading ,sum(total_fee - freight) as total,FROM_UNIXTIME(create_day,'%Y-%m-%d') as date")
                ->get();

            $query_data = $query_data->toArray();
            for ($i = 0; $i <= $rs; $i++) {
                $time = Time::toDate(($beginTime + $i * 3600 * 24),'Y-m-d');
                $data_x[] = $time;
                $flag = [];

                foreach ($query_data as $value) {
                    if($value['date'] ==  $time){
                        $flag = $value;
                        break;
                    }
                }

                if(!empty($flag)){
                    $data[$time] = array('num'=>$flag['num'],'total'=>$flag['total'],'trading'=>$flag['trading']);
                    $data_y_trading[] = $flag['trading'];
                    $data_y_num[] = $flag['num'];
                    $data_y_total[] = $flag['total'];
                } else {
                    $data[$time] = array('num'=>0,'total'=>0,'trading'=>0);
                    $data_y_trading[] = 0;
                    $data_y_num[] = 0;
                    $data_y_total[] = 0;
                }
            }
        } else {
            if($type <= 1){
                $query_data = StatisticsData::where('seller_id',$sellerId)
                    ->where('create_day',$type == 0 ? Time::getNowDay() : Time::getNowDay() - 3600 * 24 )
                    ->whereIn('status',[ORDER_STATUS_FINISH_SYSTEM, ORDER_STATUS_FINISH_USER])
                    ->groupBy('date')
                    ->selectRaw("count(*) as num ,sum(total_fee) as trading ,sum(total_fee - freight) as total , FROM_UNIXTIME(create_time+8*3600,'%H') as date")
                    ->get();
                $query_data = $query_data->toArray();
                for($i = 0 ;$i < 24;$i++){
                    $data_x[] = $i;
                    $flag = [];
                    foreach ($query_data as $value) {
                        if((int)$value['date'] ==  $i){
                            $flag = $value;
                            break;
                        }
                    }
                    if(!empty($flag)){
                        $data[$i] = array('num'=>$flag['num'],'total'=>$flag['total'],'date'=>$flag['date']);
                        $data_y_num[] = $flag['num'];
                        $data_y_total[] = $flag['total'];
                        $data_y_trading[] = $flag['trading'];
                    } else {
                        $data[$i] = array('num'=>0,'total'=>0,'trading'=>0);
                        $data_y_num[] = 0;
                        $data_y_total[] = 0;
                        $data_y_trading[] = 0;
                    }
                }
            } else {//当最小时间为天数据的时候
                $start_time = 0;
                $end_time = Time::getNowDay() + 24 * 3600;
                if($type == 7){
                    $start_time = Time::getNowDay() - 24 * 3600 * 7;
                } else {
                    $start_time = Time::getNowDay() - 24 * 3600 * 30;
                }

                $query_data = StatisticsData::where('seller_id',$sellerId)
                    ->whereIn('status',[ORDER_STATUS_FINISH_SYSTEM, ORDER_STATUS_FINISH_USER])
                    ->whereBetween('create_day',[$start_time,$end_time])
                    ->groupBy('date')
                    ->selectRaw("count(*) as num ,sum(total_fee) as trading ,sum(total_fee - freight) as total , FROM_UNIXTIME(create_time+8*3600,'%m-%d') as date")
                    ->get();

                $query_data = $query_data->toArray();

                for($i = 1 ;$i <= $type;$i++){
                    $time = Time::toDate($start_time + $i * 24 * 3600,'m-d');
                    $data_x[] = $time;
                    $flag = [];

                    foreach ($query_data as $value) {
                        if($value['date'] ==  $time){
                            $flag = $value;
                            break;
                        }
                    }

                    if(!empty($flag)){
                        $data[$time] = array('num'=>$flag['num'],'total'=>$flag['total']);
                        $data_y_num[] = $flag['num'];
                        $data_y_total[] = $flag['total'];
                        $data_y_trading[] = $flag['trading'];
                    } else {
                        $data[$i] = array('num'=>0,'total'=>0,'trading'=>0);
                        $data_y_num[] = 0;
                        $data_y_total[] = 0;
                        $data_y_trading[] = 0;
                    }
                }
            }
        }
        $result['stat'] = $data;
        $result['stat_x'] = $data_x;
        $result['stat_num'] = $data_y_num;
        $result['stat_total'] = $data_y_total;
        $result['stat_trading'] = $data_y_trading;
        return $result;
    }
    /**
     * 营业额
     * @param  $sellerId
     * @param  $beginDate 开始时间
     * @param  $endDate 结束时间
     * @param  $type
     */
    public function revenue($sellerId,$beginDate,$endDate,$type){
//        DB::connection()->enableQueryLog();
        $result = array(
            'code'	=> self::SUCCESS,
            'data'	=> null,
            'msg'	=> ''
        );
        //$result = [];			//返回的结果集
        $data = [];  			//未分组的数据
        $data_x = [];			//X轴数据
        $data_y_total = [];     //Y轴的营业额
        $data_y_num = [];       //Y轴的有效订单数
        $data_y_price = [];     //Y轴客家价
        //如果有开始时间和结束时间
        if(!empty($beginDate) && !empty($endDate)){
            $beginTime = Time::toTime($beginDate);
            $endTime = Time::toTime($endDate);
            if($beginTime > $endTime){
                $tempTime = $beginTime;
                $beginTime = $endTime;
                $endTime = $beginTime;
            }
            $rs = ($endTime - $beginTime)/(3600 * 24);

            if($rs > 90){
                $result['code'] = 80300;
                return $result;
            }
            $query_data = Order::where('seller_id',$sellerId)
                ->whereRaw("pay_status = 1 AND `create_day` BETWEEN ".$beginTime." AND ".$endTime."
							AND (status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
                            OR (status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL)
                            OR (status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL)
                            OR (status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL))")
                ->groupBy('date')
                ->selectRaw("count(*) as num ,sum(goods_fee + freight) as total,FROM_UNIXTIME(create_day+8*3600,'%m-%d') as date")
                ->get();
            $query_data = $query_data->toArray();

            for ($i = 0; $i <= $rs; $i++) {
                $time = Time::toDate($beginTime + $i * 24 * 3600,'m-d');
                $data_x[] = $time;
                $flag = [];
                foreach ($query_data as $value) {
                    if($value['date'] ==  $time){
                        $flag = $value;
                        break;
                    }
                }
                if(!empty($flag)){
                    $flag['num'] = $flag['num'] > 0 ?$flag['num']:0;
                    $data[$time] = array('num'=>$flag['num'],'total'=>number_format($flag['total'],2),'price'=>number_format($flag['total'] / $flag['num'],2));
                    $data_y_num[] = $flag['num'];
                    $data_y_total[] = $flag['total'];
                    $data_y_price[] = round($flag['total'] / $flag['num'],2);
                } else {
                    $data[$time] = array('num'=>0,'total'=>0,'price'=>0);
                    $data_y_num[] = 0;
                    $data_y_total[] = 0;
                    $data_y_price[] = 0;
                }
            }
        }else{
            if($type <= 1){//今天
                $create_day = $type == 0 ? Time::getNowDay() : Time::getNowDay() - 3600 * 24;
                $query_data = Order::where('pay_status',1)
                    ->whereRaw("seller_id = ".$sellerId." AND create_day = ".$create_day." AND (status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
	                        OR (status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL)
	                        OR (status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL)
	                        OR (status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL))")
                    ->groupBy('create_day')
                    ->selectRaw("count(*) as num ,sum(goods_fee + freight) as total")
                    ->get();

                $query_data = $query_data->toArray();
            } else {//当最小时间为天数据的时候
                $start_time = 0;
                $end_time = Time::getNowDay() + 24 * 3600;
                if($type == 7){
                    $start_time = Time::getNowDay() - 24 * 3600 * 7;
                } else {
                    $start_time = Time::getNowDay() - 24 * 3600 * 30;
                }

                $query_data = Order::where('seller_id',$sellerId)
                    ->whereRaw("pay_status = 1 AND `create_day` BETWEEN ".$start_time." AND ".$end_time."
									AND (status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
		                            OR (status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL)
		                            OR (status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL)
		                            OR (status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL))")
                    ->groupBy('date')
                    ->selectRaw("count(*) as num ,sum(goods_fee + freight) as total , FROM_UNIXTIME(create_day+8*3600,'%m-%d') as date")
                    ->get();
                $query_data = $query_data->toArray();
                for($i = 1 ;$i <= $type;$i++){
                    $time = Time::toDate($start_time + $i * 24 * 3600,'m-d');
                    $data_x[] = $time;
                    $flag = [];
                    foreach ($query_data as $value) {
                        if($value['date'] ==  $time){
                            $flag = $value;
                            break;
                        }
                    }

                    if(!empty($flag)){
                        $flag['num'] = $flag['num'] > 0 ?$flag['num']:0;
                        $data[$time] = array('num'=>$flag['num'],'total'=>number_format($flag['total'],2),'price'=>number_format($flag['total'] / $flag['num'],2));
                        $data_y_num[] = $flag['num'];
                        $data_y_total[] = $flag['total'];
                        $data_y_price[] = round($flag['total'] / $flag['num'],2);
                    } else {
                        $data[$time] = array('num'=>0,'total'=>0,'price'=>0);
                        $data_y_num[] = 0;
                        $data_y_total[] = 0;
                        $data_y_price[] = 0;
                    }
                }
            }
        }
// 	    print_r($data);exit;
        $result['stat'] = $data;
        $result['stat_x'] = $data_x;
        $result['stat_num'] = $data_y_num;
        $result['stat_total'] = $data_y_total;
        $result['stat_price'] = $data_y_price;

//        print_r($result['stat_price']);
        foreach($query_data as $key=>$val){
            $result['num'] += $val['num'];
            $result['total'] += $val['total'];
        }
        $result['total'] = number_format($result['total'],2);
        $result['price'] = number_format($result['total'] / $result['num'], 2);

        return $result;
    }

    /**
     * 营业额
     * @param  $sellerId
     * @param  $beginDate 开始时间
     * @param  $endDate 结束时间
     * @param  $type
     */
    public function goodsreport($sellerId,$beginDate,$endDate,$type,$numOrder,$priceOrder,$page,$pageSize){
        //DB::connection()->enableQueryLog();
        $result = [];			//返回的结果集
        $data = [];  			//未分组的数据

        //如果有开始时间和结束时间
        if(!empty($beginDate) && !empty($endDate)){
            $beginTime = Time::toTime($beginDate);
            $endTime = Time::toTime($endDate);
            if($beginTime > $endTime){
                $tempTime = $beginTime;
                $beginTime = $endTime;
                $endTime = $beginTime;
            }
            $rs = ($endTime - $beginTime)/(3600 * 24);
            if($rs > 90){
                $result['code'] = 80300;
                return $result;
            }
            $query_data = Order::where('seller_id',$sellerId)
                ->whereRaw("pay_status = 1 AND `create_day` BETWEEN ".$beginTime." AND ".$endTime."
							AND (status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
                            OR (status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL)
                            OR (status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL)
                            OR (status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL))")
                ->lists('id');
        }else{
            if($type <= 1){//今天

                $create_day = $type == 0 ? Time::getNowDay() : Time::getNowDay() - 3600 * 24;
                $query_data = Order::where('pay_status',1)
                    ->whereRaw("seller_id = ".$sellerId." AND create_day = ".$create_day." AND (status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
	                        OR (status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL)
	                        OR (status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL)
	                        OR (status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL))")
                    ->lists('id');

            } else {//当最小时间为天数据的时候
                $start_time = 0;
                $end_time = Time::getNowDay() + 24 * 3600;
                if($type == 7){
                    $start_time = Time::getNowDay() - 24 * 3600 * 7;
                } else {
                    $start_time = Time::getNowDay() - 24 * 3600 * 30;
                }

                $query_data = Order::where('seller_id',$sellerId)
                    ->whereRaw("pay_status = 1 AND `create_day` BETWEEN ".$start_time." AND ".$end_time."
									AND (status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
		                            OR (status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL)
		                            OR (status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL)
		                            OR (status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL))")
                    ->whereBetween('create_day',[$start_time,$end_time])
                    ->lists('id');
            }
        }

        if(!empty($query_data)){
            $totalCount = OrderGoods::whereIn('order_id',$query_data)->count();
            $data = OrderGoods::whereIn('order_id',$query_data)
                ->selectRaw("goods_name,num ,price,num*price as totleprice")
                ->skip(($page - 1) * $pageSize)
                ->take($pageSize);
            if($numOrder == 1){
                $data = $data->orderBy('num','desc');
            }else if($numOrder == 2){
                $data = $data->orderBy('num','asc');
            }
            if($priceOrder == 1){
                $data = $data->orderBy('totleprice','desc');
            }else if($priceOrder == 2){
                $data = $data->orderBy('totleprice','asc');
            }
            $list = $data->get()->toArray();
        }else{
            $list = '';
            $totalCount = '';
        }

        return ["list"=>$list, "totalCount"=>$totalCount];
    }
}
