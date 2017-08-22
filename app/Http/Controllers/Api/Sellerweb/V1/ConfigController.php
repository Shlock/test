<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\System\SystemConfigService;
use Lang, Validator;

/**
 * 系统配置
 */
class ConfigController extends BaseController 
{
    public function token() {
        $this->createToken();
        $result = [
            'code'  => 0,
            'token' => $this->token,
            'data'  => []
        ];
        $this->output($result);
    }

    /**
     * 初始化信息
     */
    public function init() {
        $this->createToken();
        $result = [
            'code'  => 0,
            'token' => $this->token,
            'data'  => [
                'configs'   => SystemConfigService::getConfigs()
            ]
        ];
        $this->output($result);
    }
}