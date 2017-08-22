<?php namespace YiZan\Services\System;

use YiZan\Models\System\Seller;
use YiZan\Models\System\OrderStatistics; 
use YiZan\Models\System\Order; 

use YiZan\Utils\String;

use DB, Validator, Time;

class StatisticsService extends \YiZan\Services\BaseService {

	/**
	 * 获取卖家统计数据
	 * @return [type] [description]
	 */
	public static function getSellerInfo(){  
		//服务人员申请处理
		$result['sellerCount'] = Seller::where('status',0)->count();
		return $result;
	}

	/**
	 * 获取订单统计数据
	 * @return [array] [result]
	 */
	public static function getOrderInfo($type){
		//当最小时间为小时数据的时候
		$result = [];
		$data_x = [];
		$data_y_num = []; 
		$data_y_total = [];
		$data = []; 
		if($type <= 1){  
		 	$order_data = OrderStatistics::where('status',7)
                ->groupBy('date')
			    ->selectRaw("count(*) as num ,sum(pay_fee) as total , FROM_UNIXTIME(create_time+8*3600,'%H') as date")
			    ->get(); 
            $order_data = $order_data->toArray(); 
			for($i = 0 ;$i < 24;$i++){
				$data_x[] = $i;
				$flag = [];
	            foreach ($order_data as $value) {
	            	if((int)$value['date'] ==  $i){
	            		$flag = $value;
	            		break;
	            	}	
	            }	 
	            if(!empty($flag)){
					$data[$i] = array('num'=>$flag['num'],'total'=>$flag['total']); 
					$data_y_num[] = $flag['num'];
					$data_y_total[] = $flag['total'];
				} else {
					$data[$i] = array('num'=>0,'total'=>0); 
					$data_y_num[] = 0;
					$data_y_total[] = 0;
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
			//$data = Order::where('status',7)
		 	$order_data = OrderStatistics::whereBetween('create_time',[$start_time,$end_time])
		 					   ->groupBy('date')
            	 		       ->selectRaw("count(*) as num ,sum(pay_fee) as total , FROM_UNIXTIME(create_time+8*3600,'%m-%d') as date") 
            	 		       ->get(); 
            $order_data = $order_data->toArray();   

			for($i = 0 ;$i < $type;$i++){
				$time = Time::toDate($start_time + $i * 24 * 3600,'m-d');
				$data_x[] = $time;
				$flag = [];

	            foreach ($order_data as $value) {
	            	if($value['date'] ==  $time){
	            		$flag = $value;
	            		break;
	            	}	
	            }	 

	            if(!empty($flag)){
					$data[$time] = array('num'=>$flag['num'],'total'=>$flag['total']); 
					$data_y_num[] = $flag['num'];
					$data_y_total[] = $flag['total'];
				} else {
					$data[$time] = array('num'=>0,'total'=>0); 
					$data_y_num[] = 0;
					$data_y_total[] = 0;
				}
			}
		}
		$result['stat'] = $data;
		$result['stat_x'] = $data_x;
		$result['stat_num'] = $data_y_num;
		$result['stat_total'] = $data_y_total;
		return $result;
	}
    /**
     * 业绩排行
     * @param int $beginDay 开始时间
     * @param int $endDay 结束时间
     * @return array
     */
    public static function performanceRanking($beginDay, $endDay)
    {
       $result = DB::table('order')
            ->select(DB::raw('SUM(pay_fee) AS totalPay, SUM(service_fee) AS totalService'), "seller.name")
            ->join('seller', 'seller.id', '=', 'order.seller_id')
            ->whereBetween('appoint_day', [$beginDay, $endDay])
            ->groupBy("seller_id")
            ->orderBy("totalPay", "DESC")
            ->take(10)
            ->get();

       return $result;
    }
    /**
     * 提成排行
     * @param int $beginDay 开始时间
     * @param int $endDay 结束时间
     * @return array
     */
    public static function bonusRanking($beginDay, $endDay)
    {
        $result = DB::table('order')
             ->select(DB::raw('SUM(pay_fee) AS totalPay, SUM(service_fee) AS totalService'), "seller.name")
             ->join('seller', 'seller.id', '=', 'order.seller_id')
             ->whereBetween('appoint_day', [$beginDay, $endDay])
             ->groupBy("seller_id")
             ->orderBy("totalService", "DESC")
             ->take(10)
             ->get();
 
        return $result;
    }
    /**
     * 卖家业绩
     * @param object $seller 卖家
     * @param int $beginDay 开始时间
     * @param int $endDay 结束时间
     * @return array
     */
    public static function sellerAchievement($seller, $beginDay, $endDay)
    {
        $sellerId = 0;
        
        if($seller == true)
        {
            if(is_int($seller) == true )
            {
                $sellerId = (int)$seller;
            }
            else if(is_string($seller) == true )
            {
               $sellerId = DB::table('seller')->whereRaw('MATCH(name_match) AGAINST(\'' . $seller . '\' IN BOOLEAN MODE)')->pluck("id");
            }
        }
        
        $db = DB::table('order')
             ->select(DB::raw('SUM(pay_fee) AS totalPay, SUM(service_fee) AS totalService'), "appoint_day")
             ->whereBetween('appoint_day', [$beginDay, $endDay])
             ->groupBy("appoint_day");
        
        if($sellerId == true)
        {
            $db->where("seller_id", $sellerId);
        }
        
        $list = $db->get();

        DB::table('order')
        	->select(DB::raw('SUM(pay_fee) AS totalPay'));

        if($sellerId == true)
        {
            $db->where("seller_id", $sellerId);
        }

        $allSum = $db->pluck("totalPay");

        $sum = $db->where("appoint_day", UTC_DAY)
        	->pluck("totalPay");

        return ["list"=>$list, "allSum"=>(double)$allSum, "sum"=>(double)$sum];
    }
    /**
     * 卖家员工业绩
     * @param object $seller 卖家
     * @param object $staff 卖家员工
     * @param int $beginDay 开始时间
     * @param int $endDay 结束时间
     * @return array
     */
    public static function sellerStaffAchievement($seller, $staff, $beginDay, $endDay)
    {
        $sellerId = 0;
        
        if($seller == true)
        {
            if(is_int($seller) == true )
            {
                $sellerId = (int)$seller;
            }
            else if(is_string($seller) == true )
            {
                $sellerId = DB::table('seller')->whereRaw('MATCH(name_match) AGAINST(\'' . $seller . '\' IN BOOLEAN MODE)')->pluck("id");
            }
        }
        
        $staffId = 0;
        
        if($staff == true)
        {
            if(is_int($staff) == true )
            {
                $staffId = (int)$staff;
            }
            else if(is_string($staff) == true )
            {
                $staffId = DB::table('seller_staff')->whereRaw('MATCH(name_match) AGAINST(\'' . $seller . '\' IN BOOLEAN MODE)')->pluck("id");
            }
        }
        
        $db = DB::table('order')
             ->select(DB::raw('SUM(pay_fee) AS totalPay, SUM(service_fee) AS totalService'), "appoint_day")
             ->whereBetween('appoint_day', [$beginDay, $endDay])
             ->groupBy("appoint_day");
        
        if($sellerId == true)
        {
            $db->where("seller_id", $sellerId);
        }
        
        if($staffId == true)
        {
            $db->where("seller_staff_id", $staffId);
        }
        
        $list = $db->get();

        DB::table('order')
        	->select(DB::raw('SUM(pay_fee) AS totalPay'));

        if($sellerId == true)
        {
            $db->where("seller_id", $sellerId);
        }

        if($staffId == true)
        {
            $db->where("seller_staff_id", $staffId);
        }
        
        $allSum = $db->pluck("totalPay");

        $sum = $db->where("appoint_day", UTC_DAY)
        	->pluck("totalPay");

        return ["list"=>$list, "allSum"=>(double)$allSum, "sum"=>(double)$sum];
    }
}
