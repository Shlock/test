<?php 
namespace YiZan\Http\Controllers\Api\Staff;

use Illuminate\Support\Facades\View;
use YiZan\Services\Staff\GoodsService;
use YiZan\Http\Controllers\Api\Staff\BaseController;
use YiZan\Models\Goods;

/**
 * 服务管理
 */
class GoodsController extends BaseController {
    /**
     * 服务列表
     */
    public function lists() {
        $data = GoodsService::getLists(
            $this->sellerId,
            max((int)$this->request('page'), 1), 
            $this->request('id'),
            (int)$this->request('status'),
            $this->request('keywords')
        );
		$this->outputData($data);
    }

    /**
     * 上下架删除操作
     */
    public function op() {
        $data = GoodsService::opGoods(
            $this->sellerId,
            $this->request('ids'),
            (int)$this->request('type')
        );
        $this->output($data);
    }

    /**
     * 添加编辑服务
     */
    public function edit() {
        $data = GoodsService::goodsUpdate(
            $this->sellerId,
            (int)$this->request('id'),
            (int)$this->request('tradeId'),
            $this->request('name'),
            $this->request('imgs'),
            $this->request('norms'),
            $this->request('brief'),
            $this->request('price'),
            $this->request('duration'),
            $this->request('staffs'),
            $this->request('stock')
        );
        $this->output($data);
    }

    /**
     * 编辑详情
     */
    public function detail(){
        $sellerId = $this->sellerId;
        $id = (int)$this->request('id');
        $data = Goods::where('id', $id)->where('seller_id', $sellerId)->first();
        if ($data){
            $data = $data->toArray();
        } else {
            $data = [];
        }
        View::share('data', $data);
        return View::make('api.goods.detail');
    }
}