<?php 
namespace YiZan\Services;

use YiZan\Models\ForumMessage;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use DB, Validator, View, Exception;
/**
 * 站内消息
 */
class ForumMessageService extends BaseService {

    /**
     * 消息列表(总后台)
     * @param int $page 页码
     * @param int $pageSize 每页数量
     */
    public static function getSystemList($page, $pageSize) {
        $count = ForumMessage::count();
        $list = ForumMessage::skip(($page - 1) * $pageSize)
                                ->take($pageSize)
                                ->with('user')
                                ->get()
                                ->toArray();
        foreach ($list as $key => $val) {
            $list[$key]['username'] = $val['user']['name'];
            unset($list[$key]['user']);
        }
        return ['list' => $list, 'totalCount' => $count];
    }

    /**
     * 删除消息
     * @param string $id 消息编号 多个用","隔开
     */
    public static function deleteSystem($id) {
        $result = [
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> ""
        ];
        ForumMessage::whereIn('id', explode(',',$id))->delete();
        return $result;
    } 

    /**
     * 创建论坛消息
     * @param int $type  类型 1:系统消息 2:其他 (后续待增加)
     * @param string $title  标题
     * @param string $content  内容
     * @param int $relateUserId  关联会员编号
     * @param int $userId  会员编号
     * @param int $postsId  关联编号(帖子编号) 
     * @param int $sendType  1:普通消息 2:html页面 args为url 3:订单消息 args为订单ID
     */
    public static function create($type, $title, $content, $relateUserId, $userId, $postsId, $args, $sendType = 1){ 
        $result = true;
        try{ 
            $forumMessage = new ForumMessage();
            $forumMessage->type             = $type;
            $forumMessage->title            = $title;
            $forumMessage->content          = $content;
            $forumMessage->relate_user_id   = $relateUserId;
            $forumMessage->user_id          = $userId;
            $forumMessage->posts_id         = $postsId; 
            $forumMessage->send_time        = UTC_TIME; 
            $forumMessage->send_type        = $sendType;
            $forumMessage->save();
        } catch(Exception $e){
            print_r($e->getMessage());
            $result = false;
        }
        return $result;
    }
}
