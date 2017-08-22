<?php

namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\SellerService;
use Lang,
    Validator;

/**
 * 服务人员管理
 */
class SellerController extends BaseController {

    /**
     * 服务人员列表
     */
    public function propertylists() {
        $data = SellerService::getPropertysList(
                        $this->request('name'), $this->request('districtName'), intval($this->request('provinceId')), intval($this->request('cityId')), intval($this->request('areaId')), intval($this->request('isCheck')), $this->request('isTotal'), max((int) $this->request('page'), 1), max((int) $this->request('pageSize'), 20)
        );

        $this->outputData($data);
    }

    /**
     * 添加服务人员
     */
    public function createproperty() {
        $authenticate = $this->request('authenticate');
        $result = SellerService::createProperty(
                        (int) $this->request('id'), $this->request('name'), $this->request('mobile'), $this->request('pwd'), $this->request('contacts'), intval($this->request('districtId')), $this->request('serviceTel'), $authenticate['idcardSn'], $authenticate['idcardPositiveImg'], $authenticate['idcardNegativeImg'], $authenticate['businessLicenceImg']
        );

        $this->output($result);
    }

    /**
     * 更新服务人员
     */
    public function updateproperty() {
        $authenticate = $this->request('authenticate');
        $result = SellerService::updateProperty(
                        $this->request('id'), $this->request('name'), $this->request('mobile'), $this->request('pwd'), $this->request('contacts'), intval($this->request('districtId')), $this->request('serviceTel'), $authenticate['idcardSn'], $authenticate['idcardPositiveImg'], $authenticate['idcardNegativeImg'], $authenticate['businessLicenceImg']
        );

        $this->output($result);
    }

    /**
     * 服务人员列表
     */
    public function lists() {
        $data = SellerService::getSystemList(
                        $this->request('name'), $this->request('mobile'), intval($this->request('provinceId')), intval($this->request('cityId')), intval($this->request('areaId')), intval($this->request('status')), (int) $this->request('cateId'), max((int) $this->request('page'), 1), max((int) $this->request('pageSize'), 20)
        );

        $this->outputData($data);
    }

    /**
     * 服务人员搜索
     */
    public function search() {
        $data = SellerService::searchSeller($this->request('mobileName'));
        $this->outputData($data);
    }

    /**
     * 添加服务人员
     */
    public function create() {
        $result = SellerService::saveSeller(
                        0, $this->request('mobile'), trim($this->request('pwd')), $this->request('name'), (int) $this->request('type'), $this->request('contacts'), $this->request('address'), $this->request('logo'), $this->request('image'), intval($this->request('provinceId')), intval($this->request('cityId')), intval($this->request('areaId')), $this->request('brief'), $this->request('mapPos'), $this->request('mapPoint'), $this->request('idcardSn'), $this->request('businessLicenceImg'), $this->request('idcardPositiveImg'), $this->request('idcardNegativeImg'), $this->request('cateIds'), (float) $this->request('serviceFee'), (float) $this->request('deliveryFee'), $this->request('isAuthenticate'), $this->request('certificateImg'), $this->request('serviceTel'), $this->request('deliveryTime'), (int) $this->request('deduct'), (int) $this->request('isCashOnDelivery')
        );

        $this->output($result);
    }

    /**
     * 获取服务人员
     */
    public function get() {
        $seller = SellerService::getSystemSellerById(intval($this->request('id')));

        $this->outputData($seller == false ? [] : $seller->toArray());
    }

    /**
     * 更新服务人员
     */
    public function update() {
        $result = SellerService::saveSeller(
                        (int) $this->request('id'), $this->request('mobile'), trim($this->request('pwd')), $this->request('name'), (int) $this->request('type'), $this->request('contacts'), $this->request('address'), $this->request('logo'), $this->request('image'), intval($this->request('provinceId')), intval($this->request('cityId')), intval($this->request('areaId')), $this->request('brief'), $this->request('mapPos'), $this->request('mapPoint'), $this->request('idcardSn'), $this->request('businessLicenceImg'), $this->request('idcardPositiveImg'), $this->request('idcardNegativeImg'), $this->request('cateIds'), (float) $this->request('serviceFee'), (float) $this->request('deliveryFee'), $this->request('isAuthenticate'), $this->request('certificateImg'), $this->request('serviceTel'), $this->request('deliveryTime'), $this->request('deduct'), (int) $this->request('isCashOnDelivery')
        );

        $this->output($result);
    }

    /**
     * 删除服务人员
     */
    public function delete() {
        $result = SellerService::deleteSystem(intval($this->request('id')));

        $this->output($result);
    }

    /**
     * 所有服务站
     */
    public function allseller() {
        $result = SellerService::all();
        $this->output($result);
    }

    /**
     * 审核列表
     */
    public function authlists() {
        $data = SellerService::getSystemSellerList(
                        $this->request('name'), intval($this->request('isCheck')), max((int) $this->request('page'), 1), max((int) $this->request('pageSize'), 20)
        );

        $this->outputData($data);
    }

    /**
     * 审核修改
     */
    public function updatecheck() {

        $data = SellerService::updateCheckStatus(
                        intval($this->request('id')), intval($this->request('isCheck')), $this->request('checkVal'), (int) $this->request('deduct')
        );

        $this->output($data);
    }

    public function updatestatus() {
        $result = SellerService::updateStatus(intval($this->request('id')), (int) $this->request('status'), $this->request('field'));

        $this->output($result);
    }

    public function bankupdate() {
        $result = SellerService::updateBankInfo((int) $this->request('id'), $this->request('bank'), $this->request('bankNo'), $this->request('mobile'), $this->request('name'), $this->request('verifyCode'));
        $this->output($result);
    }

    public function bankdelete() {
        $result = SellerService::deleteBankInfo((int) $this->request('id'), $this->request('sellerId'));
        $this->output($result);
    }

    //审核物业
    public function updatepropertystatus() {
        $result = SellerService::updateProStatus(intval($this->request('id')), (int) $this->request('isCheck'), $this->request('checkVal'));

        $this->output($result);
    }

}
