<?php namespace YiZan\Services\Staffwap;
 

class SellerStaffService extends \YiZan\Services\SellerStaffService {
	/**
	 * 获取服务人员
	 * 根据编号获取员工
	 * @param  integer $id 		员工编号
	 * @return array            员工信息
	 */
	public static function getById($id) {
		return SellerStaff::with('user')->find($id);
	}
 
}
