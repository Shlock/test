<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Services\SystemConfigService;
use View, Input, Lang, Route, Page, Response;
use YiZan\Models\Seller;
use YiZan\Models\GoodsCate;
use YiZan\Models\Goods;
use YiZan\Services\Sellerweb\GoodsService;

/**
 * 商家分类
 */
class GoodsCateController extends AuthController {
	/**
	 * 商家分类列表
	 */
	public function index() { 
        $result = $this->requestApi('goods.cate.lists');   
        View::share('list', $result['data']);
		return $this->display();
	}

    /**
     * 添加商家分类
     */
    public function create(){ 

        $seller_cate_result = $this->requestApi('seller.cate.lists');   
       // print_r($seller_cate_result);
        foreach ($seller_cate_result['data'] as $value) {
            $cate[] = $value['cates'];  
        } 
        //print_r($cate);
        View::share('cate', $cate);

        $seller = Session::get('seller');
        //$sellers = Seller::where('status', 1)->where('id', '!=', $seller['id'])->get();
        $sellers = Seller::where('id', '!=', $seller['id'])->get();
        View::share('allSeller',  json_encode($sellers));


        $result = SystemConfigService::getByCode('bat_post_admin_seller');
        $sellerIds = $result['val'];
        $sellerIds = explode(',', $sellerIds);
        View::share('checkShowBatPost', in_array($seller['id'], $sellerIds));
        return $this->display('edit');
    }

    /**
     * 添加商家分类
     */
    public function edit(){
        $args = Input::all(); 
        $seller_cate_result = $this->requestApi('seller.cate.lists'); 
        foreach ($seller_cate_result['data'] as $value) {
            $cate[] = $value['cates'];    
        } 
        View::share('cate', $cate); 
        $result_data = $this->requestApi('goods.cate.get', $args); 
        //print_r($result_data);
        View::share('data', $result_data['data']);


        $seller = Session::get('seller');
        //$sellers = Seller::where('status', 1)->where('id', '!=', $seller['id'])->get();
        $sellers = Seller::where('id', '!=', $seller['id'])->get();
        View::share('allSeller',  json_encode($sellers));


        $result = SystemConfigService::getByCode('bat_post_admin_seller');
        $sellerIds = $result['val'];
        $sellerIds = explode(',', $sellerIds);
        View::share('checkShowBatPost', in_array($seller['id'], $sellerIds));
        return $this->display();

    }

    /**
     * 保存商家分类
     */
    public function save() {
        $args = Input::get();

        // 要先找到原有的name保留
        if ( $args['id'] > 0 ) {
            $good = GoodsCate::find($args['id']);
            $name = $good['name'];

        } else {
            $name = $args['id'];

        }

        // 原有逻辑，针对此登录菜场逻辑
        if ((int)$args['id'] > 0) {
            $url = u('GoodsCate/edit',['id'=>$args['id']]);
            $result = $this->requestApi('goods.cate.update', $args);
        } else {
            $url = u('GoodsCate/index');
            $result = $this->requestApi('goods.cate.create', $args);
        }
        if($result['code'] != 0){
            return $this->error($result['msg'] ? $result['msg'] : Lang::get('admin.code.98009'));
        }

        $currentGoodsList = null;

        if(count($args['bat-post-sellers'])){
            $currentGoodsList = $this->getGoodLists($this->sellerId, $args['id']);
        }


        // 新增逻辑，同步到所有bat-post-sellers
        if(count($args['bat-post-sellers']) && count($currentGoodsList)){
            foreach ($args['bat-post-sellers'] as $sellerId) {
                $result = GoodsCate::where('name', $name)->where('seller_id', $sellerId)->get();
                if ($result->isEmpty()) {
                    $url = u('GoodsCate/index');
                    $data = [];
                    $data['sellerId'] = $sellerId;
                    $result = $this->requestApi2('goods.cate.create', $args, $data);

                    // 如果创建成功了，那么根据名称，应该可以查到新的类别ID
                    $new_cate = GoodsCate::where('name', $name)->where('seller_id', $sellerId)->get();
                    $args['id'] = $new_cate[0]['id'];

                } else {
                    $url = u('GoodsCate/edit',['id'=>$args['id']]);
                    $args['id'] = $result[0]['id'];
                    $data = ['sellerId' => $sellerId];
                    $result = $this->requestApi2('goods.cate.update', $args, $data);
                }
                if($result['code'] != 0){
                    return $this->error($result['msg'] ? $result['msg'] : Lang::get('admin.code.98009'));
                }

                if(!empty($args['id'])){
                    // 开始同步商品相关的数据
                    $this->syncCateGoods($args['id'], $sellerId, $currentGoodsList);
                }

            }

        }
        return $this->success( Lang::get('admin.code.98008'), u('GoodsCate/index'));
    }

    /**
     * 获取当前商家下当前类别的商品数据
     * @param $sellerId 商家ID
     * @param $cateId 类别ID
     * @return $lists 商品列表
     */
    protected function getGoodLists($sellerId, $cateId){
        $lists = Goods::where('seller_id','=', $sellerId)->where('cate_id','=',$cateId)->get();
        return $lists;
    }

    /**
     * 在类别已经创建okay的情况下，同步商品的信息
     */
    protected function syncCateGoods($cateId, $sellerId, $goodsList){

        if(count($goodsList)){
            foreach ($goodsList as $key => $goods) {
                // 看看商品是否已经存在如果，存在那么直接update，否则直接新建一个
                $result = Goods::where('name', $goods->name)->where('seller_id', $sellerId)->where('cate_id', $cateId)->get();

                if($result->isEmpty()){
                    $create_result = GoodsService::systemCreate
                        (
                            $sellerId, 
                            1,
                            1,  
                            0,
                            $goods->name,
                            $goods->price,
                            $cateId, 
                            $goods->brief,
                            $goods->images,
                            $goods->duration,
                            $goods->unit,
                            $goods->stock,
                            $goods->buy_limit,
                            $goods->norms,
                            $goods->status,
                            $goods->sort,
                            $goods->deduct_type,
                            $goods->deduct_val
                        );

                }else{
                    $goodsId = $result[0]['id'];
                    $update_result = GoodsService::systemUpdate
                        (
                            $goodsId,
                            $sellerId, 
                            1,
                            1,  
                            0,
                            $goods->name,
                            $goods->price,
                            $cateId, 
                            $goods->brief,
                            $goods->images,
                            $goods->duration,
                            $goods->unit,
                            $goods->stock,
                            $goods->buy_limit,
                            $goods->norms,
                            $goods->status,
                            $goods->sort,
                            $goods->deduct_type,
                            $goods->deduct_val
                        );
                }

            }
        }
       
    }

    //获取分类
    public function getcate() {
        $result = $this->requestApi('goods.cate.lists'); 
        if($result['code']==0) {
            $this->generateTree(0,$result['data']);
        }
        //生成树形
        $cate = $this->_cates;
        return $cate;
    }

    /**
     * 删除商家分类
     */
    public function destroy(){
        $args = Input::get();
        $data = $this->requestApi('goods.cate.delete', ['id' => $args['id']]); 
        $url = u('GoodsCate/index');
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), $url , $data['data']);
    }

    /**
     * 更改状态
     */
    public function updateStatus() {
        $args = Input::all();
        $result = $this->requestApi('goods.cate.updateStatus',$args);
        return Response::json($result);
    }
}
