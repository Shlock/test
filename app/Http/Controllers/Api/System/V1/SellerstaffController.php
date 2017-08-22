<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\SellerStaffService;
use Lang, Validator;

/**
 * 机构员工管理
 */
class SellerstaffController extends BaseController 
{
    /**
     * 员工列表
     */
    public function lists()
    {
        $data = SellerStaffService::getSystemList
        (
            $this->request('sellerId'),
            $this->request('mobile'),
            $this->request('name'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
        );
        
		$this->outputData($data);
    }

    /**
     * 员工搜索
     */
    public function search() {
        $data = SellerStaffService::searchGoods($this->request('name'), (int)$this->request('sellerId'));
        $this->outputData($data);
    }
     /**
     * 员工推送搜索
     */
    public function searchs() {
        $data = SellerStaffService::searchUser($this->request('name'));
        $this->outputData($data);
    }
    /**
     * 员工
     */
    public function goodsStaff()
    {
        $data = SellerStaffService::getGoodsStaff((int)$this->request('goodsId'));
        
        $this->outputData($data);
    }

    /**
     * 添加更新员工
     */
    public function update()
    {

        $result = SellerStaffService::saveSellerStaff( 
            (int)$this->request('id'),
            $this->request('mobile'),
            $this->request('pwd'),
            $this->request('name'),
            $this->request('avatar'),
            (int)$this->request('sellerId'),
            $this->request('sex'),
            (int)$this->request('type'),
            (int)$this->request('provinceId'),
            $this->request('cityId'),
            $this->request('areaId'),
            $this->request('address'),
            $this->request('authentication'),
            $this->request('authenticateImg'),
            $this->request('mapPos'),
            $this->request('mapPoint'),
            $this->request('status')
        );

        $this->output($result);
    }
    /**
     * 获取员工
     */
    public function get()
    {
        $staff = SellerStaffService::getSystemSellerStaffById((int)$this->request('id'));
        
        $this->outputData($staff == false ? [] : $staff->toArray());
    }

    /**
     * 删除员工
     */
    public function delete()
    {
        $result = SellerStaffService::deleteSystem((int)$this->request('id'));
        
        $this->output($result);
    }

    /**
     * 更新员工状态
     */
    public function updateStatus() {
        $result = SellerStaffService::updateStaffStatus(
            (int)$this->request('id'),
            (int)$this->request('status')
        );
        $this->output($result);
    }

   
}