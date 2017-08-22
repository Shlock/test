<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Lang, Route, Page, Response;

/**
 * 菜品分类
 */
class GoodsTypeController extends AuthController {
	/**
	 * 菜品分类列表
	 */
	public function index() {
        $list = $this->requestApi('goods.type.lists');
        View::share('list',$list['data']['list']);
		return $this->display();
	}

    /**
     * 添加菜品分类
     */
    public function create(){
        return $this->display('edit');
    }

    /**
     * 添加菜品分类
     */
    public function edit(){
        $id = (int)Input::get('id');
        $data = $this->requestApi('goods.type.get',['id' => $id]);
        View::share('data', $data['data']);
        return $this->display('edit');
    }

    /**
     * 保存菜品分类
     */
    public function save() {
        $args = Input::get();
        if ((int)$args['id'] > 0) {
            $url = u('GoodsType/edit',['id'=>$args['id']]);
            $result = $this->requestApi('goods.type.update', $args);
        } else {
            $url = u('GoodsType/index');
            $result = $this->requestApi('goods.type.create', $args);
        }
        if($result['code'] == 0){
            return $this->success($result['msg'] ? $result['msg'] : Lang::get('admin.code.98008'), $url);
        }else{
            return $this->error($result['msg'] ? $result['msg'] : Lang::get('admin.code.98009'));
        }

    }

    /**
     * 删除菜品分类
     */
    public function destroy(){
        $args = Input::get();
        $data = $this->requestApi('goods.type.delete', ['id' => explode(',', $args['id'])]);
        $url = u('GoodsType/index');
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), $url , $data['data']);
    }


}
