<?php 
namespace YiZan\Models;

class AdminRole extends Base 
{
    public function access()
    {
        return $this->hasMany('YiZan\Models\AdminRoleAccess', 'rid', 'id');
    }
}
