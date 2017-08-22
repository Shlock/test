<?php 
namespace YiZan\Http\Controllers\Api\Buyer\User;

use YiZan\Http\Controllers\Api\Buyer\UserAuthController;
use YiZan\Services\Buyer\UserAddressService;

class AddressController extends UserAuthController {
	/**
	 * 获取会员的常用地址列表
	 */
	public function lists() {
		$data = UserAddressService::getAddressList(
				(int)$this->userId,
				(int)$this->request('page')
		);
		$this->outputData($data);
	}

	/**
	 * 添加会员常用地址
	 */
	public function create() {
		$result = UserAddressService::createAddress(
    			$this->userId, 
    			$this->request('id'), 
    			$this->request('detailAddress'), 
    			$this->request('mapPoint'),
    			intval($this->request('provinceId')),
    			intval($this->request('cityId')),
    			intval($this->request('areaId')),
    			$this->request('name'), 
    			$this->request('mobile'),
    			$this->request('doorplate')
			);
		$this->output($result);
	}

	/**
	 * 常用地址设为默认
	 */
	public function setdefault() {
		$result = UserAddressService::setDefaultAddress($this->userId, (int)$this->request('id'));
		$this->output($result);
	}

	/**
	 * 常用地址删除
	 */
	public function delete() {
		$result = UserAddressService::deleteAddress($this->userId, (int)$this->request('id'));
		$this->output($result);
	}

    /**
     * 地址详情获取
     */
    public function get() {
        $data = UserAddressService::getById($this->userId, (int)$this->request('id'));
        $this->outputData($data);
    }
    
    /**
     * 获取默认地址
     */
    public function getdefault() {
        $data = UserAddressService::getByDefault($this->userId);
        $this->outputData($data);
    }
}