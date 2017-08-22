<?php namespace YiZan\Models;

class Promotion extends Base {
	//自动发放
	const SEND_TYPE_AUTOMATIC = 0;
	//会员领取
	const SEND_TYPE_USER      = 1;
	//手动发放
    const SEND_TYPE_HAND      = 2;
	
	// protected $visible = ['id', 'name', 'brief', 'money', 'end_time', 'seller', "send_count", "use_count", "isReceive", 'sendUserCount'];




	public function promotionSnCount() {
        return $this->hasManyCount('YiZan\Models\PromotionSn', 'promotion_id', 'id');
    }

    public function activityCount() {
        return $this->hasManyCount('YiZan\Models\ActivityPromotion', 'promotion_id', 'id');
    }


    public function seller(){
        return $this->belongsTo('YiZan\Models\Seller');
    }


    public function sellerCates() {
        return $this->hasMany('YiZan\Models\PromotionSellerCate', 'promotion_id', 'id');
    }

    public function unableDate() {
        return $this->hasMany('YiZan\Models\PromotionUnableDate', 'promotion_id', 'id');
    }
}