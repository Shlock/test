<?php 
namespace YiZan\Http\Controllers\Api\Buyer\Collect;

use YiZan\Http\Controllers\Api\Buyer\UserAuthController;
use YiZan\Services\Buyer\CollectService;
use Lang;

/**
 * 服务人员收藏
 */
class StaffController extends UserAuthController {
	/**
	 * 评价
	 */
	public function lists() {
		$data = CollectService::staffList(
				$this->userId,
				max((int)$this->request('page'), 1)
			);
		$this->outputData($data);
	}

	/**
	 * 添加收藏
	 */
	public function create() {
		$status = CollectService::collectStaff($this->userId, (int)$this->request('staffId'));
		if (!$status) {
			$this->outputCode(10405);
		}
		$this->outputCode(0, Lang::get('api.success.collect_staff_create'));
	}

	/**
	 * 删除收藏
	 */
	public function delete() {
		$status = CollectService::deleteStaff($this->userId, (int)$this->request('staffId'));
		if (!$status) {
			$this->outputCode(10406);
		}
		$this->outputCode(0, Lang::get('api.success.collect_staff_delete'));
	}
}