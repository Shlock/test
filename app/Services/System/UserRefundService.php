<?php 
namespace YiZan\Services\System;
 
use YiZan\Models\System\Order; 
use YiZan\Models\Refund; 
use YiZan\Utils\Time, 
    String,
    YiZan\Services\PaymentService;

class UserRefundService extends \YiZan\Services\UserRefundService {
	/**
	 * 获取退款列表
	 * @param  string  $user         会员
	 * @param  string  $beginTime    创建开始时间
	 * @param  string  $endTime      创建结束时间
	 * @param  integer $status       处理状态
	 * @param  integer $page         页码
	 * @param  integer $pageSize     每页数量
	 * @return array                
	 */
	public static function getLists($userName,$mobile, $orderSn, $beginTime, $endTime, $status, $page, $pageSize)
    {
		$list = Refund::orderBy('refund.id', 'desc')
            ->select("refund.*", "user.name AS userName","user.mobile","order.order_type")
            ->where('refund.status', $status);

        $list->join('user', 'user.id', '=', 'refund.user_id');
        $list->join('order', 'order.id', '=', 'refund.order_id');
        //搜索会员
		if (!empty($userName)) 
        {
            $keywords = String::strToUnicode($userName,'+');
            
			$list->whereRaw("MATCH(user.name_match) AGAINST('{$keywords}' IN BOOLEAN MODE)");
		}
        //根据会员手机号进行搜索
        if (!empty($mobile)) {
            $list->where('user.mobile', $mobile);
        }
        //根据订单号进行搜索
        if (!empty($orderSn)) {
            $list->where('refund.sn', $orderSn);
        }

        //创建开始时间
		if (!empty($beginTime)) 
        {
			$list->where('refund.create_day', '>=', Time::toTime($beginTime));
		}

        //创建结束时间
		if (!empty($endTime))
        {
			$list->where('refund.create_day', '<=', Time::toTime($endTime));
		}
        $totalMoney = $list->sum('refund.money');
		$total_count = $list->count(); 
		
		$list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
        
		return ["list" => $list, "totalCount" => $total_count,'totalMoney'=>$totalMoney];
	}

	/**
	 * Summary of dispose
	 * @param mixed $id 
	 * @return mixed
	 */
	public static function dispose($id) 
    {
        $result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ''
		); 
        
        $refund = Refund::where("id", $id)->first();
        
        // 无数据
        if($refund == null)
        {
            return $result;
        }
        
        $result[data] =  PaymentService::createRefundLog($refund);
        
        return $result;
	}
}