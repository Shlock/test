<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\PropertyService;
use Lang, Validator;

/**
 * 物业
 */
class PropertyController extends BaseController 
{
    /**
     * 物业列表
     */
    public function lists()
    {
        $data = PropertyService::getLists(
            $this->request('name'),
            $this->request('districtName'),
            (int)$this->request('provinceId'),
            (int)$this->request('cityId'),
            (int)$this->request('areaId'),
            (int)$this->request('status'),
            (int)$this->request('isTotal'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		$this->outputData($data);
    } 

    /**
     * 总物业列表
     */
    public function totallists(){

        $data = PropertyService::getTotalLists();
        
        $this->outputData($data);
    }

    /**
     * 保存物业
     */
    public function save()
    {
        $result = PropertyService::save(
            $this->request('id'),
            $this->request('companyName'), 
            $this->request('mobile'),
            $this->request('pwd'),
            $this->request('contact'), 
            $this->request('districtId'),
            $this->request('idcardSn'),
            $this->request('idcardPositiveImg'),
            $this->request('idcardNegativeImg'),
            $this->request('businessLicenceImg')
        );
        
        $this->output($result);
    }

    /**
     * 获取物业
     */
    public function get()
    {
        $result = PropertyService::get(intval($this->request('id')));
        
        $this->outputData($result);
    }

    /**
     * 删除物业
     */
    public function delete()
    {
        $result = PropertyService::delete(intval($this->request('id')));
        
        $this->output($result);
    }


    public function articlelists(){

        $data = PropertyService::getArticleLists(
                (int)$this->request('sellerId'),
                $this->request('title'), 
                $this->request('beginTime'),
                $this->request('endTime'),
                max((int)$this->request('page'), 1), 
                max((int)$this->request('pageSize'), 20)
            );
        
        $this->outputData($data);
    }

    public function articleget()
    {
        $result = PropertyService::getArticle(intval($this->request('id')));
        
        $this->outputData($result);
    }

    public function articlesave()
    {
        $result = PropertyService::articleSave(
            (int)$this->request('id'),
            (int)$this->request('sellerId'), 
            $this->request('title'),
            $this->request('content')
        );
        
        $this->output($result);
    }

    public function articledelete()
    {
        $result = PropertyService::deleteArticle(intval($this->request('id')));
        
        $this->output($result);
    }
}

