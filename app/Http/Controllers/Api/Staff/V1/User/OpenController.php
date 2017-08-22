<?php 
namespace YiZan\Http\Controllers\Api\Staff\User;
use YiZan\Services\Staff\StaffService;
use YiZan\Http\Controllers\Api\Staff\BaseController;

class OpenController extends BaseController {
	/**
	 * 更新员工信息
	 */
	public function status() {
        
		$result = [
            'code'	=> 0,
            'data'	=> null,
            'msg' => ""
        ];
		$this->output($result);
	}


}