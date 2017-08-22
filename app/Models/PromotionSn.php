<?php namespace YiZan\Models;

class PromotionSn extends Base {
//	protected $visible = ['id', 'sn', 'send_time', 'expire_time', 'use_time', 'status', 'promotion'];
    protected $appends = ['statusStr'];

	public function promotion(){
        return $this->belongsTo('YiZan\Models\Promotion');
    }

    public function user(){
        return $this->belongsTo('YiZan\Models\System\User');
    }

    public function seller(){
        return $this->belongsTo('YiZan\Models\System\Seller');
    }

    public function activity(){
        return $this->belongsTo('YiZan\Models\Activity');
    }

    public function getStatusStrAttribute() {
        $str = '';
        if ($this->attributes['user_id'] == 0) {
           $str = '未兑换';
        } elseif ($this->attributes['use_time'] == 0) {
            $str = '未使用';
        } elseif ($this->attributes['use_time'] > 0) {
            $str = '已使用';
        } elseif ($this->attributes['expire_time'] < UTC_TIME) {
            $str = '已过期';
        }
        return $str;
    }


}
