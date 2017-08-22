<?php namespace YiZan\Services\System;

use YiZan\Models\System\Seller;
use YiZan\Models\System\SellerCertificate;

use YiZan\Utils\String;
use DB;

class SellerCertificateService extends \YiZan\Services\SellerCertificateService {
	/**
     * 获取认证列表
     * @param  int $type 机构类型
	 * @param  string  $mobileName     服务人员名称手机号
	 * @param  integer $status   认证状态
	 * @param  integer $page     页码
	 * @param  integer $pageSize 每页显示数量
	 * @return array            
	 */
	public static function getLists($type, $mobileName, $status, $page, $pageSize) {
		$list = SellerCertificate::with('admin','seller');
        
        $dbPrefix = DB::getTablePrefix();
        
		$match = empty($mobileName) ? '' : String::strToUnicode($mobileName,'+');
        
        if (!empty($match)) {
        	$list->select(DB::raw("{$dbPrefix}seller_certificate.*"))
                ->join('seller', 'seller.id', '=', 'seller_certificate.seller_id');
			$list->whereRaw('MATCH('.env('DB_PREFIX').'seller.name_match) AGAINST(\'' . $match . '\' IN BOOLEAN MODE)');
            
            if($type == true)
            {
                $list->where("seller.type", $type); 
            }
		}
        else if($type == true)
        {
            $list->select(DB::raw("{$dbPrefix}seller_certificate.*"))
                ->join('seller', "seller.id", "=", "seller_certificate.seller_id")
                ->where("seller.type", $type); 
        }

		if ($status > 0) {//状态
			$list->where('status', $status - 2);
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
	public static function getCertificate($sellerId) {
		if ($sellerId < 1) {
			return false;
		}
		return SellerCertificate::where('seller_id', $sellerId)->with('seller')->first();
	}

	/**
	 * 更新认证信息
	 * @param  integer $adminId  管理员编号
	 * @param  integer $sellerId 服务人员编号
	 * @param  string  $remark   处理备注
	 * @param  integer $status   处理状态
	 * @return array            
	 */
	public static function updateCertificate($adminId, $sellerId, $remark, $status) {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '更新认证信息成功'
		);

		$certificate = SellerCertificate::where('seller_id', $sellerId)->first();
		if (!$certificate) {//认证信息不存在
			$result['code'] = 30701;
			return $result;
		}

		if (empty($remark)) {//备注信息不能为空
			$result['code'] = 30702;
			return $result;
		}

		$seller_status = $status == 1 ? 1 : 0;
		Seller::where('id', $sellerId)->update(['is_certificate' => $seller_status]);

        SellerCertificate::where('seller_id', $sellerId)->update([
                "status"          => $status,
		        "dispose_admin"   => $adminId,
		        "dispose_time"    => UTC_TIME,
		        "dispose_remark"  => $remark
            ]);
		
		return $result;
	}
}