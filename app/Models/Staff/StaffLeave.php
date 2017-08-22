<?php namespace YiZan\Models\Staff;

class StaffLeave extends \YiZan\Models\StaffLeave
{
    protected $visible = ['id', 'begin_time','end_time', 'remark','create_time','staff','status','dispose_time','dispose_result', 'staff_id','seller_id','seller','statusStr'];

    protected  $appends = ['statusStr'];

    public function getStatusStrAttribute() {
        $result = '';
        switch ($this->attributes['status']) {
            case '0' : $result = '待审核'; break;
            case '1' : $result = '同意'; break;
            case '-1' : $result = '驳回'; break;
        }
        return $result;
    }
}
