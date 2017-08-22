<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\PropertyFeeService;
use Lang, Validator;

/**
 * 物业费
 */
class PropertyfeeController extends BaseController 
{
    /**
     * 物业费列表
     */
    public function lists()
    {
        $data = PropertyFeeService::getLists(
            $this->sellerId,
            $this->request('name'),
            $this->request('build'),
            $this->request('roomNum'),
            $this->request('beginTime'),
            $this->request('endTime'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20),
            (int)$this->request('status')
        );
        
		$this->outputData($data);
    } 


    /**
     * 保存物业费
     */
    public function save()
    {
        $result = PropertyFeeService::save(
            $this->sellerId,
            (int)$this->request('id'),
            $this->request('buildId'),
            $this->request('roomId'),
            $this->request('name'),
            $this->request('payableFee'),
            $this->request('payableTime')
        );
        
        $this->output($result);
    }


    /**
     * 删除物业费
     */
    public function delete()
    {
        $result = PropertyFeeService::delete(intval($this->request('id')));
        
        $this->output($result);
    }


    public function get()
    {
        $result = PropertyFeeService::getById(intval($this->request('id')));
        $this->outputData($result);
    }

    public function paysave()
    {
        $result = PropertyFeeService::savePayFee(
            $this->sellerId,
            (int)$this->request('id'),
            $this->request('payFee'),
            $this->request('payTime')
        );
        
        $this->output($result);
    }

    public function import()
    {   
        $result = PropertyFeeService::import(
            $this->sellerId,
            $this->request('build'),
            $this->request('roomNum'),
            $this->request('name'),
            $this->request('payableFee'),
            $this->request('payableTime'),
            $this->request('payFee'),
            $this->request('payTime')
        );
        
        $this->output($result);
    }

}

