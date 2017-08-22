<?php 
namespace YiZan\Http\Controllers\Api\System\Goods;

use YiZan\Services\GoodsTypeService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 服务分类
 */
class TypeController extends BaseController
{
    /**
     * 分类列表
     */
    public function lists()
    {
        $data = GoodsTypeService::getList(
            max($this->request('page'),1),
            max($this->request('pageSize'),20)
        );
        
		$this->outputData($data);
    }
    /**
     * 添加分类
     */
    public function create()
    {
        $result = GoodsTypeService::create
        (
            $this->request('name'), 
            $this->request('ico'),
            intval($this->request('sort'))
        );
        
        $this->output($result);
    }
    /**
     * 更新分类
     */
    public function update()
    {
        $result = GoodsTypeService::update
        (
            (int)$this->request('id'),
            $this->request('name'),
            $this->request('ico'),
            intval($this->request('sort'))
        );
        
        $this->output($result);
    }
    /**
     * 删除分类
     */
    public function delete()
    {
        $result = GoodsTypeService::delete((array)$this->request('id'));
        
        $this->output($result);
    }

    /**
     * 获取分类
     */

    public function get() {
        $data = GoodsTypeService::get((int)$this->request('id'));
        $this->outputData($data);
    }

    /**
     * 无分页分类
     */
    public function all() {
        $list = GoodsTypeService::getAll();
        $this->outputData($list);
    }
}