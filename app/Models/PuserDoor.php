<?php namespace YiZan\Models;

class PuserDoor extends Base { 
	
    public function puser()
    {
        return $this->belongsTo('YiZan\Models\PropertyUser', 'puser_id');
    }

    public function door()
    {
        return $this->belongsTo('YiZan\Models\DoorAccess', 'door_id');
    }
    
}
