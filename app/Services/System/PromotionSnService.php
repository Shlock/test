<?php namespace YiZan\Services\System;

use YiZan\Models\System\PromotionSn;
use YiZan\Models\System\Promotion;
use YiZan\Models\System\Seller;
use YiZan\Models\System\User;

use YiZan\Utils\String;
use YiZan\Utils\Time;
use Lang, DB;

class PromotionSnService extends \YiZan\Services\PromotionSnService {
    /**
     * 优惠券兑换码列表
     * @param string $sn 序列号
     * @param int $promotionId  优惠券编号
     * @param int $status 状态
     * @param string $actName 活动名称
     * @param string $mobile 会员手机号
     * @param date $beginTime 发放开始时间
     * @param date $endTime 发放结束时间
     * @param int $actType 活动类型
     * @param int $page
     * @param int $pageSize
     * @return array
     */
	public static function getLists($sn, $promotionId, $status,$actName,$mobile,$beginTime,$endTime, $actType, $page, $pageSize) {
		$list = PromotionSn::with('seller', 'user', 'promotion','activity')->where('is_del',0);
		
		if (!empty($sn)) {
			$list->where('sn', $sn);
		}

		if ($promotionId > 0) {
			$list->where('promotion_id', $promotionId);
		}

		if ($status > 0) {
            if ($status == 1) {
                $list->where('user_id',0);
            } elseif ($status == 2) {
                $list->where('use_time',0);
            } elseif ($status == 3) {
                $list->where('use_time', '>', 0);
            } else {
                $list->where('expire_time','<',UTC_DAY);
            }
		}

        if ($actName != '') {
            $actName = String::strToUnicode($actName,'+');
           $list->whereIn('activity_id', function($query) use ($actName){
               $query->select('id')
                   ->from('activity')
                   ->whereRaw('MATCH(name_match) AGAINST(\'' . $actName . '\' IN BOOLEAN MODE)');
           });
        }

        if ($mobile != '') {
            $list->where('user_id', function($query) use ($mobile){
                $query->select('id')
                    ->from('user')
                    ->where('mobile', $mobile);
            });
        }

        if ($beginTime > 0) {
            $list->where('create_time', '>', $beginTime);
        }

        if ($endTime > 0) {
            $list->where('create_time', '<', $endTime);
        }

        if ($actType > 0) {
            $list->whereIn('activity_id',function($query) use ($actType){
               $query->select('id')
                    ->from('activity')
                    ->where('type', $actType);
            });
        }



		$total_count = $list->count();
		$list->orderBy('id', 'desc');
		$list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
		return ["list" => $list, "totalCount" => $total_count];
	}


	/**
	 * 删除优惠券
	 * @param  [type] $ids     [description]
	 * @return [type]         [description]
	 */
	public static function deletePromotionSn($ids) {
        $result = array(
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> Lang::get('api_system.success.delete')
        );
		$res = PromotionSn::whereIn('id', explode(',',$ids))->update(['is_del' => 1]);
	    return $result;
	}
}
