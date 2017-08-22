<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Http\Controllers\YiZanApiController; 
use YiZan\Services\SellerService;
use YiZan\Services\UserService;
use Input, Lang, DB, Config;

abstract class BaseController extends YiZanApiController {
	protected $user = null;
	protected $userId = 0;
    protected $seller = null;
	protected $sellerId = 0;
	protected $allowAction = ['config.init','config.token'];
	protected $isEncrypterData = false;

	/**
	 * 初始化信息
	 * @return boolean
	 */
	protected function initialize() {
	    
		$this->sellerId = (int)Input::get('sellerId');
		if ($this->sellerId > 0) {
            $this->seller = SellerService::getById($this->sellerId);
			//找不到卖家信息,则返回false
			if (!$this->seller) {
				return false;
			}
            $this->tokenId = $this->userId = $this->seller->user_id;
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
		return Lang::get('api_sellerweb.code.'.$code);
	}

	/**
     * 通用更新状态的
     */
    public function updateStatus() {
    	$val 		 = (int)$this->request('val');
    	$id  		 = (int)$this->request('id');
    	$field  	 = trim($this->request('field'));
    	$ref_module  = trim($this->request('ref_module'));
        $result = array (
        	'status'	=> true,
			'code'	    => self::SUCCESS,
			'data'	    => $val,
			'msg'	    => null
		);

		if(empty($ref_module)){
			$table = API_TABLE;
		} else {
			$table = snake_case($ref_module);
		}

		$field = empty($field) ? 'status' : snake_case($field);
		
		DB::table($table)->where('id', $id)->update([$field => $val]);
		$this->output($result);
    }
}