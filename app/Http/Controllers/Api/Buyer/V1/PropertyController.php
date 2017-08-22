<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\PropertyService;
use YiZan\Services\RepairService;
use Lang, Validator;

class PropertyController extends BaseController 
{
    /*
    * 物业介绍
    */
    public function detail() {
        $result = PropertyService::getProperty((int)$this->request('districtId'));

        $this->outputData($result ? $result->toArray() : []);
    }


    /*
    * 报修列表
    */
    public function repairlists() {
        $result = RepairService::getRepairLists($this->userId, (int)$this->request('districtId'), max((int)$this->request('page'), 1));
        $this->outputData($result);
    }

    public function repairget() {
        $result = RepairService::get((int)$this->request('id'), (int)$this->request('districtId'));
        $this->outputData($result);
    }

    //报修类型
    public function typelists() {
        $result = RepairService::getRepairTypeLists();
        $this->outputData($result);
    }

    public function createrepair()
    {
        $result = RepairService::createRepair(
            $this->userId,
            $this->request('districtId'),
            $this->request('typeId'),
            $this->request('images'),
            $this->request('content')
        );
        
        $this->outputData($result);
    }

    
}

