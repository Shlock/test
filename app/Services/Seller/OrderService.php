<?php 
namespace YiZan\Services\Seller;

use YiZan\Models\Seller\Order;
use YiZan\Models\Seller;
use YiZan\Models\Goods;
use YiZan\Models\PromotionSn;
use YiZan\Models\UserPayLog;
use YiZan\Models\OrderPromotion;

use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use Exception, DB, Lang, Validator, App;

class OrderService extends \YiZan\Services\OrderService 
{
	/**
     * 订单列表
     * @param  int $sellerId 卖家
     * @param  int $status 订单状态
     * @param  int $page 页码
     * @return array          订单列表
	 */
	public static function getSellerList($sellerId, $status, $page, $pageSize = 20) 
    {
        $list = Order::orderBy('id', 'desc');
        
        $list->where('seller_id', $sellerId);

		return $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('goods','promotion', 'user')
            ->get()
            ->toArray();
    }
    /**
     * 获取订单
     * @param  int $id 订单id
     * @return array   订单
     */
	public static function getSellerOrderById($id) 
    {
		return Order::where('id', $id)
		    ->with('goods','promotion', 'user')
		    ->first();
	}
    /**
     * 更新订单
     * @param  int $id 订单id
     * @param  int $status 状态
     * @return array   更新结果
     */
	public static function updateSellerOrder($id, $status) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.update_info')
		];

        if( $status != ORDER_STATUS_START_SERVICE &&
            $status != ORDER_STATUS_FINISH_SERVICE)
        {
            $result['code'] = 20002; // 订单状态不合法
            
	    	return $result;
        }
        
        $order = Order::where('id', $id)->first();
        
        //没有订单
		if ($order == false) 
        {
			$result['code'] = 20001; // 没有找到相关订单
            
	    	return $result;
		}

        if(($order->status == ORDER_STATUS_SELLER_ACCEPT ||
            $order->status == ORDER_STATUS_STAFF_ACCEPT || 
            $order->status == ORDER_STATUS_STAFF_SETOUT) && 
            $status != ORDER_STATUS_START_SERVICE)
        {
            $result['code'] = 20002; // 订单状态不合法
            
	    	return $result;
        }
        
        if($order->status == ORDER_STATUS_START_SERVICE &&
            $status != ORDER_STATUS_FINISH_SERVICE)
        {
            $result['code'] = 20002; // 订单状态不合法
            
	    	return $result;
        }
        
        if($status == ORDER_STATUS_START_SERVICE)
        {
            Order::where('id', $id)->update(['status' => $status, "service_start_time" => UTC_TIME]);
        }
        else if ($status == ORDER_STATUS_FINISH_SERVICE)
        {
            Order::where('id', $id)->update(['status' => $status, "service_finish_time" => UTC_TIME]);
        }
        
        $result["data"] = self::getSellerOrderById($id)->toArray();
        
		return $result;
	}
}
