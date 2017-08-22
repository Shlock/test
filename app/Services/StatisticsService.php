<?php 
namespace YiZan\Services;
use YiZan\Models\Sellerweb\StatisticsDaily;
use YiZan\Models\Sellerweb\StatisticsData;
use YiZan\Services\OrderService;
use YiZan\Utils\Time;
use DB;
class StatisticsService extends \YiZan\Services\BaseService {  

	/**
	 * 订单统计
	 * @return [type] [description]
	 */
	public static function orderCount($restaurantId){
		$result = [];
		$queries = StatisticsDaily::where('restaurant_id',$restaurantId);
		$result['comfirm'] = $queries->whereIn('status',[ORDER_STATUS_BEGIN_SERVICE,ORDER_USER_CONFIRM_SERVICE, ORDER_STATUS_SYSTEM_CONFIRM])
									->selectRaw("count(*) as num")
									->pluck('num'); 
		$result['unfinished'] = $queries->whereIn('status',
            [
                ORDER_STATUS_BEGIN_SERVICE, 
                ORDER_STATUS_PAY_SUCCESS,
                ORDER_STATUS_PAY_DELIVERY, 
                ORDER_STATUS_AFFIRM_SERVICE
            ])
									->selectRaw("count(*) as num")
									->pluck('num'); 
		$result['unpay'] = $queries->where('status', ORDER_STATUS_WAIT_PAY)
									->selectRaw("count(*) as num")
									->pluck('num'); 
		return $result;
	} 
	
	/**
	 * 本日营业统计
	 * @return [type] [description]
	 */
	public static function today($restaurantId){ 
		$today = Time::getNowDay();
		$queries = StatisticsDaily::where('restaurant_id',$restaurantId);
		$data_comfirm = $queries
			 		->whereIn('status',[
			 		    ORDER_STATUS_BEGIN_SERVICE,
			 		    ORDER_STATUS_PAY_SUCCESS,
			 		    ORDER_STATUS_PAY_DELIVERY,
			 		    ORDER_STATUS_AFFIRM_SERVICE,
			 		    ORDER_STATUS_AFFIRM_ASSIGN_SERVICE,
			 		    ORDER_STATUS_ASSIGN_SERVICE,
			 		    ORDER_USER_CONFIRM_SERVICE,
			 		    ORDER_STATUS_SYSTEM_CONFIRM			 		    
			 		])
					->where('create_day',$today)
					->groupBy('create_day')
            		->selectRaw("count(*) as num,IFNULL(sum(total_fee),0) as trading ,IFNULL(sum(pay_fee - service_fee),0) as total ")
        			->first(); 
		$data_unfinished = $queries
			 		->whereIn('status',
                     [
                            ORDER_STATUS_BEGIN_SERVICE, 
                            ORDER_STATUS_PAY_SUCCESS,
                            ORDER_STATUS_PAY_DELIVERY, 
                            ORDER_STATUS_AFFIRM_SERVICE
                    ])
					->where('create_day',$today)
					->groupBy('create_day')
            		->selectRaw("count(*) as num,IFNULL(sum(total_fee),0) as trading ,IFNULL(sum(pay_fee - service_fee),0) as total")
        			->first();  

        $result['comfirm'] = $data_comfirm ? $data_comfirm : ['num'=>0,'trading'=>0,'total'=>0,'duration'=>0];
        $result['unfinished'] = $data_unfinished ? $data_unfinished : ['num'=>0,'trading'=>0,'total'=>0,'duration'=>0]; 
        return $result;
	}

	/**
	 * 收入统计
	 * @return [type] [description]
	 */
	public static function income($restaurantId,$beginDate,$endDate,$type){ 
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

			$query_data = StatisticsData::where('restaurant_id',$restaurantId)
						->whereIn('status',[ORDER_STATUS_BEGIN_SERVICE, ORDER_USER_CONFIRM_SERVICE, ORDER_STATUS_SYSTEM_CONFIRM])
						->whereBetween('create_day',array($beginTime,$endTime))
						->groupBy('create_day')
						->selectRaw("count(*) as num,sum(total_fee) as trading ,sum(pay_fee - service_fee) as total,FROM_UNIXTIME(create_day,'%Y-%m-%d') as date")
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
			 	$query_data = StatisticsData::where('restaurant_id',$restaurantId)
			 								->where('create_day',$type == 0 ? Time::getNowDay() : Time::getNowDay() - 3600 * 24 )
			 								->whereIn('status',[ORDER_STATUS_BEGIN_SERVICE,ORDER_USER_CONFIRM_SERVICE, ORDER_STATUS_SYSTEM_CONFIRM])
			 								->groupBy('date') 
				            	 		    ->selectRaw("count(*) as num ,sum(total_fee) as trading ,sum(pay_fee - service_fee) as total , FROM_UNIXTIME(create_time+8*3600,'%H') as date")
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
			
			 	$query_data = StatisticsData::where('restaurant_id',$restaurantId)
                    ->whereIn('status',[ORDER_STATUS_BEGIN_SERVICE,ORDER_USER_CONFIRM_SERVICE, ORDER_STATUS_SYSTEM_CONFIRM])
			 		->whereBetween('create_day',[$start_time,$end_time])
			 		->groupBy('date')
	            	->selectRaw("count(*) as num ,sum(total_fee) as trading ,sum(pay_fee - service_fee) as total , FROM_UNIXTIME(create_time+8*3600,'%m-%d') as date") 
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

}
