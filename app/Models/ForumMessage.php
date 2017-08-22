<?php 
namespace YiZan\Models;

/**
 * 推送信息
 */
class ForumMessage extends Base 
{

    protected $visible = ['id', 'type', 'user', 'title', 'content', 'user_id', 'send_time', 'read_time', 'typeStr', 'forum', 'relateUser', 'relate_id', 'posts_id', 'posts'];
    protected $appends = ['typeStr'];

    public function user()
    {
        return $this->belongsTo('YiZan\Models\User', 'user_id');
    }

    public function relateUser()
    {
        return $this->belongsTo('YiZan\Models\User', 'relate_user_id');
    }

    public function posts()
    {
        return $this->belongsTo('YiZan\Models\ForumPosts', 'posts_id');
    }

    public function getTypeStrAttribute() {
        $type = ['1' => '系统消息', '2' => '其他消息'];
        return $type[$this->attributes['type']];
    }
}
