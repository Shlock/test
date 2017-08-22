<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Http\Controllers\YiZanApiController;
use YiZan\Services\System\AdminUserService;
use YiZan\Services\RegionService;
use Input, Lang, DB;

abstract class BaseController extends YiZanApiController {
	protected $admin = null;
	protected $adminId = 0;
	protected $cityIds = [];
	protected $allowAction = ['config.init', 'config.token'];
	protected $isEncrypterData = false;

	/**
	 * 初始化信息
	 * @return boolean
	 */
	protected function initialize() {
		$this->tokenId = $this->adminId	= (int)Input::get('adminId');
		$this->tokenPwd = '';
		if ($this->adminId > 0) {
			$this->admin = AdminUserService::getById($this->adminId);
			if (!$this->admin) {//找不到管理员信息,则返回false
				return false;
			}
                        
            /*foreach($this->admin->citys as $city) {
                $this->cityIds[] = $city->city_id;
            }*/
            $provinces = RegionService::getProvinces();
            foreach($provinces as $province) {
                $this->cityIds[] = $province['id'];
            }
			$this->tokenPwd = $this->admin->pwd;
		}
        
		return parent::initialize();
	}

	/**
	 * 创建Token
	 * @param  integer $adminId 管理员编号
	 * @param  string  $pwd     密码
	 * @return string           生成的TOKEN
	 */
	protected function createToken($adminId = 0, $pwd = '') {
		$this->adminId = $adminId;
		return parent::createToken($adminId, $pwd);
	}

	protected function getApiMsg($code){
		return Lang::get('api_system.code.'.$code);
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