<?php 
namespace YiZan\Http\Controllers\Api\Staff;

use YiZan\Services\Staff\StatisticsService; 
use Lang, Validator;

class StatisticsController extends BaseController {
	
	/**
	 * 统计明细
	 */
	public function detail(){  
		$data = StatisticsService::getStatisticsDetail(
            $this->staffId,
            $this->request('month'),
            $this->request('page')
        );
		$this->outputData($data);
	}
	
	
	/**
	 * 按月份来统计
	 */
	public function month(){  
		$data = StatisticsService::getStatisticsByMonth($this->staffId,$this->request('page'));
		$this->outputData($data);
	}




}