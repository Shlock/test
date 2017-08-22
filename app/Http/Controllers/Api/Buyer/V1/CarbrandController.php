<?php 
namespace YiZan\Http\Controllers\Api\Buyer;
use YiZan\Services\CarBrandService;
use Config;

/**
 * 车辆信息
 */
class CarbrandController extends BaseController {

    public function lists()
    {   
        $result = CarBrandService::getList(
            max((int)$this->request('page'),1),
            max((int)$this->request('pageSize'),20)
        );

        $this->outputData($result);
    }
}