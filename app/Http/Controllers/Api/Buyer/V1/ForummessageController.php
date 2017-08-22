<?php 
namespace YiZan\Http\Controllers\Api\Buyer;


use YiZan\Services\Buyer\ForumMessageService;
use Lang, Validator;

/**
 * 站内消息
 */
class ForummessageController extends UserAuthController
{
    /**
     * 消息列表
     */
    public function lists()
    {
        $data = ForumMessageService::getList
        (
			$this->userId,
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		$this->outputData($data);
    }

    /** 
     * 消息数量
     */
    public function messagenum(){
        $data = ForumMessageService::getMessageNum(
            $this->userId
        );
        $this->outputData($data);

    }
    
    /**
     * 删除消息
     */
    public function delete()
    {
        $result = ForumMessageService::delete($this->userId, $this->request('id'));
        
        $this->output($result);
    }

    /**
     * 阅读消息
     */
    public function read(){
        $result = ForumMessageService::read($this->userId, $this->request('id'));

        $this->output($result);
    }
}