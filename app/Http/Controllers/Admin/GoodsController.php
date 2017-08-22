<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\Goods;
use YiZan\Http\Requests\Admin\GoodsCreatePostRequest;
use View, Input, Lang, Route, Page, Validator, Session, Response;
/**
 * 服务
 */
class GoodsController extends AuthController {
	/**
	 * 服务管理-服务列表
	 */
	public function index() {
		$args = Input::all();
		$result = $this->requestApi('goods.lists', $args);
		if ($result['code'] == 0)
			View::share('list', $result['data']['list']);
		return $this->display();
	}



	/**
	 * 服务管理-更新服务详细
	 */
	public function edit() {
		$args = Input::all();
        $data = $this->requestApi('goods.get', $args);
		$type = $this->requestApi('goods.type.all');
        View::share('data', $data['data']);
        View::share('type', $type['data']);
		return $this->display();
	}

	/**
	 * 添加菜品
	 */
	public function create() {
        $type = $this->requestApi('goods.type.all');
        $data = ['restaurantId' => (int)Input::get('restaurantId')];
		View::share('data', $data);
        View::share('type', $type['data']);
		return $this->display('edit');
	}

	/**
	 * 保存菜品
	 */
	public function save() {
		$args = Input::all();
        if ((int)$args['id'] > 0) {
            $url = u('goods/edit',['id' => $args['id']]);
            $result = $this->requestApi('goods.update', $args);
        } else {
            $url = u('goods/index');
            $result = $this->requestApi('goods.create', $args);
        }
        if ($result['code'] == 0) {
            return  $this->success(Lang::get('admin.code.98008'), $url, $result['data']);
        } else {
            return $this->error($result['msg']);
        }

	}

	/**
	 * 服务管理-删除服务
	 */
	public function destroy() {
		$args = Input::all();
		if ( !empty( $args['id'] ) )
			$result = $this->requestApi('goods.delete',$args);

		if( $result['code'] > 0 ) 
			return $this->error($result['msg']);

		return $this->success(Lang::get('admin.code.98005'), u('goods/index'), $result['data']);
		
	}



	/**
	 * 修改状态
	 */
	public function updateStatus() {
		$result = $this->requestApi('system.goods.updateStatus',[
				'id' => Input::input('id'),
				'status' => Input::input('val')
			]);
		$result = array (
            'status'    => true,
            'data'      => Input::input('val'),
            'msg'       => null
        );
		return Response::json($result);
	}

    /**
     * 菜品审核
     */
    public function dispose() {
        $args = Input::get();
        $result = $this->requestApi('goods.dispose', $args);
        return Response::json($result);
    }
}
