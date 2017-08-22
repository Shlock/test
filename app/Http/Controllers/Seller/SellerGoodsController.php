<?php
namespace YiZan\Http\Controllers\Seller;

use YiZan\Services\System\SellerService;
use YiZan\Models\Goods;
use YiZan\Models\Seller;
use YiZan\Models\GoodsCate;
use View, Input, Lang, Route, Page, Validator, Response;
use YiZan\Services\SystemConfigService;

/**
 * 商品
 */
class SellerGoodsController extends AuthController {
	/**
	 * 服务管理-商品列表
	 */
	public function index() {
		$args = Input::all();
		$args['type'] = $option['type'] = Goods::SELLER_GOODS;
		$result = $this->requestApi('goods.lists', $args);
		if( $result['code'] == 0 ){
			View::share('list', $result['data']['list']);
		}
        $result_cate = $this->requestApi('goods.cate.lists',$option);
        View::share('cate', $result_cate['data']);
		View::share('excel',http_build_query($args));
		return $this->display();
	}

	/**
	 * 服务审核
	 */
	public function audit() {
		$post = Input::all();

		$args['status'] = [\YiZan\Models\Base::STATUS_NOT_BY, \YiZan\Models\Base::STATUS_AUDITING];


        switch(Input::get("status"))
        {
            case "1":
                $args['status'] = [\YiZan\Models\Base::STATUS_NOT_BY];
                break;

            case "2":
                $args['status'] = [\YiZan\Models\Base::STATUS_AUDITING];
                break;

            default:
                $args['status'] = [\YiZan\Models\Base::STATUS_NOT_BY, \YiZan\Models\Base::STATUS_AUDITING];
                break;
        }

		!empty($post['name']) 	?  $args['name']  	= strval($post['name']) 	: null;
		!empty($post['page']) 	?  $args['page'] 	= intval($post['page']) 	: $args['page'] = 1;
		$result = $this->requestApi('goods.lists', $args);
		if($result['code']==0)
			View::share("list",$result['data']['list']);
		$_cate = $this->getcate();
		$cate = [];
		foreach ($_cate as $key => $value) {
			$cate[$value['id']] = $value;
		}
		View::share('cate', $cate);
		return $this->display();
	}

	/**
	 * 服务审核进度
	 */
	public function auditplan() {
		$args = Input::all();
		if( isset($args['id']) ) {
			View::share('data',$result['data']);
		}
		return $this->display();
	}

	/**
	 * 添加服务(取消)
	 */
	public function addgoods() {
		return $this->display();
	}

	/**
	 * 服务种类须知
	 */
	public function quickchoose(){
		$type = (int)Input::get('type');
		$args['name'] = Input::get('name');
		$args['page'] = (int)Input::get('page') > 1 ? (int)Input::get('page') : 1;
		$result = $this->requestApi('system.goods.lists',$args);
		if( $result['code'] == 0 ){
			View::share('list', $result['data']['list']);
		}
		if($type==0){
			$args['type'] = 1;
		} else {
			$args['type'] = 0;
		}
		View::share('args',$args);
		if($type > 0){
			return $this->display('textchoose');
		}
		return $this->display();
	}

	/**
	 * 添加新服务
	 */
	public function create() {
        $result_cate = $this->requestApi('goods.cate.lists',['type'=>Goods::SELLER_GOODS]);
        if(!count($result_cate['data']))
		{
			$result_cate['data'][0] = ['id'=>0, 'name'=>'请选择'];
		}
        View::share('cate', $result_cate['data']);

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
	 * 编辑服务
	 */
	public function edit() {
        $result_cate = $this->requestApi('goods.cate.lists',['type'=>Goods::SELLER_GOODS]);
        View::share('cate', $result_cate['data']);

		$args['goodsId'] = Input::get('id');
		$result = $this->requestApi('goods.get',$args);
 		View::share('data', $result['data']);

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

	public function save() {
		$args = Input::all();

		$norms['ids'] =  $args['_id'];
		$norms['names'] =  $args['_name'];
		$norms['prices'] = $args['_price'];
		$norms['weights'] = $args['_weight'];
		$norms['stocks'] = $args['_stock'];
		$args['norms'] = json_encode($norms);
		$args['type'] = Goods::SELLER_GOODS;

		// 要先找到原有的name保留
		if ( $args['id'] > 0 ) {
			$good = Goods::find($args['id']);
			$name = $good['name'];
			$cateId = $good['cate_id'];
			$goodsCate = GoodsCate::find($cateId);
			$cateIds = GoodsCate::where('name', $goodsCate['name'])->lists('id');


		} else {
			$name = $args['name'];
			$cateId = $args['cateId'];
		}
		$newCateId = $args['cateId'];
		$goodsCate = GoodsCate::find($newCateId);
		$newCateIds = GoodsCate::where('name', $goodsCate['name'])->lists('id');

		// 原有逻辑
		if( $args['id'] > 0 ){
			$result = $this->requestApi('goods.update',$args);
			$msg = Lang::get('seller.code.98003');
		}
		else{
			$result = $this->requestApi('goods.create',$args);
			$msg = Lang::get('seller.code.98001');
		}

		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}

		// 新增同步到其他菜市场的逻辑
		foreach ($args['bat-post-sellers'] as $sellerId) {
			// 要先判断那个商家有没有那个新发过来的商品分类，没有的话直接忽略了
			$result = GoodsCate::where('seller_id', $sellerId)->whereIn('id', $newCateIds)->get();
//			dump($sellerId);
//			dump($newCateIds);
			if ($result->isEmpty()) {
				// 没有
				break;
			}
//			dump('有那个分类');
			$newCateId = $result[0]['id'];
//			dump($newCateId);
			// 再检查那个原有分类名字下面有没有name一样的商品

			$result = Goods::where('name', $name)->where('seller_id', $sellerId)->whereIn('cate_id', $cateIds)->get();
			if ($result->isEmpty()) {
//               没有的话就新建一个商品
//				dump('新建');
				$data = ['sellerId' => $sellerId];
				$newArgs = array_merge($args, ['cateId' => $newCateId]);
				$result = $this->requestApi2('goods.create', $newArgs, $data);
//				dump($result);
//                dd($result);
			} else {
//               有的话就修改那个商品
//				dump('修改');
				$newArgs = array_merge($args, ['id' => $result[0]['id'], 'cateId' => $newCateId]);
				$data = ['sellerId' => $sellerId];
				$result = $this->requestApi2('goods.update', $newArgs, $data);
//				dump($result);
//                dump($args);
//                dump($result);

			}
			if($result['code'] != 0){
				return $this->error($result['msg'] ? $result['msg'] : Lang::get('admin.code.98009'));
			}

		}


		return $this->success($msg, u('SellerGoods/index'));
	}

	public function destroy() {
		$args['id'] = Input::get('id');
		$result = $this->requestApi('goods.delete',$args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('seller.code.98005'), u('SellerGoods/index'), $result['data']);
	}

	public function updateStatus() {
		$args = Input::all();
		$result = $this->requestApi('goods.updateStatus',$args);
		return Response::json($result);
	}
}
