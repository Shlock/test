<?php namespace YiZan\Models\Www;
use Time;
class Restaurant extends \YiZan\Models\Restaurant {
	protected $visible = ['id','seller_id','name','logo','tel','mobile','contacts','begin_time','end_time','license_img','license','expired','permits_img','permits','create_time','status','sort','dispose_time','dispose_admin_id','dispose_result','dispose_status', 'source', 'address','sale_count','comment_count', 'star', 'adminuser', 'seller', 'businessHours', 'collect', 'isCollect','isBusiness','collectCount'];

	protected $appends = ['businessHours','isBusiness'];
	public function collect(){
        return $this->hasMany('YiZan\Models\UserCollectRestaurant', 'restaurant_id', 'id');
    }
	
	public function getIsBusinessAttribute() {
		$isBusiness = 0;
		$beginTime = str_replace(':','',$this->attributes['begin_time']);
		$endTime = str_replace(':','',$this->attributes['end_time']);
		$time = Time::toDate(UTC_TIME, 'Hi');
		if ($time > $beginTime && $time < $endTime) {
			$isBusiness = 1;
		}
        return $isBusiness;
    }
}
