<?php 
namespace YiZan\Http\Controllers\Api\System;
use YiZan\Services\CarseriesService;
use Config;

/**
 * 车系信息
 */
class CarseriesController extends BaseController {

    public function lists()
    {   
        $result = CarseriesService::getlist(
            $this->request('name'),
            max((int)$this->request('page'),1),
            max((int)$this->request('pageSize'),20)
        );

        $this->outputData($result);
    }
    
    public function save()
    {
        $result = CarseriesService::save(
            $this->request('id'),
            $this->request('brandId'),
            $this->request('pinyin'),
            $this->request('name')
        );
        $this->output($result);
    }
    
    public function getseriesid()
    {
        $result = CarseriesService::getBySeriesId($this->request('id'));    
        $this->outputData($result);
    }
    public function delete()
    {
        $result = CarseriesService::delete($this->request('id'));
        $this->outputData($result);
    }
    
}