<?php 
namespace YiZan\Services\System;

use YiZan\Models\System\SellerWithdrawMoney;
use YiZan\Models\SellerExtend;
use YiZan\Utils\Time;
use Exception, DB, Lang, Validator, App;
use YiZan\Models\SellerMoneyLog;

class SellerWithdrawMoneyService extends \YiZan\Services\SellerWithdrawMoneyService {
    
    /**
     *
     *总后台提现列表显示
     *sellerName 商户名称
     */
    public static function lists($sellerName, $status,$beginTime, $endTime, $page, $pageSize) {
        $list = SellerWithdrawMoney::with('seller', 'extend', 'admin')->where('status', $status);
        if (!empty($sellerName)) {//搜索商户
           $list->where('seller_id',function($query) use ($sellerName) {
              $query->select('id')
                    ->from('seller')
                    ->where('name', $sellerName);
           });
        }
        if ($beginTime > 0) {
            $list->where('create_time', '>=', $beginTime);
        }
        if ($endTime > 0) {
            $list->where('create_time', '<', $endTime);
        }
        $total_count = $list->count();
        $list->orderBy('seller_withdraw_money.id', 'desc');
    
        $list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();        
        
		return ["list" => $list, "totalCount" => $total_count];
    }
    
	/**
	 * 获取提现列表
	 * @param  string  $sellerName   服务人员名称
	 * @param  string  $sellerMobile 服务人员手机号
	 * @param  string  $beginTime    创建开始时间
	 * @param  string  $endTime      创建结束时间
	 * @param  integer $status       处理状态
	 * @param  integer $page         页码
	 * @param  integer $pageSize     每页数量
	 * @return array                
	 */
	public static function getLists($sellerName, $sellerMobile, $beginTime, $endTime, $status, $page, $pageSize) {
		$list = SellerWithdrawMoney::with('admin', 'seller', 'authenticate');
		if (!empty($sellerName) || !empty($sellerMobile)) {//搜索名称或手机号
			$list->join('seller', function($join) use($sellerName, $sellerMobile) {
	            $join->on('seller.id', '=', 'seller_withdraw_money.seller_id');
	            if (!empty($sellerName)) {
	            	$join->where('seller.name', '=', $sellerName);
	            }
	            if (!empty($sellerMobile)) {
	            	$join->where('seller.mobile', '=', $sellerMobile);
	            }
	        });
		}

		if (!empty($beginTime)) {//创建开始时间
			$list->where('create_day', '>=', Time::toTime($beginTime));
		}

		if (!empty($endTime)) {//创建结束时间
			$list->where('create_day', '<=', Time::toTime($endTime));
		}

		if ($status > 0) {//状态
			$list->where('status', $status - 1);
		}
		$total_count = $list->count();
		$list->orderBy('seller_withdraw_money.id', 'desc');
		
		$list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
		return ["list" => $list, "totalCount" => $total_count];
	}
   
	public static function dispose($adminId, $id, $content, $status) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ''
		);
		// if (empty($content)) {//处理结果不能为空
		//     $result['code'] = 40102;
		//     return $result;
		// }
		$withdraw = SellerWithdrawMoney::find($id);
		if (!$withdraw) {//提现不存在
			$result['code'] = 40101;
			return $result;
		}

        // 已处理
		if ($withdraw->status == STATUS_WITHDRAW_PASS ||
            $withdraw->status == STATUS_WITHDRAW_REFUSE) 
        {
			$result['code'] = 40103;
			return $result;
		}
		DB::beginTransaction();
        try {
           
            // 成功
            if($status == STATUS_WITHDRAW_PASS)
            {
                $withdraw->status  = STATUS_WITHDRAW_PASS;
                $extend = SellerExtend::where("seller_id", $withdraw->seller_id)->first();
                $extend->use_money += $withdraw->money;
                $extend->save();
            }
            // 1：未通过
            if($status == STATUS_WITHDRAW_REFUSE)
            {
                $withdraw->status          = STATUS_WITHDRAW_REFUSE;
                
                $extend = SellerExtend::where("seller_id", $withdraw->seller_id)->first();
            
                $extend->money += $withdraw->money;
            
                $extend->save();
                
            }            
            $withdraw->dispose_admin   = $adminId;
            $withdraw->dispose_time    = UTC_TIME;
            $withdraw->dispose_remark  = $content;
            $withdraw->save();
            
            \YiZan\Models\SellerMoneyLog::where('related_id',$withdraw->id)->update(['status' => $status]);
            
            DB::commit();
            $bln = true;
        }catch (Exception $e){
           // print_r($e);
            DB::rollback();
            $bln = false;
            $result['code'] = 40104;
        }
		return $result;
	}
}
