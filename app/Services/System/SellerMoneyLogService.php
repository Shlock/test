<?php namespace YiZan\Services\System;

use YiZan\Models\System\SellerMoneyLog;
use YiZan\Utils\Time;

class SellerMoneyLogService extends \YiZan\Services\SellerMoneyLogService {
	/**
	 * 获取提现列表
	 * @param  string  $sellerName   服务人员名称
	 * @param  string  $sellerMobile 服务人员手机号
	 * @param  string  $beginTime    创建开始时间
	 * @param  string  $endTime      创建结束时间
	 * @param  integer $page         页码
	 * @param  integer $pageSize     每页数量
	 * @return array                
	 */
	public static function getLists($sellerName, $sellerMobile, $beginTime, $endTime, $page, $pageSize) {
		$list = SellerMoneyLog::with('seller');

		if (!empty($sellerName) || !empty($sellerMobile)) {//搜索名称或手机号
			$list->join('seller', function($join) use($sellerName, $sellerMobile) {
	            $join->on('seller.id', '=', 'seller_money_log.seller_id');
	            if (!empty($sellerName)) {
	            	$join->where('seller.name', '=', $sellerName);
	            }
	            if (!empty($sellerMobile)) {
	            	$join->where('seller.mobile', '=', $sellerMobile);
	            }
	        });
		}

		if (!empty($beginTime)) {//创建开始时间
			$list->where('seller_money_log.create_day', '>=', Time::toTime($beginTime));
		}

		if (!empty($endTime)) {//创建结束时间
			$list->where('seller_money_log.create_day', '<=', Time::toTime($endTime));
		}

		$total_count = $list->count();
		$list->orderBy('seller_money_log.id', 'desc');
		
		$list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
		return ["list" => $list, "totalCount" => $total_count];
	}
}
