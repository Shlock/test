<?php namespace YiZan\Models;
use YiZan\Utils\Time;
class StaffLeave extends Base
{
    public function staff(){
        return $this->belongsTo('YiZan\Models\SellerStaff','staff_id','id');
    }

    public function seller(){
        return $this->belongsTo('YiZan\Models\Seller','seller_id','id');
    }

    public function getBeginTimeAttribute() {
        if (!isset($this->attributes['begin_time'])) {
            return false;
        }
        return Time::ToDate($this->attributes['begin_time'],'Y-m-d H:i:s');
    }

    public function getEndTimeAttribute() {
        if (!isset($this->attributes['end_time'])) {
            return false;
        }
        return Time::ToDate($this->attributes['end_time'],'Y-m-d H:i:s');
    }

    public function getCreateTimeAttribute() {
        if (!isset($this->attributes['create_time'])) {
            return false;
        }
        return Time::ToDate($this->attributes['create_time'],'Y-m-d H:i:s');
    }

    public function getDisposeTimeAttribute() {
        if (!isset($this->attributes['dispose_time'])) {
            return false;
        }
        return Time::ToDate($this->attributes['dispose_time'],'Y-m-d H:i:s');
    }

    public function getIsAgreeAttribute() {
        $result = '';
        switch ($this->attributes['status']) {
            case '0' : $result = '待处理'; break;
            case '1' : $result = '同意'; break;
            case '-1' : $result = '拒绝'; break;
        }
        return $result;
    }
}
