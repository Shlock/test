<?php namespace YiZan\Models;

class UserCollectRestaurant extends Base {
	protected $visible = ['id', 'create_time', 'restaurant'];

	public function restaurant(){
        return $this->belongsTo('YiZan\Models\Restaurant', 'restaurant_id', 'id');
    }
}
