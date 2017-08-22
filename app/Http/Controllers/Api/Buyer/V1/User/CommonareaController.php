<?php 
namespace YiZan\Http\Controllers\Api\Buyer\User;

use YiZan\Http\Controllers\Api\Buyer\UserAuthController;
use YiZan\Services\UserCommonareaService;

class CommonareaController extends UserAuthController {
	/**
	 * 获取会员的常用小区列表
	 */
	public function lists() {
		$data = UserCommonareaService::getCommonareaList($this->userId);
		$this->outputData($data);
	}

	/**
	 * 清空会员常用小区列表
	 */
	public function delete() {
		$data = UserCommonareaService::deleteCommonarea($this->userId);
		$this->outputData($data);
	}

	/**
	 * 添加会员常用小区列表
	 */
	public function add() {
		$data = UserCommonareaService::addCommonarea(
			$this->userId,
			$this->request('districtId')
		);
		$this->outputData($data);
	}
	
}