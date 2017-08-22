<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\ForumPlateService;

/**
 * 板块
 */
class ForumplateController extends BaseController {
	
	/**
	 * [lists 板块列表]
	 */
	public function lists(){
	    $result = ForumPlateService::lists();
	    $this->outputData($result);
	}

	/**
	 * [get 板块]
	 */
	public function get(){
	    $result = ForumPlateService::get($this->request('id'));
	    $this->outputData($result);
	}

}