<?php namespace YiZan\Services\Buyer;
use YiZan\Models\Promotion;
use YiZan\Models\PromotionSn;
use YiZan\Models\PushMessage;
use YiZan\Models\ReadMessage;
use YiZan\Models\ShoppingCart;
use YiZan\Models\UserAddress;
use YiZan\Models\UserCollect;
use YiZan\Models\Order;
use YiZan\Utils\Time;
use DB;
/**
 * 阅读信息
 */
class ReadMessageService extends \YiZan\Services\ReadMessageService 
{ 
    /**
     * Summary of getSellerList
     * @param mixed $userId 
     * @param mixed $page 
     * @param mixed $pageSize 
     * @return mixed
     */
    public static function getBuyerList($userId, $page, $pageSize = 20) 
    {
        $begin = $pageSize * ($page - 1);

        $sql = "SELECT  T.`seller_id`,
				T.`sum`,
				P.`title`,
				IFNULL(S.`logo`, '') AS `logo`,
				IFNULL(S.`name`, '平台消息') AS `name`
		FROM
		(
				SELECT 	P.`seller_id`,
								SUM(CASE WHEN read_time > 1 THEN 0 ELSE 1 END) AS `sum`,
								MAX(P.`id`) AS `id`
						FROM `yz_read_message` AS R
								INNER JOIN `yz_push_message` AS P ON R.`message_id` = P.`id`
						WHERE R.`user_id` = ".$userId."
						GROUP BY P.`seller_id`
		) AS T
		INNER JOIN `yz_push_message` AS P ON T.`id` = P.`id`
		LEFT OUTER JOIN `yz_seller` AS S ON T.`seller_id` = S.`id` limit ".$begin.",".$pageSize;

        //return $sql;
        $list = DB::select($sql);
        return $list;
    }
    /**
     * 阅读消息
     * @param int $userId 买家编号
     * @param int $id 消息编号
     * @return array 
     */
    public static function readMessage($userId, $ids)
    {
		$result = 
        [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];
        if( !is_array( $ids ) ){
            $ids = [  '0' => $ids   ];
        }
        if( !empty($ids))
        {
            ReadMessage::where("user_id", $userId)->where("read_time", 0)->whereIn("id", $ids)->update(["read_time" => Time::getTime()]);
        }
        else
        {
            ReadMessage::where("user_id", $userId)->where("read_time", 0)->update(["read_time" => Time::getTime()]);
        }

        return $result;
    }
    /**
     * 删除消息
     * @param int $userId 买家编号
     * @param int $id 消息编号
     * @return array 
     */
    public static function deleteMessage($userId, $id) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		];
        
        if(is_int($id))
        {
            if($id > 0)
            {
                ReadMessage::where("user_id", $userId)->where("id", $id)->delete();
            }
            else
            {
                ReadMessage::where("user_id", $userId)->delete();
            }
        }
        else if(is_array($id) && count($id) > 0)
        {
            ReadMessage::where("user_id", $userId)->whereIn("id", $id)->delete();
        }
        
        return $result;
    }
    /**
     * 是否有新消息
     * @param int $userId 买家编号
     * @return bool
     */
    public static function hasNewMessage($userId)
    {
        $reslut = ReadMessage::where('user_id', $userId)
            ->where('is_read', 0)
            ->pluck('user_id');
        
        return $reslut > 0;
    }

    public static function getDatas($userId, $sellerId, $page)
    {
        $pageSize = 20;
        $begin = $pageSize * ($page - 1);
        ReadMessage::where('read_time',0)
                    ->where('user_id', $userId)
                    ->whereIn('message_id',function($query) use ($userId, $sellerId){
                            $query->select('id')
                                ->from('push_message')
                                ->where('seller_id', $sellerId);
                    })->update(['read_time' => UTC_TIME]);

        $sql = "SELECT P.*, O.id,O.count,O.total_fee,O.app_time,O.order_type,S.logo
		FROM `yz_read_message` AS R
				INNER JOIN `yz_push_message` AS P ON R.`message_id` = P.`id`
				LEFT OUTER JOIN `yz_order` AS O ON P.`send_type` = 3 AND P.`args` + 0 = O.`id`
				LEFT OUTER JOIN `yz_seller` AS S ON P.`seller_id` = S.`id`
		WHERE R.`user_id` = ".$userId."
				AND P.`seller_id` = ".$sellerId." ORDER BY P.send_time DESC limit ".$begin.",".$pageSize;
        $list = DB::select($sql);
        return $list;
    } 

    public static function getCounts($userId)
    {
        $list = [];
        $carts = 0;
        $cartcount = ShoppingCart::where('user_id', $userId)->get()->toArray();
        foreach ($cartcount as $key => $value) {
            $carts += $value['num'];
        }
        if ($userId > 0) {
            $list['cartGoodsCount'] = $carts;
            $list['collectCount'] = UserCollect::where('user_id', $userId)->count();
            $list['addressCount'] = UserAddress::where('user_id', $userId)->count();
            $list['newMsgCount'] = ReadMessage::where('user_id', $userId)
                                                ->where('read_time', 0)
                                                ->join('push_message', function($join) {
                                                    $join->on('read_message.message_id', '=', 'push_message.id');
                                                 })
                                                ->count();
            $list['orderCount'] = Order::where('user_id', $userId)
                                            ->whereNotIn('status', [
                                                ORDER_STATUS_USER_DELETE,
                                                ORDER_STATUS_SELLER_DELETE,
                                                ORDER_STATUS_ADMIN_DELETE
                                            ])->count();
            $list['proCount'] = PromotionSn::where('user_id', $userId)
												->where('is_del',0)
                                                ->where('use_time',0)
                                                ->where('expire_time','>',UTC_TIME)
                                                ->count();

        } else {
            $list['cartGoodsCount'] = 0;
            $list['collectCount'] = 0;
            $list['addressCount'] = 0;
            $list['newMsgCount'] = 0;
            $list['orderCount'] = 0;
            $list['proCount'] = 0;
        }
        
        return $list;
    }
}
