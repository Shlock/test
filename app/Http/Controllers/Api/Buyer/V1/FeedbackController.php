<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\FeedbackService;

class FeedbackController extends UserAuthController {
	/**
	 * 意见反馈
	 */
	public function create() {
		$result = FeedbackService::create(
				$this->userId,
				trim($this->request('content')),
				$this->request('deviceType')
			);
		$this->output($result);
	}

	
}