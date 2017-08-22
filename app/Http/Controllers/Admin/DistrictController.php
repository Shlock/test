<?php 
namespace YiZan\Http\Controllers\Admin;
 
use View, Input, Lang, Redirect, Response;

/**
 * 小区管理
 */
class DistrictController extends AuthController {

	public function index(){
		$args = Input::all();
		$result = $this->requestApi('district.lists', $args); 
		View::share('list',$result['data']['list']); 		
		return $this->display();
	}

	/**
	 * 添加小区
	 * @return [type] [description]
	 */
	public function create() {
		return $this->display('edit');
	}

	/**
	 * 检索小区
	 */
	public function search(){
		$args = Input::all();
		$args['isTotal'] = 1;
		$result = $this->requestApi('district.lists', $args);
		return Response::json($result);
	}

	/**
	 * 编辑小区
	 */
	public function edit() {
		$args = Input::all();
		if($args['id'] < 0)
			Redirect::to(u('District/index'))->send();
		$result = $this->requestApi('district.get', $args);
		View::share('data',$result['data']);
		return $this->display();
	}

	/**
	 * 保存小区
	 */
	public function save() {
		$args = Input::all(); 
		$result = $this->requestApi('district.save', $args);
		if ( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('District/index'), $result['data']);
	}

	/**
	 * 删除小区
	 */
	public function destroy() {
		$args = Input::all();
		if ( !empty( $args['id'] ) )
			$result = $this->requestApi('district.delete',$args); 

		if( $result['code'] > 0 ) 
			return $this->error($result['msg']);

		return $this->success(Lang::get('admin.code.98005'), u('District/index'), $result['data']);
		
	} 

}
