<?php namespace YiZan\Models;

class ForumPosts extends Base 
{
	
    public function user()
    {
        return $this->belongsTo('YiZan\Models\User', 'user_id');
    }
    
    public function plate()
    {
        return $this->belongsTo('YiZan\Models\ForumPlate', 'plate_id');
    } 
	
    public function address()
    {
        return $this->belongsTo('YiZan\Models\UserAddress', 'address_id');
    }

    public function posts()
    {
        return $this->belongsTo('YiZan\Models\ForumPosts', 'pid');
    }

    public function replyPosts()
    {
        return $this->belongsTo('YiZan\Models\ForumPosts', 'reply_id');
    }

    public function praise()
    {
        return $this->belongsTo('YiZan\Models\UserPraise', 'user_id', 'user_id');
    } 
}
