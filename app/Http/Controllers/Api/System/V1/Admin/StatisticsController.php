<?php 
namespace YiZan\Http\Controllers\Api\System\Admin;

use YiZan\Services\System\StatisticsService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 统计类
 */
class StatisticsController extends BaseController 
{ 
    /**
     * 获取卖家统计信息  
     */
    public function getSellerInfo(){ 
        $data = StatisticsService::getSellerInfo();
        $this->outputData($data);
    }

    /**
     * 获取订单统计信息
     */
    public function getOrderInfo(){
        $type = (int)$this->request('type') < 0 ? 0 : (int)$this->request('type');
        $data = StatisticsService::getOrderInfo($type);
        $this->outputData($data);
    }
    /**
     * 业绩排行  
     */
    public function getPerformanceRanking()
    { 
        $data = StatisticsService::performanceRanking((int)$this->request('beginDay'), (int)$this->request('endDay'));
        
        $this->outputData($data);
    }
    /**
     * 提成排行
     */
    public function getBonusRanking()
    {
        $data = StatisticsService::bonusRanking((int)$this->request('beginDay'), (int)$this->request('endDay'));
        
        $this->outputData($data);
    }
    /**
     * 卖家业绩
     */
    public function getSellerAchievement()
    {
        $data = StatisticsService::sellerAchievement($this->request('seller'), (int)$this->request('beginDay'), (int)$this->request('endDay'));
        
        $this->outputData($data);
    }
    /**
     * 卖家员工业绩
     */
    public function getSellerStaffAchievement()
    {
        $data = StatisticsService::sellerStaffAchievement($this->request('seller'), $this->request('staff'), (int)$this->request('beginDay'), (int)$this->request('endDay'));
        
        $this->outputData($data);
    }
}