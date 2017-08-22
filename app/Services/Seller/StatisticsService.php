<?php namespace YiZan\Services\Seller;
use YiZan\Models\Seller\StatisticsMonth;
use YiZan\Models\Order;
use YiZan\Utils\Time;
use DB;
class StatisticsService extends \YiZan\Services\BaseService { 

	/**
	 * 获取某月统计明细
	 * @param  [type] $month  [description]
	 * @param  [type] $page [description]
	 * @return [type]          [description]
	 */
	public static function getStatisticsDetail($sellerId,$month,$page) {    
		$query = Order::where('seller_id',$sellerId)
		 		  	 ->whereIn('status',[ORDER_STATUS_USER_CONFIRM, ORDER_STATUS_SYSTEM_CONFIRM]);
		if($month == 0){
            $query->where('create_day',Time::getNowDay());
		} else {
			$start = Time::toTime($month.'01');
			$end = Time::toTime($month . Time::toDate($start, "t")); 
			$query->whereBetween('create_day',array($start,$end)); 
		}

		$data = $query->with('goods')->skip(($page - 1) * 20)->take(20)->get(); 
		return $data->toArray();
	}

	/**
	 * 按月份来统计
	 * @param  [type] $page  [description] 
	 * @return [type]          [description]
	 */
	public static function getStatisticsByMonth($sellerId,$page) {

		$data = StatisticsMonth::where('seller_id',$sellerId)
		 		  	 ->whereIn('status',[ORDER_STATUS_USER_CONFIRM, ORDER_STATUS_SYSTEM_CONFIRM])
		 		  	  ->groupBy('month')
            		  ->selectRaw("count(*) as num ,sum(pay_fee) as total , FROM_UNIXTIME(create_day,'%Y%m') as month")
    		  		  ->skip(($page - 1) * 20)->take(20)->get(); 

		return $data->toArray();
	}

}
