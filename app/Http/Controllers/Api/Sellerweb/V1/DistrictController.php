<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\DistrictService;
use Input;
/**
 * 小区
 */
class DistrictController extends BaseController 
{
    /**
     * 添加小区
     */
    public function save(){
        $result = DistrictService::save( 
            $this->request('id'),
            $this->request('name'),
            $this->request('address'),
            (int)$this->request('provinceId'),
            (int)$this->request('cityId'),
            (int)$this->request('areaId'),
            $this->request('mapPoint') ,
            $this->request('departTel') ,
            $this->request('departMail') ,
            $this->request('departStreet') ,
            $this->request('departCommon') 
        );  
        $this->output($result);
    }

    /**
     * [delete 删除小区] 
     */
    public function delete(){
        $result = DistrictService::delete($this->request('id'));
        $this->output($result);
    }

    /**
     * [lists 小区列表] 
     */
    public function lists(){
        $result = DistrictService::getLists( 
            $this->request('name'),
            (int)$this->request('provinceId'),
            (int)$this->request('cityId'),
            (int)$this->request('areaId'),
            (int)$this->request('isUser'),
            (int)$this->request('isPropertyAdd'),
            (int)$this->request('isTotal'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20) 
            ); 
        $this->outputData($result);
    }

    /**
     * [get 获取小区] 
     */
    public function get(){
        $result = DistrictService::get($this->request('id'));
        $this->outputData($result);
    }  
 
}