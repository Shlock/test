<?php 
namespace YiZan\Http\Controllers\Api\System;
use YiZan\Services\CarBrandService;
use Config;

/**
 * 车辆信息
 */
class CarbrandController extends BaseController {

    public function lists()
    {   
        $result = CarBrandService::carlist(
            $this->request('name'),
            max((int)$this->request('page'),1),
            max((int)$this->request('pageSize'),20)
        );

        $this->outputData($result);
    }
    
    public function save()
    {
        $result = CarBrandService::savecar(
            $this->request('id'),
            $this->request('name'),
            $this->request('ename'),
            $this->request('logo'),
            $this->request('pinyin')
        );
        $this->output($result);
    }
    
    public function getid()
    {
        $result = CarBrandService::getById($this->request('id'));    
        $this->outputData($result);
    }
    public function delete()
    {
        $result = CarBrandService::delete($this->request('id'));
        $this->outputData($result);
    }
    
}