<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\PropertyBuildingService;
use Lang, Validator;

/**
 * 楼宇管理
 */
class PropertybuildingController extends BaseController 
{
    /**
     * 楼宇列表
     */
    public function lists()
    {
        $data = PropertyBuildingService::getSystemList(
            $this->request('name'),
            (int)$this->request('sellerId'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		$this->outputData($data);
    }

    /**
     * 添加楼宇
     */
    public function save()
    {
        $result = PropertyBuildingService::save(
            (int)$this->request('id'),
            $this->request('name'),
            (int)$this->request('sellerId'),
            $this->request('remark')
        );
        
        $this->output($result);
    }
    /**
     * 获取楼宇
     */
    public function get()
    {
        $data = PropertyBuildingService::getSystemById(intval($this->request('id')));
        
        $this->outputData($data == false ? [] : $data->toArray());
    }

    /**
     * 删除楼宇
     */
    public function delete()
    {
        $result = PropertyBuildingService::deleteSystem(intval($this->request('id')));
        
        $this->output($result);
    }

}

