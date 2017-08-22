<?php 
namespace YiZan\Http\Controllers\Api\Buyer;
use YiZan\Services\CarseriesService;
use Config;

/**
 * 车系信息
 */
class CarseriesController extends BaseController {

    public function lists()
    {   
        $result = CarseriesService::getList(
            max((int)$this->request('page'),1),
            max((int)$this->request('pageSize'),20)
        );

        $this->outputData($result);
    }
    public function getid()
    {
        $result = CarseriesService::getById((int)$this->request('brandId'));    
        $this->outputData($result);
    }
}