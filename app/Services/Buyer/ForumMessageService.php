<?php 
namespace YiZan\Services\Buyer;

use YiZan\Models\ForumMessage;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use DB, Validator, View;
/**
 * 站内消息
 */
class ForumMessageService extends \YiZan\Services\ForumMessageService {

    /**
     * 消息列表(总后台)
     * @param int $userId 会员编号
     * @param int $page 页码
     * @param int $pageSize 每页数量
     */
    public static function getList($userId, $page, $pageSize) {
        $list = ForumMessage::where('user_id', $userId)
                                ->skip(($page - 1) * $pageSize)
                                ->take($pageSize)
                                ->with('posts.plate', 'relateUser', 'user')
                                ->orderBy('id', 'DESC')
                                ->get()
                                ->toArray();

        foreach ($list as $key => $val) {
            $list[$key]['sendTime'] = Time::toDate($val['sendTime'], 'Y-m-d H:i:s'); 
            $list[$key]['readTime'] = Time::toDate($val['readTime'], 'Y-m-d H:i:s'); 
        } 
        return $list;
    }

    /**
     * 消息数量
     */
    public static function getMessageNum($userId){
        return ForumMessage::where('user_id', $userId)
                           ->where('read_time', 0)
                           ->count();
    }

    /**
     * 删除消息
     * @param int $userId 会员编号
     * @param string $id 消息编号 多个用","隔开
     */
    public static function delete($userId, $id) {
        $result =
            [
                'code'  => 0,
                'data'  => null,
                'msg'   => ""
            ];

        if(is_int($id))
        {
            if($id > 0)
            {
                ForumMessage::where("user_id", $userId)->where("id", $id)->delete();
            }
            else
            {
                ForumMessage::where("user_id", $userId)->delete();
            }
        }
        else if(is_array($id) && count($id) > 0)
        {
            ForumMessage::where("user_id", $userId)->whereIn("id", $id)->delete();
        }

        return $result;
    }

    /**
     * 阅读消息
     * @param int $id  消息编号
     */
    public static function read($userId, $id){
        $result =
            [
                'code'  => 0,
                'data'  => null,
                'msg'   => ""
            ];
        $formMessage = ForumMessage::where('user_id', $userId)
                                 //  ->where('id', $id)
                                   ->get();
        if(!$formMessage){
            $result['code'] = 30927;
            return $result;
        }
        foreach ($formMessage as $key => $value) {
            ForumMessage::where('id', $value['id'])->update(['read_time' => UTC_TIME]);
        }
        return $result;
    }

}
