<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\PropertyRoomService;
use Lang, Validator;

/**
 * 房间管理
 */
class PropertyroomController extends BaseController 
{
    /**
     * 房间列表
     */
    public function lists()
    {
        $data = PropertyRoomService::getSystemList(
            $this->sellerId,
            $this->request('roomNum'),
            (int)$this->request('buildId'),
            $this->request('owner'),
            $this->request('mobile'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		$this->outputData($data);
    }

    /**
     * 添加房间
     */
    public function save()
    {
        $result = PropertyRoomService::save(
            $this->sellerId,
            (int)$this->request('id'),
            (int)$this->request('buildId'),
            $this->request('roomNum'),
            $this->request('owner'),
            $this->request('mobile'),
            $this->request('propertyFee'),
            $this->request('structureArea'),
            $this->request('roomArea'),
            $this->request('intakeTime'),
            $this->request('remark')
        );
        
        $this->output($result);
    }
    /**
     * 获取房间
     */
    public function get()
    {
        $data = PropertyRoomService::getSystemById(intval($this->request('id')));
        
        $this->outputData($data == false ? [] : $data->toArray());
    }

    /**
     * 删除房间
     */
    public function delete()
    {
        $result = PropertyRoomService::deleteSystem(intval($this->request('id')));
        
        $this->output($result);
    }

    public function import()
    {   
        $result = PropertyRoomService::import(
            $this->sellerId,
            $this->request('build'),
            $this->request('roomNum'),
            $this->request('owner'),
            $this->request('mobile'),
            $this->request('propertyFee'),
            $this->request('structureArea'),
            $this->request('roomArea'),
            $this->request('intakeTime'),
            $this->request('remark')
        );
        
        $this->output($result);
    }

}

