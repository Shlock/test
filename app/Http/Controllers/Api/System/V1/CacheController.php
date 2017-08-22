<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\ConfigService;
use Input;
/**
 * 缓存清理
 */
class CacheController extends BaseController 
{
    /**
     * 缓存清理
     */
    public function clear()
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);
        
        $this->output($result);
    }
}