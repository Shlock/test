<?php 
namespace YiZan\Http\Controllers\Api\System\Seller;

use YiZan\Services\SellerComplainService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 举报管理
 */
class ComplainController extends BaseController 
{
    /**
     * 举报列表
     */
    public function lists()
    {
        $data = SellerComplainService::getList (
            intval($this->request('type', \YiZan\Models\Seller::SERVICE_PERSONAL)),
            intval($this->request('disposeAdminId')),
            $this->request('beginTime'),
            $this->request('endTime'),
            intval($this->request('status')),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        
		$this->outputData($data);
    }
    /**
     * 举报处理
     */
    public function dispose()
    {
        $result = SellerComplainService::dispose(intval($this->request('id')), $this->request('content'), $this->adminId);
        
        $this->output($result);
    }
}