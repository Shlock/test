<?php 
namespace YiZan\Services\Wap;
use YiZan\Models\Activity;
use Lang;
/**
 * 文章
 */
class ActivityService extends \YiZan\Services\ActivityService
{ 
    /**
     * [getList 活动列表]
     * @param  [type] $type     [类型]
     * @param  [type] $name     [活动名称]
     * @param  [type] $page     [页码]
     * @param  [type] $pageSize [分页大小]
     * @return [array]          [返回数据]
     */
    public static function getList($type=null, $name=null, $page, $pageSize) 
    {
        $list = Activity::select();
        $list->where('status',1)
            ->where('begin_time', '<', UTC_TIME)
            ->where('end_time', '>', UTC_TIME);

        if(!empty($type))
            $list->where('type',$type);
        if(!empty($name))
            $list->where('name', 'like', '%'.$name.'%');

        $totalCount = $list->count();
        $list = $list->orderBy('id', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->with('sellpromotion','giftpromotion')
            ->take($pageSize)
            ->get()
            ->toArray();
        return ["list"=>$list, "totalCount"=>$totalCount];
    }

    /**
     * 支付订单
     * @param  [type] $userId  [description]
     * @param  [type] $orderId [description]
     * @param  [type] $payment [description]
     * @return [type]          [description]
     */
    public static function payOrder($userId, $activityId, $payment, $extend = []) {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.user_pay_order')
        );

        $order = Activity::where('id', $activityId)->first();

        if (!$order) {//活动不存在
            $result['code'] = 60315;
            return $result;
        }

        $order->user_id = $userId;
        $order->pay_fee = $order->price;
        $order->goods_name = $order->name;
        $order->activity_id = $order->id;
        $payLog = PaymentService::createPayLog($order, $payment, $extend);
        if (is_numeric($payLog)) 
        {
            $result['code'] = abs($payLog);
            
            return $result;
        }
        
        $result['data'] = $payLog;
        
        return $result;
    }

    /**
     * 获取活动
     * @param  [type] $userId  [description]
     * @param  [type] $activityId [description] 825722
     * @return [type]          [description]
     */
    public static function getOrder($userId, $activityId) {

        return Activity::where('id', $activityId)
                    ->where('status',1)
                    ->first();
    }

}
