<?php namespace YiZan\Services\Buyer;

use YiZan\Models\Activity;
use YiZan\Models\ActivityPromotion;
use YiZan\Models\ActivityGoods;
use YiZan\Models\ActivityLogs;
use YiZan\Models\Order;

use YiZan\Utils\Time;
use YiZan\Utils\String;
use DB, Exception,Validator;

/**
 * 活动列表
 */
class ActivityService extends \YiZan\Services\ActivityService {
    /**
     * 列表
     * @param string $clientType 类型
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          广告信息
     */
    public static function getList($page, $pageSize=100)
    {
        //刷新活动
    	self::refreshActicity();

        $list = Activity::where('type', 1)
        				->orderBy('status', 'desc')
        				->orderBy('time_status', 'asc')
        				->orderBy('start_time', 'asc');

        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();

        return $list;
    }

	/**
	 * [activityGoodsLists 获取活动服务]
	 * @param  [type] $activityId [description]
	 * @param  [type] $page       [description]
	 * @return [type]             [description]
	 */
    public static function activityGoodsLists($activityId, $times, $price, $makeType, $page, $pageSize=20) {
    	$list = ActivityGoods::select('activity_goods.*','goods.*')
					    		->where('activity_goods.activity_id', $activityId)
					    		->with('activity')
					    		->join('goods', function($query){
									$query->on('goods.id', '=', 'activity_goods.goods_id')
										  ->where('goods.sale_status', '=', 1)
										  ->where('goods.status', '=', 1)
										  ->where('goods.is_del', '=', 0);
								});
    						
		//0=次数由少到多 1=次数由多到少
		if(!empty($times)){
			if ($times == 0) {
	    		$list->orderBy('goods.sales_volume', 'asc');
	    	}
	    	else if ($times == 1) {
	    		$list->orderBy('goods.sales_volume', 'desc');
	    	}
		}

		//0=价格由低到高 1=由高到底
		if(!empty($price)){
			if($price == 0){
	    		$list->orderBy('activity_goods.shopping_spree_price', 'asc');
	    	}
	    	else if($price == 1){
	    		$list->orderBy('activity_goods.shopping_spree_price', 'desc');
	    	}
		} 
		
    	//进行方式：1=上门服务 2=到店服务 3=上门服务+到店服务
    	if($makeType > 0){
    		$list->where('goods.make_type', $makeType);
    	}

    	$list = $list->skip(($page - 1) * $pageSize)
            		->take($pageSize)
            		->get()
            		->toArray();

        return $list;
    }

    /**
     * 获取分享活动
     */
    public static function getshare($orderId,$activityId){
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> ''
        );

        $order = Order::where('id',$orderId)->where('pay_status',1)->where('pay_type','!=','cashOnDelivery')->first();

        if(!empty($order)){
            if(!empty($activityId)){
                $share_activity = Activity::where('type',1)->where('id',$activityId);
            }else{
                $share_activity = Activity::where('type',1);
            }

            $share_activity->where('status',1)->where('start_time','<',$order->pay_time)->where('end_time','>=',$order->pay_time)->where('money','<=',$order->pay_fee)->with('promotion.promotion');
            $share_activity = $share_activity->first();

            if(!empty($share_activity)){
                $share_activity = $share_activity->toArray();
                $result['data'] = $share_activity;
                $result['data']['linkUrl'] = u('wap#UserCenter/obtaincoupon',array('orderId'=>$orderId,'activityId'=>$share_activity['id']));

                if(!empty($activityId)){
                    $logs = ActivityLogs::where('activity_id',$activityId)->where('order_id',$orderId)->with('user');
                }else{
                    $logs = ActivityLogs::where('order_id',$orderId)->with('user');
                }
                $logs = $logs->get();
                if(!empty($share_activity)){
                    $logs = $logs->toArray();
                    $result['data']['logs'] = $logs;
                }

                if(empty($activityId) && ($result['data']['promotion'][0]['num'] <= 0 || count($result['data']['logs']) >= $result['data']['sharePromotionNum'])){
                    $result['data'] = null;
                }
            }
        }

        return $result;
    }


}
