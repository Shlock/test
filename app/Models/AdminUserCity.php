<?php namespace YiZan\Models;

class AdminUserCity extends Base {
    public function city() {
        return $this->belongsTo('YiZan\Models\Region', 'city_id', 'id');
    }
}
