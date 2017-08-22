<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\GoodsTypeService;
use Lang, Validator;

/**
 * 系统配置
 */
class GoodsTypeController extends BaseController 
{
    /**
     * 菜单分类列表
     * @return [type] [description]
     */
    public function lists() {
        $data = GoodsTypeService::getList(
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
        );
        $this->outputData($data);
    }

    /**
     * 获取单个分类信息
     */
    public function get() {
        $result = GoodsTypeService::get(
            intval($this->request('id'))
        );
        
        $this->output($result);
    }

    /**
     * 添加分类信息
     */
    public function create() {
        $result = GoodsTypeService::create(
            $this->request('name'),
            $this->request('ico'),
            intval($this->request('sort'))
        );
        
        $this->output($result);
    }

    /**
     * 更新分类信息
     */
    public function update() {
        $result = GoodsTypeService::update(
            intval($this->request('id')),
            $this->request('name'),
            $this->request('ico'),
            intval($this->request('sort'))
        );
        
        $this->output($result);
    }

    /**
     * 删除分类
     */
    public function destroy() {
        $result = GoodsTypeService::delete(
            intval($this->request('id'))
        );
        
        $this->output($result);
    }
}