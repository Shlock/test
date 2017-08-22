<?php 
namespace YiZan\Http\Controllers\Api\Buyer;
use YiZan\Services\VehicleService;
use Config;

/**
 * 车辆信息
 */
class VehicleController extends BaseController {
    /**
     * 车辆信息列表
     */
    public function lists()
    {
        $data = VehicleService::getList($this->userId, max((int)$this->request('page'), 1));

        $this->outputData($data);
    }
    /* 获取单条消息
     */
    public function getdata()
    {
        $result = VehicleService::getdatas($this->userId,intval($this->request('id')));
        $this->outputData($result);
    }
    /* 添加车辆
     */
    public function add()
    {
        $result = VehicleService::insert($this->userId,$this->request('plateNumber'),$this->request('appellation'),$this->request('image'),$this->request('brandId'),$this->request('carColor'),$this->request('seriesId'));
        $this->output($result);
    }
    /* 编辑车辆信息
     */
    public function update()
    {
        $result = VehicleService::update($this->userId,$this->request('id'),$this->request('plateNumber'),$this->request('appellation'),$this->request('image'),$this->request('brandId'),$this->request('carColor'),$this->request('seriesId'));
        $this->output($result);
    }
    /**
     * 删除车辆信息
     */
    public function delete()
    {
        $result = VehicleService::delete($this->userId, $this->request('id'));

        $this->outputData($result);
    }
    /**
     * 设置默认车辆信息
     */
    public function set()
    {
        $result = VehicleService::setstatus($this->userId, $this->request('id'));
        $this->outputData($result);
    }
    /**
     * 设置默认车辆信息
     */
    public function get()
    {
        $result = VehicleService::getstatus($this->userId);
        $this->outputData($result);
    }
}