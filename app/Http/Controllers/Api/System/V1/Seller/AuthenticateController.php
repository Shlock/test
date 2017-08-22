<?php 
namespace YiZan\Http\Controllers\Api\System\Seller;

use YiZan\Http\Controllers\Api\System\BaseController;
use YiZan\Services\System\SellerAuthenticateService;

/**
 * 身份认证管理
 */
class AuthenticateController extends BaseController 
{
    /**
     * 认证列表
     */
    public function lists() {
        $data = SellerAuthenticateService::getLists(
                intval($this->request('type', \YiZan\Models\Seller::SERVICE_PERSONAL)),
                $this->request('realName'), 
                $this->request('mobile'), 
                $this->request('idcardSn'), 
                $this->request('companyName'), 
                $this->request('businessLicenceSn'), 
                (int)$this->request('status'), 
                max((int)$this->request('page'), 1), 
                max((int)$this->request('pageSize'), 20)
            );

        $this->outputData($data);
    }

    /**
     * 认证详细
     */
    public function get() {
        $data = SellerAuthenticateService::getAuthenticate((int)$this->request('sellerId'));
        if (!$data) {
            $this->outputCode(30501);
        }
        $this->outputData($data->toArray());
    }

    /**
     * 更新认证
     */
    public function update() {
        $result = SellerAuthenticateService::updateAuthenticate(
                $this->adminId,
                (int)$this->request('sellerId'), 
                $this->request('remark'), 
                (int)$this->request('status')
            );
        $this->output($result);
    }
}