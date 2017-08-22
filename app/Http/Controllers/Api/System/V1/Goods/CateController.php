<?php 
namespace YiZan\Http\Controllers\Api\System\Goods;

use YiZan\Services\GoodsCateService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 服务分类
 */
class CateController extends BaseController 
{
    /**
     * 分类列表
     */
    public function lists()
    {
        $data = GoodsCateService::getList($this->request('sellerId'), $this->request('type'));
        
		$this->outputData($data);
    }
    /**
     * 添加分类
     */
    public function create()
    {
        $result = GoodsCateService::create
        (
            intval($this->request('pid')), 
            $this->request('type'), 
            $this->request('name'), 
            $this->request('img'), 
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
        $result = GoodsCateService::systemUpdateCate
        (
            intval($this->request('id')),
            $this->request('type'), 
            $this->request('name'), 
            intval($this->request('sort')),
            intval($this->request('status')),
            intval($this->request('sellerId'))
        );

        $this->output($result);
    }
    /**
     * 删除分类
     */
    public function delete()
    {
        $result = GoodsCateService::delete(intval($this->request('id')), intval($this->request('sellerId')));
        
        $this->output($result);
    }

    public function get()
    {
        $result = GoodsCateService::getSellerCate(intval($this->request('sellerId')),intval($this->request('id')));
        
        $this->output($result);
    }
	
	public function updatestatus()
    {
        $result = GoodsCateService::updateStatus(
            intval($this->request('id')),
            intval($this->request('status'))
        ); 
        $this->output($result);
    }
}