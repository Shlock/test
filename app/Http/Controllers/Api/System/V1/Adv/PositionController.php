<?php 
namespace YiZan\Http\Controllers\Api\System\Adv;

use YiZan\Services\System\AdvPositionService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 广告位管理
 */
class PositionController extends BaseController 
{
    /**
     * 广告位列表
     */
    public function lists()
    {        
        $data = AdvPositionService::getList($this->request('clientType'));
        
		$this->outputData($data);
    }
    /**
     * 添加广告位
     */
    public function create()
    {
        $result = AdvPositionService::create
        (
            $this->request('code'),
            (int)$this->request('isAutoCode'),
            $this->request('name'),
            $this->request('clientType'),
            intval($this->request('width')),
            intval($this->request('height')),
            $this->request('brief'),
            $this->request('style')
        );
        
        $this->output($result);
    }
    /**
     * 获取广告位
     */
    public function get()
    {
        $position = AdvPositionService::getById(intval($this->request('id')));
        
        $this->outputData($position == false ? [] : $position->toArray());
    }    
    /**
     * 更新广告位
     */
    public function update()
    {
        $result = AdvPositionService::update
        (
            intval($this->request('id')),
            $this->request('name'),
            $this->request('clientType'),
            intval($this->request('width')),
            intval($this->request('height')),
            $this->request('brief'),
            $this->request('style')
        );
        
        $this->output($result);
    }
    /**
     * 删除广告位
     */
    public function delete()
    {
        $result = AdvPositionService::delete(intval($this->request('id')));
        
        $this->output($result);
    }
}