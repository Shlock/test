<?php 
namespace YiZan\Http\Controllers\Api\System;


use YiZan\Services\System\PushMessageService;
use Lang, Validator;

/**
 * 推送管理
 */
class PushmessageController extends BaseController 
{
    /**
     * 推送列表
     */
    public function lists()
    {
        $data = PushMessageService::getList
        (
            $this->request('type'),
            intval($this->request('userType')),
            $this->request('sendTime'),
            $this->request('endsendTime'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		$this->outputData($data);
    }
    /**
     * 添加推送
     */
    public function create()
    {       
        $result = PushMessageService::create
        (
            $this->request('type'),
            $this->request('title'),
            $this->request('content'),
            intval($this->request('userType')), 
            $this->request('users'),
            $this->request('args'),
            $this->request('sendType')
        );
        
        $this->output($result);
    }
    /**
     * 删除推送
     */
    public function delete()
    {
        $result = PushMessageService::delete(intval($this->request('id')));
        
        $this->output($result);
    }
}