<?php 
namespace YiZan\Http\Controllers\Seller;

use View, Input, Lang, Route, Page, Response;

/**
 * 房间
 */
class PropertyRoomController extends AuthController {
	/**
	 * 房间列表
	 */
	public function index() {
        $args = Input::all();
        $list = $this->requestApi('propertyroom.lists', $args); 
        //print_r($list);
        if( $list['code'] == 0 ){
            View::share('list', $list['data']['list']);
        }
        $build = $this->requestApi('propertybuilding.get', ['id'=>$args['buildId']]); 
        //print_r($data);
        View::share('build', $build['data']);
        return $this->display();
    }

    /**
     * 添加房间
     */
    public function create(){
        $args = Input::all();
        $build = $this->requestApi('propertybuilding.get', ['id'=>$args['buildId']]); 
        //print_r($data);
        View::share('build', $build['data']);
        return $this->display('edit');
    }

    /**
     * 编辑房间
     */
    public function edit(){
        $args = Input::all();
        $data = $this->requestApi('propertyroom.get', $args); 
        View::share('data', $data['data']);
        $build = $this->requestApi('propertybuilding.get', ['id'=>$args['buildId']]); 
        //print_r($data);
        View::share('build', $build['data']);
        return $this->display();
    }

    /**
     * 保存房间
     */
    public function save() {
        $args = Input::all();
        $data = $this->requestApi('propertyroom.save', $args);

        if ($args['id'] > 0) {
           $url = u('PropertyRoom/index',['buildId'=>$args['buildId']]);
        } else {
            $url = u('PropertyRoom/create',['buildId'=>$args['buildId']]);
        }
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98009'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98008'), $url , $data['data']);

    }


    /**
     * 删除房间
     */
    public function destroy(){
        $args = Input::all();
        $data = $this->requestApi('propertyroom.delete', ['id' => $args['id']]);
        $url = u('PropertyRoom/index');
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), $url , $data['data']);
    }


}
