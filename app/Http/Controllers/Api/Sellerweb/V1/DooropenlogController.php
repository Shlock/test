<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\DoorOpenLogService;
use Input;
/**
 * 开门记录
 */
class DooropenlogController extends BaseController { 
    /**
     * [lists 开门记录列表] 
     */
    public function lists(){
        $result = DoorOpenLogService::getLists(
            $this->sellerId,  
            $this->request('doorName'),
            $this->request('userName'),
            $this->request('beginTime'),
            $this->request('endTime'),
            $this->request('build'),
            $this->request('roomNum'),
            (int)$this->request('isTotal'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
            );
        $this->outputData($result);
    } 
}