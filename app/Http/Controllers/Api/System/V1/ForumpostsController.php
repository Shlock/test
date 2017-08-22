<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\ForumPostsService;
use Lang, Validator;

/**
 * 帖子管理
 */
class ForumpostsController extends BaseController 
{
    /**
     * 帖子列表
     */
    public function lists()
    {
        $data = ForumPostsService::getLists(
            $this->request('username'),
            $this->request('title'),
            $this->request('plateId'),
            $this->request('status'),
            $this->request('beginTime'),
            $this->request('endTime'),
            $this->request('hot'), 
            $this->request('top'),
            $this->request('sort'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		$this->outputData($data);
    } 

    /**
     * 待审核帖子列表
     */
    public function auditlists()
    {
        $data = ForumPostsService::auditLists(
            $this->request('type'), 
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
        $this->outputData($data);
    } 

    /**
     * 保存帖子
     */
    public function save()
    {
        $result = ForumPostsService::save(
            $this->request('id'),  
            $this->request('title'),
            $this->request('content'),
            $this->request('images'),
            $this->request('mobile'),
            $this->request('top'),
            $this->request('hot'), 
            $this->request('status')
        );
        
        $this->output($result);
    }

    /**
     * 获取帖子
     */
    public function get()
    {
        $result = ForumPostsService::get(
            intval($this->request('id')), 
            intval($this->request('type')),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
            );
        
        $this->outputData($result);
    }

    /**
     * 删除帖子
     */
    public function delete()
    {
        $result = ForumPostsService::delete(intval($this->request('id')));
        
        $this->output($result);
    }

    public function updatestatus(){
        $result = ForumPostsService::updateStatus($this->request('id'),intval($this->request('status')), $this->request('field'));
        
        $this->output($result);
    }


    /**
     * 更新信息
     */
    public function update(){ 
        $result = ForumPostsService::update(
            $this->request('id'),
            $this->request('key'),
            $this->request('val')
            );
        $this->output($result);
    } 

}

