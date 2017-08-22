<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb\Goods;

use YiZan\Services\GoodsCateService;
use YiZan\Http\Controllers\Api\Sellerweb\BaseController;
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
        $data = GoodsCateService::getSellerList($this->sellerId, intval($this->request('type')));
        
		$this->outputData($data);
    }
    /**
     * 系统分类
     */
    public function systemlists(){
        
        $data = GoodsCateService::getSystemList();
        
		$this->outputData($data);
    }

    public function get(){
    	$data = GoodsCateService::getSellerCate($this->sellerId,intval($this->request('id')));

    	$this->outputData($data);
    }

    /**
     * 添加分类
     */
    public function create()
    {
        $result = GoodsCateService::create
        (
            intval($this->request('tradeId')), 
            intval($this->request('pid')), 
            $this->request('type'), 
            $this->request('name'), 
            $this->request('img'), 
            intval($this->request('sort')),
            intval($this->request('status')),
            $this->sellerId
        );
        
        $this->output($result);
    }
    /**
     * 更新分类
     */
    public function update()
    {
        $result = GoodsCateService::update
        (
            intval($this->request('id')),
            intval($this->request('tradeId')), 
            intval($this->request('pid')), 
            $this->request('type'), 
            $this->request('name'), 
            $this->request('img'),
            intval($this->request('sort')),
            intval($this->request('status')),
            $this->sellerId
        );
        
        $this->output($result);
    }
    /**
     * 删除分类
     */
    public function delete()
    {
        $result = GoodsCateService::delete(intval($this->request('id')), $this->sellerId);
        
        $this->output($result);
    }

    public function updateStatus()
    {
        $result = GoodsCateService::updateStatus(
            intval($this->request('id')),
            intval($this->request('val'))
        );
        $this->output($result);
    }
}