<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Models\System\Goods;
use YiZan\Services\Buyer\GoodsService;

/**
 * 服务
 */
class ServiceController extends BaseController {

    /**
     * 服务列表
     */
    public function lists() { 
        $data = GoodsService::getSellerServiceLists( 
                $this->request('id'),
                max((int)$this->request('page'),1),
                max((int)$this->request('pageSize'),20)
            );
        $this->outputData($data);
    }

    /**
     * 服务详情
     */
    public function detail() {
        $data = GoodsService::getById((int)$this->request('goodsId'), $this->userId);  
        if ($data && $data['status'] == STATUS_ENABLED) {  
            if(isset($data['collect'])){
                unset($data['collect']);
                $data['iscollect'] = 1;
            } else {
                $data['iscollect'] = 0;
            }
            $data['url'] = u('wap#Goods/appbrief',['goodsId'=>$data['id']]); 
            $this->outputData($data);
        }
        $this->outputCode(40002);
    }
}