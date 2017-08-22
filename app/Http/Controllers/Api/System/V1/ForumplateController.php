<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\ForumPlateService;
use Lang, Validator;

/**
 * 板块管理
 */
class ForumplateController extends BaseController 
{
    /**
     * 板块列表
     */
    public function lists()
    {
        $data = ForumPlateService::getLists(
            $this->request('name'),
            (int)$this->request('isTotal'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		$this->outputData($data);
    } 


    /**
     * 保存板块
     */
    public function save()
    {
        $result = ForumPlateService::save(
            $this->request('id'),
            $this->request('name'),
            $this->request('icon'),
            (int)$this->request('sort'),
            (int)$this->request('status')
        );
        
        $this->output($result);
    }

    /**
     * 获取板块
     */
    public function get()
    {
        $result = ForumPlateService::get(intval($this->request('id')));
        
        $this->outputData($result);
    }

    /**
     * 删除板块
     */
    public function delete()
    {
        $result = ForumPlateService::delete(intval($this->request('id')));
        
        $this->output($result);
    }

}

