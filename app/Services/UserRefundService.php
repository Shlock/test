<?php namespace YiZan\Services;

use YiZan\Models\UserRefund;
use YiZan\Utils\Helper;
use Exception;

class UserRefundService extends BaseService {
	/**
	 * 创建会员退款
	 * @param  [type] $userId   [description]
	 * @param  [type] $orderId  [description]
	 * @param  [type] $sellerId [description]
	 * @param  [type] $content  [description]
	 * @param  [type] $money    [description]
	 * @return [type]           [description]
	 */
	public static function createRefund($userId, $orderId, $sellerId, $staffId, $content, $money) {
		$userRefund = new UserRefund;
		$userRefund->user_id 		= $userId;
		$userRefund->order_id 		= $orderId;
		$userRefund->seller_id 		= $sellerId;
		$userRefund->staff_id 		= $staffId;
		$userRefund->content 		= $content;
		$userRefund->money 			= $money;
		$userRefund->create_time 	= UTC_TIME;
		$userRefund->create_day 	= UTC_DAY;
		$userRefund->status 		= 0;
		do {
	    	try {
	    		$userRefund->sn = Helper::getSn();
	    		$userRefund->save();
	    		$bln = true;
	    	} catch (Exception $e) {
	    		$bln = false;
	    	}
	    } while(!$bln);
	}
}
