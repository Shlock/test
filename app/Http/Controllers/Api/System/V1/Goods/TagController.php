<?php 
namespace YiZan\Http\Controllers\Api\System\Goods;

use YiZan\Services\GoodsTagService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 服务标签
 */
class TagController extends BaseController 
{
    /**
     * 标签列表
     */
    public function lists()
    {
        $data = GoodsTagService::getList();
        
		$this->outputData($data);
    }

    /**
     * 获取标签
     */
    public function get()
    {
        $result = GoodsTagService::get( intval($this->request('id')));
        
        $this->outputData($result);
    }

    /**
     * 添加标签
     */
    public function create()
    {
        $result = GoodsTagService::create
        ( 
            $this->request('name'),  
            intval($this->request('sort')),
            intval($this->request('status'))
        );
        
        $this->output($result);
    }
    /**
     * 更新标签
     */
    public function update()
    {
        $result = GoodsTagService::update
        (
            intval($this->request('id')), 
            $this->request('name'),  
            intval($this->request('sort')),
            intval($this->request('status'))
        );
        
        $this->output($result);
    }
    /**
     * 删除标签
     */
    public function delete()
    {
        $result = GoodsTagService::delete(intval($this->request('id')));
        
        $this->output($result);
    }
}