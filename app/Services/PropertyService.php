<?php namespace YiZan\Services;

use YiZan\Models\Seller;
use YiZan\Models\PropertyBuilding;
use YiZan\Models\District;
use YiZan\Models\PropertyUser;
use YiZan\Models\PropertyRoom;
use YiZan\Models\User;
use YiZan\Models\DoorAccess;
use YiZan\Models\PuserDoor;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use Illuminate\Database\Query\Expression;
use DB, Lang;

class PropertyService extends BaseService {
	
	public static function getProperty($districtId) {
		$data = Seller::join('district', function($join) use($districtId) {
								$join->on('district.seller_id', '=', 'seller.id')
									->where('district.id', '=', $districtId);
							})
							->select('seller.*')
							->with('authenticate')
							->first();
		if ($data) {
			$data->business_licence_img = $data->authenticate->business_licence_img;
		}
		return $data;
	}


}
