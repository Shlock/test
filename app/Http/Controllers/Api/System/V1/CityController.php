<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\RegionService;
use Input;
/**
 * 开通城市管理
 */
class CityController extends BaseController 
{
   	/**
     * 城市列表
	 */
	public function lists()
    {
        $data = RegionService::getSystemServiceCitys 
        (
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		$this->outputData($data);
    }
    /**
     * 添加开通城市
     */
    public function create()
    {
        $result = RegionService::create(intval($this->request('cityId')), intval($this->request('sort')));
        
        $this->output($result);
    }
    /**
     * 删除开通城市
     */
    public function delete()
    {
        $result = RegionService::delete(intval($this->request('cityId')));
        
        $this->output($result);
    }

    /**
     * 设置默认开通城市
     */
    public function setdefault() {
        $result = RegionService::setDefault(intval($this->request('id')));
        $this->outputCode(0);
    }
}