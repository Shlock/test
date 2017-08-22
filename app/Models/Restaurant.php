<?php namespace YiZan\Models;

class Restaurant extends Base {
    protected $visible = ['id','seller_id','name','logo','tel','mobile','contacts','begin_time','end_time','license_img','license','expired','permits_img','permits','create_time','status','sort','dispose_time','dispose_admin_id','dispose_result','dispose_status', 'source', 'address','sale_count','comment_count', 'star', 'adminuser', 'seller', 'businessHours', 'collect', 'isCollect'];

    protected $appends = ['businessHours'];


    protected $casts = [
        'isDefault' => 'boolean',
    ];

    public function adminuser() {
        return $this->belongsTo('YiZan\Models\AdminUser', 'dispose_admin_id', 'id');
    }

    public function seller() {
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id', 'id');
    }

    public function getBusinessHoursAttribute() {
        return $this->attributes['begin_time'].' - '.$this->attributes['end_time'];
    }


}

