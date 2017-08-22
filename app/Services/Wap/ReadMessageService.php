<?php namespace YiZan\Services\Wap;
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
     * Summary of getList
     * @param mixed $userId 
     * @param mixed $page 
     * @param mixed $pageSize 
     * @return mixed
     */
    public static function getList($userId, $page, $pageSize = 20) 
    {
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
            ->where('read_message.user_id', $userId)
            ->orderBy('read_message.id', "DESC")
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();
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
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
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
    public static function deleteMessage($userId, $ids) 
    {
        if( !is_array( $ids ) ){
            $ids = [  '0' => $ids   ];
        }        
        if( !empty($ids))
        {        
          return  ReadMessage::where("user_id", $userId)->whereIn("id", $ids)->delete();
        }
    }
    /**
     * 获取最新消息条数
     * @param int $userId 买家编号
     * @return array 
     */
    public static function getNewest($userId)
    {
        return ReadMessage::where("user_id", $userId)->where('read_time', '0')->count();
    }
    /**
     * 获取消息
     * @param int $userId 买家编号
     * @param int $id 消息编号
     * @return array 
     */
    public static function getdatas($userId,$id)
    {
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
            ->where('read_message.user_id', $userId)
            ->where('read_message.id', $id)
            ->first();
    }    
}
