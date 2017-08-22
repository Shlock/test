<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\StatisticsService; 
use Lang, Validator,Time;

class StatisticsController extends BaseController {
	
	/**
	 * 订单统计
	 */
	public function orderCount(){  
		$data = StatisticsService::orderCount($this->sellerId);
		$this->outputData($data);
	}  
	
	/**
	 * 今日营业统计
	 */
	public function today(){  
		$data = StatisticsService::today($this->sellerId);
		$this->outputData($data);
	} 
	
	/**
	 * 收入统计
	 */
	public function income(){  
		$data = StatisticsService::income($this->sellerId,$this->request('beginDate'),$this->request('endDate'),(int)$this->request('type'));
		$this->outputData($data);
	} 
    /**
     *  营业额
     */
    public function revenue(){
        $data = StatisticsService::revenue($this->sellerId,$this->request('beginDate'),$this->request('endDate'),(int)$this->request('type'));

        $args['endDate'] = $this->request('endDate');
        $args['beginDate'] = $this->request('beginDate');
        if(!empty($args['endDate']) && !empty($args['beginDate'])){
            $endDate = Time::toTime($args['endDate']);
            $beginDate = Time::toTime($args['beginDate']);
            $args['rs'] = ($endDate-$beginDate)/86400;
        }else{
            $type = $this->request('type');
            $args['rs'] = isset($type) ? intval($type) : 1;;
        }
        $page = $this->request('page');
        $args['page'] = isset($page) ? intval($page) : 0;
        if($args['page'] > 0){
            $args['page'] = $args['page'] - 1;
        }
        $data['totalCount'] = $args['rs'];
        $data['list'] = array_slice($data['stat'],$args['page']*20,20);
        $this->outputData($data);
    }

    /**
     *  商品统计
     */
    public function goodsreport(){
        $data = StatisticsService::goodsreport(
            $this->sellerId,
            $this->request('beginDate'),
            $this->request('endDate'),
            (int)$this->request('type'),
            (int)$this->request('numOrder'),
            (int)$this->request('priceOrder'),
            max((int)$this->request('page'), 1),
            max((int)$this->request('pageSize'), 20)
        );
        $this->outputData($data);
    }

}