<?php namespace YiZan\Models;

class AdminUser extends Base 
{
    public function role() {
        return $this->belongsTo('YiZan\Models\AdminRole', 'rid', 'id');
    }

    public function citys() {
        return $this->hasMany('YiZan\Models\AdminUserCity', 'admin_user_id', 'id');
    }
}
