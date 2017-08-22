<?php 
namespace YiZan\Http\Controllers\Api\Staff\User;
use YiZan\Services\Staff\StaffService;
use YiZan\Http\Controllers\Api\Staff\BaseController;

class InfoController extends BaseController {
	/**
	 * 更新员工信息
	 */
	public function update() {
		$result = StaffService::updateInfo(
                    $this->userId,
                    $this->request('avatar')
                );
		$this->output($result);
	}
	
	/**
	 * 检验
	 */
	public function verifymobile() {
	
	    $result = StaffService::verifymobile(
	        $this->staffId,
	        $this->request('mobile'),
	        $this->request('verifyCode')
	    );
	    $this->output($result);
	}
	
	/**
	 * 更新员工信息
	 */
	public function mobile() {
	
	    $result = StaffService::mobile(
	        $this->userId,
	        $this->request('oldmobile'),
	        $this->request('mobile'),
	        $this->request('verifyCode')
	    );
	    $this->output($result);
	} 

}