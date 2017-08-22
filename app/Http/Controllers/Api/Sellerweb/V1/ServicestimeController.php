<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;
use YiZan\Services\Sellerweb\SellerServiceTimeService;
use YiZan\Utils\Time;

class ServicestimeController extends BaseController {
    /**
     *  商家服务的时间设置
     */
    public function add() {
        $result = SellerServiceTimeService::insert(
            $this->sellerId,
            (int)$this->request('goodsId'),
            $this->request('weeks'),
            $this->request('hours')
        );
        $this->output($result);
    }

    /**
     * 商家服务的时间列表
     */
    public function lists() {
        $list = SellerServiceTimeService::getList($this->sellerId, (int)$this->request('goodsId'));
        $this->outputData($list);
    }

    /**
     * 商家服务的时间更新
     */
    public function update() {
        $result = SellerServiceTimeService::update(
            $this->sellerId,
            (int)$this->request('goodsId'),
            $this->request('id'),
            $this->request('weeks'),
            $this->request('hours')
        );
        $this->output($result);
    }

    /**
     * 商家服务的时间详情
     */
    public function edit() {
        $data = SellerServiceTimeService::detail(
           (int)$this->request('sellerId'),
           $this->request('goodsId')
        );
        $this->outputData($data);
    }

    /**
     * 员工服务时间删除
     */
    public function delete() {
        $result = SellerServiceTimeService::delete(
            $this->sellerId,
            (int)$this->request('goodsId'),
            $this->request('id')
        );
        $this->output($result);
    }

}