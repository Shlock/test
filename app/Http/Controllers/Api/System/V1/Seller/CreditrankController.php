<?php 
namespace YiZan\Http\Controllers\Api\System\Seller;

use YiZan\Services\System\SellerCreditRankService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 信誉等级管理
 */
class CreditRankController extends BaseController 
{
    /**
     * 信誉等级列表
     */
    public function lists()
    {
        $data = SellerCreditRankService::getList();
        
		$this->outputData($data);
    }
    /**
     * 添加信誉等级
     */
    public function create()
    {
        $result = SellerCreditRankService::create
        (
            $this->request('name'),
            $this->request('icon'),
            intval($this->request('minScore')),
            intval($this->request('maxScore'))
        );
        
        $this->output($result);
    }
    /**
     * 获取信誉等级
     */
    public function get()
    {
        $rank = SellerCreditRankService::getById(intval($this->request('id')));
        
        $this->outputData($rank == false ? [] : $rank->toArray());
    }    
    /**
     * 更新信誉等级
     */
    public function update()
    {
        $result = SellerCreditRankService::update
        (
            intval($this->request('id')),
            $this->request('name'),
            $this->request('icon'),
            intval($this->request('minScore')),
            intval($this->request('maxScore'))
        );
        
        $this->output($result);
    }
    /**
     * 删除信誉等级
     */
    public function delete()
    {
        $result = SellerCreditRankService::delete(intval($this->request('id')));
        
        $this->output($result);
    }
}