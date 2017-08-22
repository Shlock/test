<?php namespace YiZan\Models;

class ForumComplain extends Base 
{
    public function user()
    {
        return $this->belongsTo('YiZan\Models\User', 'user_id');
    }
    
	
    public function posts()
    {
        return $this->belongsTo('YiZan\Models\ForumPosts', 'post_id', 'id');
    } 
    
}
