<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb\Seller;

use YiZan\Services\Sellerweb\SellerService;
use YiZan\Http\Controllers\Api\Sellerweb\BaseController;
use Lang, Validator;

/**
 * 商家营业统计
 */
class StatisticsController extends BaseController{ 

    /**
     * 获取年份
     */
    public function year(){
        $data = SellerService::getyear();
        $this->outputData($data);
    } 

    /**
     * 商家营业月统计列表
     */
    public function monthlists()
    {
        $data = SellerService::getBusinessListByMonth(
            $this->sellerId,
            intval($this->request('month')),
            intval($this->request('year')),
            intval($this->request('cityId')) 
        );
        
        $this->outputData($data);
    }

    /**
     * 商家营业天统计列表
     */
    public function daylists()
    {
        $data = SellerService::getBusinessListByDay(
            $this->sellerId,
            $this->request('day'), 
            $this->request('sn'), 
            intval($this->request('status')), 
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
        $this->outputData($data);
    }
}

