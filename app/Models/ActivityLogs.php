<?php 
namespace YiZan\Models;

class ActivityLogs extends Base
{
    public function user(){
        return $this->belongsTo('YiZan\Models\User','user_id','id');
    }
}
