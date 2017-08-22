<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\PropertyUserService;
use Lang, Validator;

/**
 * 业主
 */
class PropertyuserController extends BaseController 
{
    /**
     * 业主列表
     */
    public function lists()
    {
        $data = PropertyUserService::getLists(
            (int)$this->request('sellerId'),
            $this->request('name'),
            $this->request('build'),
            $this->request('roomNum'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		$this->outputData($data);
    } 

    /**
     * 门禁列表
     */
    public function accesscardlists(){
        
        $data = PropertyUserService::getTotalLists(
            intval($this->request('puserId')),
            intval($this->request('sellerId')),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
            );
        
        $this->outputData($data);
    }

    /**
     * 保存业主
     */
    public function save()
    {
        $result = PropertyUserService::save(
            (int)$this->request('id'),
            $this->request('puserId'),
            $this->request('doorId'),
            $this->request('sellerId'),
            $this->request('endTime')
        );
        
        $this->output($result);
    }

    /**
     * 获取门禁
     */
    public function getaccesscard()
    {
        $result = PropertyUserService::get(intval($this->request('id')));
        
        $this->outputData($result);
    }

    /**
     * 删除业主
     */
    public function delete()
    {
        $result = PropertyUserService::delete(intval($this->request('id')));
        
        $this->output($result);
    }

    public function deletedoor()
    {
        $result = PropertyUserService::deleteDoor(intval($this->request('id')));
        
        $this->output($result);
    }

    public function doorslists()
    {
        $result = PropertyUserService::getdoorsLists(
            intval($this->request('sellerId'))
            );
        
        $this->outputData($result);
    }
}

