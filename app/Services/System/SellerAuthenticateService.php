<?php namespace YiZan\Services\System;

use YiZan\Models\System\Seller;
use YiZan\Models\System\SellerAuthenticate;
use DB;

class SellerAuthenticateService extends \YiZan\Services\SellerAuthenticateService {
	/**
     * 获取认证列表
     * @param  int $type 机构类型
	 * @param  string  $realName 真实名称
	 * @param  string  $mobile   服务人员手机号
     * @param  string  $idcardSn 身份证号码
     * @param  string  $companyName 公司名称
     * @param  string  $businessLicenceSn 营业执照号
	 * @param  integer $status   认证状态
	 * @param  integer $page     页码
	 * @param  integer $pageSize 每页显示数量
	 * @return array            
	 */
	public static function getLists($type, $realName, $mobile, $idcardSn, $companyName, $businessLicenceSn, $status, $page, $pageSize) 
    {
		$list = SellerAuthenticate::with('admin','seller');
        $list->join('seller', "seller.id", "=", "seller_authenticate.seller_id");
        $list->where('seller.is_del', 0);

        $dbPrefix = DB::getTablePrefix();
        $list->select(DB::raw("{$dbPrefix}seller_authenticate.*"));

		if (!empty($realName)) {//搜索真实名称
			$list->where('real_name', $realName);
		}

		//跑腿不区分机构和个人
		if($type == true) {
        	//$list->where("seller.type", $type); 
        }

		if (!empty($mobile)) {//搜索电话号码
			$list->where('seller.mobile', $mobile);
		}

		if (!empty($idcardSn)) {//身份证号码
			$list->where('seller_authenticate.idcard_sn', $idcardSn);
		}
        
        if (!empty($companyName)) 
        {
			$list->where('seller_authenticate.company_name', $companyName);
		}
        
        if (!empty($businessLicenceSn)) 
        {
			$list->where('seller_authenticate.business_licence_sn', $businessLicenceSn);
		}

		if ($status > 0) {//状态
			$list->where('seller_authenticate.status', $status - 2);
		}

		$total_count = $list->count();

		$list->orderBy('update_time', 'desc');
		$list->orderBy('seller_id', 'desc');
		$list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
 
		return ["list" => $list, "totalCount" => $total_count];
	}

	/**
	 * 获取认证信息
	 * @param  integer $sellerId 服务人员编号
	 * @return object|false            
	 */
	public static function getAuthenticate($sellerId) {
		if ($sellerId < 1) {
			return false;
		}

		return SellerAuthenticate::where('seller_id', $sellerId)->with('seller')->first();
	}

	/**
	 * 更新认证信息
	 * @param  integer $adminId  管理员编号
	 * @param  integer $sellerId 服务人员编号
	 * @param  string  $remark   处理备注
	 * @param  integer $status   处理状态
	 * @return array            
	 */
	public static function updateAuthenticate($adminId, $sellerId, $remark, $status) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ''
		);

		$authenticate = SellerAuthenticate::where('seller_id', $sellerId)->first();
		if (!$authenticate) {//认证信息不存在
			$result['code'] = 30501;
			return $result;
		}

		if (empty($remark)) {//备注信息不能为空
			$result['code'] = 30502;
			return $result;
		}

		$seller_status = $status == 1 ? 1 : 0;
		Seller::where('id', $sellerId)->update(['status' => $seller_status, 'is_authenticate' => $seller_status]);

        SellerAuthenticate::where('seller_id', $sellerId)->update([
                "status"          => $status,
		        "dispose_admin"   => $adminId,
		        "dispose_time"    => UTC_TIME,
		        "dispose_remark"  => $remark
            ]);
        
		return $result;
	}
}