<?php namespace YiZan\Services;

use Illuminate\Support\Facades\Lang;
use YiZan\Models\UserCollect;
use YiZan\Models\Goods;
use YiZan\Models\Seller;
use DB, Exception;

class CollectService extends BaseService {
	/**
	 * 收藏餐厅列表
	 * @param  [type] $userId [description]
	 * @param  [type] $page   [description]
	 * @return [type]         [description]
	 */
	public static function restaurantList($userId, $page) {
        $list = [];
		$lists =  UserCollectRestaurant::where('user_id', $userId)
								->with('restaurant')
								->skip(($page - 1) * 20)
								->take(20)
								->get()
								->toArray();

        foreach ($lists as $key=>$val) {
            $list[$key] = [
                'id' => $val['restaurant']['id'],
                'name' => $val['restaurant']['name'],
                'businessHours' => $val['restaurant']['businessHours'],
                'logo' => $val['restaurant']['logo'],
                'saleCount' => $val['restaurant']['saleCount'],
                'star' => $val['restaurant']['star'],
                'commentCount' => $val['restaurant']['commentCount'],
                'address' => $val['restaurant']['address'],
                'isCollect' => 1
            ];
        }

		return $list;
	}
	/**
	 * 收藏餐厅
	 * @param  [type] $userId  [description]
	 * @param  [type] $id [餐厅编号]
	 * @return [type]          [description]
	 */
	public static function collectRestaurant($userId, $id) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.collect_res_create')
        ];
		$checkRes = Restaurant::where('id', $id)->first();
		if (!$checkRes) {
			$result['code'] = '10501';
            return $result;
		}

        $check = UserCollectRestaurant::where('user_id', $userId)
                            ->where('restaurant_id', $id)
                            ->first();
        if ($check) {
            $result['code'] = '10504';
            return $result;
        }

		$res = UserCollectRestaurant::insert([
                'user_id' => $userId,
                'restaurant_id' => $id,
                'create_time' => UTC_TIME
        ]);
        if (!$res) {
            $result['code'] = '10502';
            return $result;
        }
        return $result;
	}

	/**
	 * 删除餐厅收藏
	 * @param  [type] $userId  [description]
	 * @param  [type] $id [餐厅编号]
	 * @return [type]          [description]
	 */
	public static function deleteRestaurant($userId, $id) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.collect_res_delete')
        ];
        $res = UserCollectRestaurant::where('user_id', $userId)
                        ->where('restaurant_id', $id)
                        ->delete();
        if (!$res) {
            $result['code'] = '10503';
            return $result;
        }
        return $result;
	}


}
