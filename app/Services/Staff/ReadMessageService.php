<?php namespace YiZan\Services\Staff;
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
     * Summary of getStaffList
     * @param mixed $sellerId 
     * @param mixed $staffId 
     * @param mixed $page 
     * @param mixed $pageSize 
     * @return mixed
     */
    public static function getStaffList($sellerId, $staffId, $page, $pageSize = 20) 
    {
        return ReadMessage::select
            (
                'read_message.id', 
                'push_message.args', 
                'push_message.send_type AS type', 
                'push_message.create_type AS create_type', 
                'push_message.title', 
                'push_message.content', 
                'push_message.send_time', 
                DB::raw('(CASE WHEN read_time > 1 THEN 1 ELSE 0 END) AS is_read')
            )
            ->join('push_message', 'push_message.id', '=', 'read_message.message_id')
            ->where(function($query) use($sellerId, $staffId) { 
                if($sellerId > 0){
                    $query->where('read_message.seller_id', $sellerId);
                    if($staffId > 0){
                        $query->orWhere('read_message.staff_id', $staffId);
                    }
                }elseif($staffId > 0){
                    $query->where('read_message.staff_id', $staffId);
                } 
            }) 
            ->orderBy('read_message.id', "DESC")
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();
    }
    /**
     * 阅读消息
     * @param int $staffId 卖家编号
     * @param int $id 消息编号
     * @return array 
     */
    public static function readMessage($sellerId, $staffId, $id) 
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
                ReadMessage::where("staff_id", $staffId)->where("id", $id)->update(["read_time" => Time::getTime()]);
				ReadMessage::where("seller_id", $sellerId)->where("id", $id)->update(["read_time" => Time::getTime()]);
            }
            else
            {
                ReadMessage::where("staff_id", $staffId)->where("read_time", 0)->update(["read_time" => Time::getTime()]);
                ReadMessage::where("seller_id", $sellerId)->where("read_time", 0)->update(["read_time" => Time::getTime()]);
            }
        }
        else if(is_array($id) && count($id) > 0)
        {
            ReadMessage::where("staff_id", $staffId)->whereIn("id", $id)->update(["read_time" => Time::getTime()]);
            ReadMessage::where("seller_id", $sellerId)->whereIn("id", $id)->update(["read_time" => Time::getTime()]);
        }
        
        return $result;
    }
    
    /**
     * 删除消息
     * @param int $staffId 卖家员工编号
     * @param int $id 消息编号
     * @return array 
     */
    public static function deleteMessage($staffId, $id) 
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
                ReadMessage::where("staff_id", $staffId)->where("id", $id)->delete();
            }
            else
            {
                ReadMessage::where("staff_id", $staffId)->delete();
            }
        }
        else if(is_array($id) && count($id) > 0)
        {
            ReadMessage::where("staff_id", $staffId)->whereIn("id", $id)->delete();
        }
        
        return $result;
    }
    /**
     * 是否有新消息
     * @param int $staffId 卖家编号
     * @return bool
     */
    public static function hasNewMessage($staffId)
    {
        $reslut = ReadMessage::where('staff_id', $staffId)
            ->where('read_time', 0)
            ->pluck('staff_id');
        
        return $reslut > 0;
    }

    /**
     * 获取消息
     * @param int $userId 买家编号
     * @param int $id 消息编号
     * @return array 
     */
    public static function getdatas($staffId,$id)
    {
        ReadMessage::where("staff_id", $staffId)->where("id", $id)->update(["read_time" => Time::getTime()]);
        return ReadMessage::select
            (
                'read_message.id', 
                'push_message.args', 
                'push_message.send_type', 
                'push_message.title', 
                'push_message.content', 
                'push_message.send_time', 
                DB::raw('(CASE WHEN read_time > 1 THEN 1 ELSE 0 END) AS status')
            )
            ->join('push_message', 'push_message.id', '=', 'read_message.message_id')
            ->where('read_message.staff_id', $staffId)
            ->where('read_message.id', $id)
            ->first();
    } 
}
