<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Http\Controllers\YiZanApiController;
use YiZan\Services\Buyer\UserService;
use YiZan\Services\RegionService;
use Input, Lang, Exception;

abstract class BaseController extends YiZanApiController {
	protected $user 	= null;
	protected $cityId	= 0;
	protected $city 	= null;
	protected $userId	= 0;
	protected $allowAction = ['app.init', 'config.token', 'config.init','seller.mappos','user.login', 'user.verifylogin', 'user.reg', 'user.logout', 'user.mobileverify', 'user.repwd'];
	protected $isEncrypterData = true;

	/**
	 * 初始化信息
	 * @return boolean
	 */
	protected function initialize() {
		$this->tokenId = $this->userId	= (int)Input::get('userId');
		$this->cityId  = (int)Input::get('cityId');

		$this->tokenPwd = '';
		if ($this->userId > 0) {
			$this->user = UserService::getById($this->userId);
			if (!$this->user) {//找不到会员信息,则返回false
				throw new Exception("会员不存在", 99996);
				return false;
			}
			$this->tokenPwd = $this->user->pwd;
		}

		//获取城市信息
		if ($this->cityId > 0) {
			$this->city = RegionService::getById($this->cityId);
		}
        
        $this->isEncrypterData = Input::get('NotEncrypterData') !== "true";
        
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
		return Lang::get('api.code.'.$code);
	}
}