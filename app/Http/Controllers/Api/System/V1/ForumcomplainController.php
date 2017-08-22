<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\ForumComplainService;
use Lang, Validator;

/**
 * 帖子举报
 */
class ForumcomplainController extends BaseController 
{
    /**
     * 列表
     */
    public function lists()
    { 
        $data = ForumComplainService::getLists(
            $this->request('keywords'),
            (int)$this->request('status'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		$this->outputData($data);
    }  
    /**
     * 删除帖子
     */
    public function delete()
    {
        $result = ForumComplainService::delete( 
            intval($this->request('id')) 
            );
        
        $this->output($result);
    }

    /**
     * 处理帖子举报
     */
    public function dispose()
    { 
        $result = ForumComplainService::dispose(
            $this->adminId,
            intval($this->request('id')),
            $this->request('remark'),
            intval($this->request('status'))
            );
        
        $this->output($result);
    }

}

