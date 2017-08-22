<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\GoodsService;
use YiZan\Services\GoodsCateService;

class GoodsController extends BaseController {
	/**
	 * 服务列表
	 */
	public function lists() {
		$data = GoodsService::getSellerGoodsLists( 
				$this->request('id')
			);
		$this->outputData($data);
	}

	public function setbrowse() {
		$code = GoodsService::setBrowse((int)$this->request('goodsId'), $this->userId);
		$this->outputCode($code);
	}

	/**
	 * 服务详细
	 */
	public function detail() {
        $data = GoodsService::getById((int)$this->request('goodsId'), $this->userId);   
        if ($data && $data['status'] == STATUS_ENABLED) {  
            if(isset($data['collect'])){
                unset($data['collect']);
                $data['iscollect'] = 1;
            } else {
                unset($data['collect']);
                $data['iscollect'] = 0;
            }
            $data['url'] = u('wap#Goods/appbrief',['goodsId'=>$data['id']]); 
            $this->outputData($data);
        }
		$this->outputCode(40002);
	}

    /*
     * 服务分类
     */
    public function goodCateList() {
        $this->outputData(GoodsCateService::wapGetList());
    }
    /*
     * 服务二级分类
     */
    public function goodCateList2()
    {
        $this->outputData(GoodsCateService::wapGetList2((int)$this->request('cateId')));
    }
    /**
	 * 可预约时间
	 */
	public function appointday() {
		$data = GoodsService::getCanAppointHours(
				(int)$this->request('goodsId'),
				(int)$this->request('duration'),
				(int)$this->request('staffId')
			);
		if (!$data) {
			$this->outputCode(40001);
		}
		$this->outputData($data);
	}
}