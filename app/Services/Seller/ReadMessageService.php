<?php namespace YiZan\Services\Seller;
use YiZan\Models\PushMessage;
use YiZan\Models\ReadMessage;
use YiZan\Utils\Time;
use DB;
/**
 * 阅读信息
 */
class ReadMessageService extends \YiZan\Services\ReadMessageService 
{ 
    /**
     * Summary of getSellerList
     * @param mixed $sellerId 
     * @param mixed $page 
     * @param mixed $pageSize 
     * @return mixed
     */
    public static function getSellerList($sellerId, $page, $pageSize = 20) 
    {
        return ReadMessage::select
            (
                'read_message.id', 
                'push_message.args', 
                'push_message.send_type AS type', 
                'push_message.title', 
                'push_message.content', 
                'push_message.send_time', 
                DB::raw('(CASE WHEN read_time > 1 THEN 1 ELSE 0 END) AS status')
            )
            ->join('push_message', 'push_message.id', '=', 'read_message.message_id')
            ->where('read_message.seller_id', $sellerId)
            ->orderBy('read_message.id', "DESC")
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();
    }
    /**
     * 阅读消息
     * @param int $sellerId 卖家编号
     * @param int $id 消息编号
     * @return array 
     */
    public static function readMessage($sellerId, $id) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		];
        
        if($id > 0)
        {
            ReadMessage::where("seller_id", $sellerId)->where("id", $id)->update(["read_time" => Time::getTime()]);
        }
        else
        {
            ReadMessage::where("seller_id", $sellerId)->where("read_time", 0)->update(["read_time" => Time::getTime()]);
        }
        
        return $result;
    }
    /**
     * 是否有新消息
     * @param int $sellerId 卖家编号
     * @return bool
     */
    public static function hasNewMessage($sellerId)
    {
        $reslut = ReadMessage::where('seller_id', $sellerId)
            ->where('read_time', 0)
            ->pluck('seller_id');
        
        return $reslut > 0;
    }
}
