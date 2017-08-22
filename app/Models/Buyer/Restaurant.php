<?php namespace YiZan\Models\Buyer;

class Restaurant extends \YiZan\Models\Restaurant {
	
	public function collect(){
        return $this->hasMany('YiZan\Models\UserCollectRestaurant', 'restaurant_id', 'id');
    }
}
