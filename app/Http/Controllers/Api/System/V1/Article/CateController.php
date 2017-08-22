<?php 
namespace YiZan\Http\Controllers\Api\System\Article;

use YiZan\Services\ArticleCateService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 文章分类
 */
class CateController extends BaseController 
{
    /**
     * 分类列表
     */
    public function lists()
    {
        $data = ArticleCateService::getList();
        
		$this->outputData($data);
    }
    /**
     * 添加分类
     */
    public function create()
    {
        $result = ArticleCateService::create
        (
            intval($this->request('pid')), 
            $this->request('name'), 
            intval($this->request('sort')),
            intval($this->request('status'))
        );
        
        $this->output($result);
    }
    /**
     * 更新分类
     */
    public function update()
    {
        $result = ArticleCateService::update
        (
            intval($this->request('id')),
            intval($this->request('pid')), 
            $this->request('name'), 
            intval($this->request('sort')),
            intval($this->request('status'))
        );
        
        $this->output($result);
    }
    /**
     * 删除分类
     */
    public function delete()
    {
        $result = ArticleCateService::delete(intval($this->request('id')));
        
        $this->output($result);
    }
}