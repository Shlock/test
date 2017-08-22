<?php namespace YiZan\Services;

use YiZan\Models\SellerPayLog;
use YiZan\Models\SellerExtend;
use YiZan\Models\Seller;
use YiZan\Utils\Helper;
use Exception;

class SellerPayLogService extends BaseService {

	/**
	 * 商家支付列表
	 * @param  string  $userName   会员名称
	 * @param  string  $userMobile 会员手机号
	 * @param  string  $beginTime  创建开始时间
	 * @param  string  $endTime    创建结束时间
	 * @param  string  $payment    支付方式
	 * @param  integer $page       页码
	 * @param  integer $pageSize   每页数量
	 * @return array                
	 */
	public static function getLists($userName, $userMobile, $beginTime, $endTime, $payment, $page, $pageSize) {
		$list = SellerPayLog::with('seller');

		if ($userName == true) {//搜索名称
			$sellerIds = Seller::where('name', 'like', '%'.$userName.'%')
							   ->lists('id');
			$list->whereIn('seller_id', $userIds);
		}

		if ($userMobile == true) {//搜索手机号
			$sellerIds = Seller::where('mobile', 'like', '%'.$userMobile.'%')
							   ->lists('id');
			$list->whereIn('seller_id', $sellerIds);
		}

		if (!empty($payment)) {//支付方式
			$list->where('payment_type', $payment);
		}

		if (!empty($beginTime)) {//创建开始时间
			$list->where('create_day', '>=', Time::toTime($beginTime));
		}

		if (!empty($endTime)) {//创建结束时间
			$list->where('create_day', '<=', Time::toTime($endTime));
		}

		$total_count = $list->count();
		$list->orderBy('id', 'desc');
		
		$list = $list->skip(($page - 1) * $pageSize)
					 ->take($pageSize)
					 ->get()
					 ->toArray();
		return ["list" => $list, "totalCount" => $total_count];
	}

}