<?php namespace YiZan\Services\Staff;
use YiZan\Models\SellerStaff;
use YiZan\Models\Staff\StatisticsMonth;
use YiZan\Models\Order;
use YiZan\Models\SellerStaffExtend;
use YiZan\Utils\Time;
use DB;
class StatisticsService extends \YiZan\Services\BaseService { 

	/**
	 * 获取某月统计明细
	 * @param  [type] $month  [description]
	 * @param  [type] $page [description]
	 * @return [type]          [description]
	 */
	public static function getStatisticsDetail($staffId,$month,$page) {
        $data = [];
        $money = SellerStaffExtend::where('staff_id', $staffId)->pluck('withdraw_money');
		$query = Order::where('seller_staff_id',$staffId)
		 		  	 ->whereIn('status',[ORDER_STATUS_FINISH_USER, ORDER_STATUS_FINISH_SYSTEM]);
                     //->where('staff_fee', '>', '0');
		if($month == 0){
            //$query->where('create_day', UTC_DAY);
		} else {
			$start = Time::toTime($month.'01');
			$end = Time::toTime($month . Time::toDate($start, "t")); 
			$query->whereBetween('create_day',array($start,$end)); 
		}
        
		$list = $query->skip(($page - 1) * 20)->take(20)->get()->toArray();

        foreach ($list as $k => $v) {
            $data[$k] = [
                'orderSn' => $v['sn'],
                'money' => $v['staffFee'],
                'createTime' => Time::toDate($v['createTime'], 'Y-m-d H:i'),
                'content' => '佣金收入'
            ];
        }
		return ['total' => $money, 'commisssions' => $data];
	}

	/**
	 * 按月份来统计
	 * @param  [type] $page  [description] 
	 * @return [type]          [description]
	 */
	public static function getStatisticsByMonth($staffId,$page) {
		$list = StatisticsMonth::where('seller_staff_id',$staffId)
		 		  	 ->whereIn('status',[ORDER_STATUS_FINISH_USER, ORDER_STATUS_FINISH_SYSTEM])
                     //->where('staff_fee', '>', '0')
		 		  	 ->groupBy('month')
            		 ->selectRaw("count(*) as num ,sum(staff_fee) as total , FROM_UNIXTIME(create_day,'%Y%m') as month, create_day")
    		  		 ->skip(($page - 1) * 20)->take(20)->get()->toArray();

        foreach($list as $k=>$v) {
            $list[$k]['year'] = Time::toDate($v['createDay'],'Y');
            $list[$k]['month'] = Time::toDate($v['createDay'],'m');
            unset($list[$k]['createDay']);
        }
        return $list;
	}

}
