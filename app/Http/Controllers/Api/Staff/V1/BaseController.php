<?php 
namespace YiZan\Http\Controllers\Api\Staff;

use YiZan\Http\Controllers\YiZanApiController;
use YiZan\Services\SellerStaffService;
use YiZan\Services\UserService;
use YiZan\Services\SellerService;
use Input, Lang;

abstract class BaseController extends YiZanApiController {
	protected $user = null;
	protected $userId = 0;
    protected $staff = null;
	protected $staffId = 0;
	protected $seller = null;
	protected $sellerId = 0;
	protected $allowAction = ['app.init','config.token','config.init', 'user.login', 'user.verifylogin', 'user.reg', 'user.logout', 'user.mobileverify','user.repwd'];
    protected $isEncrypterData = false;
    
    /**
	 * 初始化信息
	 * @return boolean
	 */
	protected function initialize() {

		$this->userId = (int)Input::get('userId');

		$this->staff = SellerStaffService::getByUserId($this->userId);
		if ($this->staff) {
			$this->staffId = (int)$this->staff->id;
		}
		$this->seller = SellerService::getById(0, $this->userId);
		if ($this->seller) {
			$this->sellerId = (int)$this->seller->id;
		}
		//print_r($this->seller);
		if ($this->staffId > 0 || $this->sellerId > 0) {
			$this->tokenId = $this->userId;
	        $this->user = UserService::getById($this->userId);
	        //找不到会员信息,则返回false
	        if (!$this->user) {
				return false;
			}
			$this->tokenPwd = $this->user->pwd;

		}
		
		return parent::initialize();

	}

	/**
	 * 创建Token
	 * @param  integer $userId 会员编号
	 * @param  string  $pwd    密码
	 * @return string          生成的TOKEN
	 */
	protected function createToken($userId = 0, $pwd = '') {
		$this->userId = $userId;
		return parent::createToken($userId, $pwd);
	}

	/**
	 * 根据状态码获取提示
	 * @param  int $code 状态码
	 * @return string    提示
	 */
	protected function getApiMsg($code){
		return Lang::get('api_staff.code.'.$code);
	}
}