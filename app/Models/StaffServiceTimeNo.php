<?php namespace YiZan\Models;

class StaffServiceTimeNo extends Base
{
    public function stime(){
        return $this->belongsTo('YiZan\Models\StaffServiceTime','id','service_time_id');
    }
}
