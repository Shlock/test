<?php 
namespace YiZan\Http\Controllers\Api\Buyer\User;

use YiZan\Http\Controllers\Api\Buyer\UserAuthController;
use YiZan\Services\Buyer\UserMobileService;

class MobileController extends UserAuthController {
	/**
	 * 获取会员的常用地址列表
	 */
	public function lists() {
		$data = UserMobileService::getMobileList($this->userId);
		$this->outputData($data);
	}

	/**
	 * 添加会员常用地址
	 */
	public function create() {
		$result = UserMobileService::createMobile($this->userId, $this->request('mobile'));
		$this->output($result);
	}

	/**
	 * 常用地址设为默认
	 */
	public function setdefault() {
		$result = UserMobileService::setDefaultMobile($this->userId, (int)$this->request('mobileId'));
		$this->output($result);
	}

	/**
	 * 常用地址删除
	 */
	public function delete() {
		$result = UserMobileService::deleteMobile($this->userId, (int)$this->request('mobileId'));
		$this->output($result);
	}

	/**
     * 地址详情获取
     */
    public function get() {
        $data = UserMobileService::getById($this->userId, (int)$this->request('mobileId'));
        $this->outputData($data);
    }
}