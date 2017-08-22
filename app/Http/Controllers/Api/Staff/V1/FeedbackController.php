<?php 
namespace YiZan\Http\Controllers\Api\Staff;

use YiZan\Services\Staff\FeedbackService;

class FeedbackController extends BaseController {
	/**
	 * 意见反馈
	 */
	public function create() {
		$result = FeedbackService::create(
				$this->staffId,
				trim($this->request('content')),
				$this->request('deviceType')
			);
		$this->output($result);
	}

	
}