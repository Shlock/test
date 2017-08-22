<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\HotWordsService;
use Input;
/**
 * 热搜关键词管理
 */
class HotwordsController extends BaseController 
{
   	/**
     * 列表
	 */
	public function lists()
    {
        $data = HotWordsService::getLists( 
            $this->request('hotwords'), 
            $this->request('city'), 
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20) 
        );
        
		$this->outputData($data);
    }

    /**
     * 添加
     */
    public function save()
    {
        $result = HotWordsService::save(
            intval($this->request('id')), 
            $this->request('hotwords'), 
            intval($this->request('provinceId')), 
            intval($this->request('cityId')), 
            intval($this->request('areaId')), 
            intval($this->request('sort')), 
            intval($this->request('status'))
        );
        
        $this->output($result);
    }

    /**
     * 详情 
     */
    public function get()
    {
        $result = HotWordsService::getById(intval($this->request('id')));
        
        $this->outputData($result);
    } 

    /**
     * 删除 
     */
    public function delete()
    {
        $result = HotWordsService::delete(intval($this->request('id')));
        
        $this->output($result);
    } 

    /**
     * 修改状态
     */
    public function updateStatus()
    {
        $result = HotWordsService::updateStatus(intval($this->request('id')), intval($this->request('val')));
        
        $this->output($result);
    }

}