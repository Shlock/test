<?php 
namespace YiZan\Http\Controllers\Api\System\Seller;

use YiZan\Http\Controllers\Api\System\BaseController;
use YiZan\Services\System\SellerCertificateService;

/**
 * 资质认证
 */
class CertificateController extends BaseController 
{
    /**
     * 认证列表
     */
    public function lists() {
        $data = SellerCertificateService::getLists(
                intval($this->request('type', \YiZan\Models\Seller::SERVICE_PERSONAL)),
                $this->request('mobileName'),
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
        $data = SellerCertificateService::getCertificate((int)$this->request('sellerId'));
        if (!$data) {
            $this->outputCode(30701);
        }
        $this->outputData($data->toArray());
    }

    /**
     * 更新认证
     */
    public function update() {
        $result = SellerCertificateService::updateCertificate(
                $this->adminId,
                (int)$this->request('sellerId'), 
                $this->request('remark'), 
                (int)$this->request('status')
            );
        $this->output($result);
    }
}