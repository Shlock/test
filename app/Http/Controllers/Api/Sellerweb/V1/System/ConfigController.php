<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb\System;

use YiZan\Services\System\SystemConfigService;
use YiZan\Http\Controllers\Api\Sellerweb\BaseController;
use Input;
/**
 * 系统设置
 */
class ConfigController extends BaseController 
{
	/**
     * 获取配置
	 */
	public function get()
    {
        $data = SystemConfigService::getLists($this->request('groupCode'));
        $this->outputData($data);
    }
}