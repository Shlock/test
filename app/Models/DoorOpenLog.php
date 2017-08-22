<?php 
namespace YiZan\Models;

/**
 * 开门记录
 */
class DoorOpenLog extends Base {

    public function seller() {
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id');
    }

    public function district() {
        return $this->belongsTo('YiZan\Models\District', 'district_id');
    }

    public function door() {
        return $this->belongsTo('YiZan\Models\DoorAccess', 'door_id');
    }

    public function puser() {
        return $this->belongsTo('YiZan\Models\PropertyUser', 'puser_id');
    }

    public function build() {
        return $this->belongsTo('YiZan\Models\PropertyBuilding', 'build_id');
    }

    public function room() {
        return $this->belongsTo('YiZan\Models\PropertyRoom', 'room_id');
    }

}
