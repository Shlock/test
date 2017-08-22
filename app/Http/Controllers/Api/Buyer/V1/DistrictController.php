<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\DistrictService;
use Input;
/**
 * 小区
 */
class DistrictController extends BaseController { 

    /**
     * 获取最近小区
     */
    public function getnearestlist(){
        $result = DistrictService::getNearestLists(
            $this->request('location') 
        );
        $this->outputData($result);
    }

    /**
     * 获取开通区/县
     */
    public function getarealist(){
        $result = DistrictService::getOpenAreaLists(
            $this->request('cityid') 
        );
        $this->outputData($result);
    } 

    /**
     * 获取开通小区
     */
    public function getvillageslist(){
        $result = DistrictService::getOpenVillageLists(
            $this->request('areaid') 
        );
        $this->outputData($result);
    }

    /**
     * 获取小区下楼栋列表
     */
    public function getbuildinglist(){
        $result = DistrictService::getBuildingLists(
            $this->request('villagesid')
        );
        $this->outputData($result);
    }

    /**
     * 获取楼栋下面的房间列表
     */
    public function getroomlist(){
        $result = DistrictService::getRoomLists(
            $this->request('buildingid')
        );
        $this->outputData($result);
    }

    /**
     * 搜索小区
     */
    public function searchvillages(){
        $result = DistrictService::getSearchVillages(
            $this->request('keywords')
        );
        $this->outputData($result);
    }

    //获取小区详情
    public function get(){
        $result = DistrictService::get(
            $this->userId,
            $this->request('districtId')
        );
        $this->outputData($result);
    }

    //我的小区列表
    public function lists(){
        $result = DistrictService::getMyLists(
            $this->userId
        );
        $this->outputData($result);
    }

    //s删除我的小区
    public function delete(){
        $result = DistrictService::delete(
            $this->request('districtId')
        );
        $this->output($result);
    }

    //加入我的小区
    public function create(){
        $result = DistrictService::createDistrict(
            $this->userId,
            $this->request('districtId')
        );
        $this->output($result);
    }

    //获取我的物业
    public function getdistrict(){
        $result = DistrictService::getDistrict(
            $this->userId,
            (int)$this->request('districtId')
        );
        $result = $result ? $result->toArray() : null;
        $this->outputData($result);
    }

}