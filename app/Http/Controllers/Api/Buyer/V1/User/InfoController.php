<?php 
namespace YiZan\Http\Controllers\Api\Buyer\User;

use YiZan\Http\Controllers\Api\Buyer\UserAuthController;
use YiZan\Services\Buyer\UserService;

class InfoController extends UserAuthController {
	/**
	 * 更新会员信息
	 */
	public function update() {
		$result = UserService::updateInfo($this->user, [
				'name'   => $this->request('name'), 
				'avatar' => $this->request('avatar')
			]);
		$this->output($result);
	}

    /**
     * 检验
     */
    public function verifymobile() {

        $result = UserService::verifymobile(
            $this->request('mobile'),
            $this->request('verifyCode')
        );
        $this->output($result);
    }
}