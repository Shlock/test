<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\StaffComplainService;

class StaffcomplainController extends UserAuthController {
	/**
	 * 服务人员举报增加
	 */
	public function create() {
		$result = StaffComplainService::create(
				$this->userId,
				(int)$this->request('staffId'),
				trim($this->request('content'))
			);
		$this->output($result);
	}

	
}