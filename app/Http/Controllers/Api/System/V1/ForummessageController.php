<?php 
namespace YiZan\Http\Controllers\Api\System;


use YiZan\Services\ForumMessageService;
use Lang, Validator;

/**
 * 站内消息
 */
class ForummessageController extends BaseController 
{
    /**
     * 消息列表
     */
    public function lists()
    {
        $data = ForumMessageService::getSystemList
        (
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		$this->outputData($data);
    }
    
    /**
     * 删除消息
     */
    public function delete()
    {
        $result = ForumMessageService::deleteSystem($this->request('id'));
        
        $this->output($result);
    }
}