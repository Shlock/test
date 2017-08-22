<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\PropertyUserService;
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
            $this->sellerId,
            $this->request('name'),
            $this->request('build'),
            $this->request('roomNum'),
            $this->request('mobile'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20),
            (int)$this->request('status')
        );
        
		$this->outputData($data);
    } 

    /**
     * 门禁列表
     */
    public function accesscardlists(){
        $data = PropertyUserService::getTotalLists(
            intval($this->request('puserId')),
            $this->sellerId,
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
            $this->sellerId,
            $this->request('endTime')
        );
        
        $this->output($result, false);
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
        $result = PropertyUserService::getdoorsLists($this->sellerId);
        
        $this->outputData($result);
    }

    public function updateStatus()
    {
        $result = PropertyUserService::updateStatus(intval($this->request('id')), (int)$this->request('status'), $this->request('content'));
        
        $this->output($result);
    }

    public function get()
    {
        $result = PropertyUserService::getPuser(intval($this->request('puserId')));
        $this->outputData($result);
    }

    public function check()
    {
        $result = PropertyUserService::checkPuser(intval($this->request('puserId')));
        $this->output($result);
    }

}

