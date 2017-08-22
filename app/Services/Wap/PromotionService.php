<?php namespace YiZan\Services\Wap;
      use YiZan\Http\Controllers\Api\Wap\UserAuthController;
      use YiZan\Services\Wap\PromotionService;

class PromotionService extends \YiZan\Services\PromotionService
{

	/**
	 * [generatePromotion 发放优惠券]
	 * @param  integer $id            优惠券编号
	 * @param  string  $prefix        前缀
	 * @param  integer $generateCount 生成张数
	 * @param  array   $userIds       要发放的会员
	 * @return [type]                 
	 */
	public static function generatePromotion($id, $prefix, $generateCount, $userIds) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ''
		);

		if ($id < 1) {//优惠券不存在
    		$result['code'] = 40409;
    		return $result;
    	}

		if ($generateCount < 1) {//请设置生成数量
    		$result['code'] = 40411;
    		return $result;
    	}

    	if ($generateCount > 500) {//一次最多只能生成500张
    		$result['code'] = 40412;
    		return $result;
    	}

    	$promotion = Promotion::find($id);
    	if (!$promotion) {//优惠券不存在
    		$result['code'] = 40409;
    		return $result;
    	}

    	if ($promotion->send_count > 0) {
    		$use_send_count = PromotionSn::where('promotion_id', $id)->count();
    		if ($use_send_count + $generateCount > $promotion->send_count){
    			//生成数量超过，优惠券最大发放数量
    			$result['code'] = 40413;
    			return $result;
    		}
    	}

    	if (is_array($userIds) && count($userIds) > 0) {
    		$user_count = User::whereIn('id', $userIds)->count();
    		if ($user_count != count($userIds)) {
    			//会员不存在
    			$result['code'] = 40414;
    			return $result;
    		}
    	}

    	$prefix = strlen($prefix) > 3 ? substr($prefix, 0 , 3) : $prefix;
    	$sn_length = 8 - strlen($prefix);

    	DB::beginTransaction();
		try {
			do {
				$user_id = 0;
				if (is_array($userIds) && count($userIds) > 0) {
					$user_id = array_shift($userIds);
				}
				$promotionSn = new PromotionSn;
				$promotionSn->promotion_id = $id;
				$promotionSn->seller_id = $promotion->seller_id;
				$promotionSn->user_id = $user_id;
				$promotionSn->create_time = UTC_TIME;
				if ($user_id > 0) {
					$promotionSn->send_time = UTC_TIME;
					$promotionSn->expire_time = $promotion->end_time;
					if ($promotion->expire_day > 0) {//如果有有效天数
						$promotionSn->expire_time = UTC_TIME + $promotion->expire_day * 86400;
					}
				}

				do {
			    	try {
			    		$promotionSn->sn = $prefix . String::randString($sn_length, 1);
			    		$promotionSn->save();
			    		$bln = true;
			    	} catch (Exception $e) {
			    		$bln = false;
			    	}
			    } while(!$bln);

				$generateCount--;
			} while($generateCount > 0);
			DB::commit();
		} catch (Exception $e) {
    		DB::rollback();
    		$result['code'] = 40417;
    	}
    	return $result;
	}
}
