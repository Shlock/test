<?php namespace YiZan\Models;

class SellerServiceTimeSet extends Base
{
    protected $visible = ['id','week','weeks','hours','stime'];

    protected $appends = ['weeks'];

    public function stime(){
        return $this->hasMany('YiZan\Models\SellerServiceTime','service_time_id','id');
    }

    public function getWeeksAttribute() {
        if (!isset($this->attributes['week'])) {
            return false;
        }
        $value = '';
        $weeks = ['周日','周一','周二','周三','周四','周五','周六'];
        $week = json_decode($this->attributes['week']);
        foreach ($week as $key=>$val) {
            $value[$key] = $weeks[$val];
        }
        return implode(' ',$value);
    }

    public function getWeekAttribute() {
        if (!isset($this->attributes['week'])) {
            return false;
        }
        return json_decode($this->attributes['week']);
    }

    public function getHoursAttribute() {
        if (!isset($this->attributes['hours'])) {
            return false;
        }
        return json_decode($this->attributes['hours']);
    }
}
