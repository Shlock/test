<?php 
namespace YiZan\Http\Controllers\Seller;

use View, Input, Lang, Response;
/**
 * 菜单分类管理
 */
class GoodsTypeController extends AuthController {
	/**
	 * 菜单分类列表
	 */
	public function index() {
		$result = $this->requestApi('goodstype.lists');
		if($result['code']==0){
			View::share('list',$result['data']['list']);		 	
		}  
		return $this->display();
	}

	/**
	 * 添加分类
	 */
	public function add() {
		return $this->display('edit');
	}

	/**
	 * 编辑分类
	 */
	public function edit() {
		$args = Input::all();
		$result = $this->requestApi('goodstype.get',$args);
		View::share('data',$result);
		return $this->display();
	}

	/**
	 * 更新分类
	 */
	public function save() {
		$args = Input::all();
		if($args['id'] < 1){
			$result = $this->requestApi('goodstype.create',$args);
		}
		else{
			$result = $this->requestApi('goodstype.update',$args);
		}
		return Response::json($result);
	}

	/**
	 * 删除分类
	 */
	public function destroy() {
		$args = Input::all();
		$result = $this->requestApi('goodstype.destroy',$args);
		return Response::json($result);
	}
}