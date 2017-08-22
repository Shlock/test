<?php 
namespace YiZan\Http\Controllers\Admin;
 
use View, Input, Lang, Route, Page,Response;

/**
 * 热搜关键词
 */
class HotListsController extends AuthController {
	
	/**
	 * 热搜关键词列表
	 */
	public function index() {
		$args = Input::all(); 
		$result = $this->requestApi('hotwords.lists', $args);
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']);
		} 
		return $this->display();
	}

	/**
	 * 热搜关键词添加-编辑详细
	 */
	public function edit() {
		$args = Input::all();
		$result = $this->requestApi('hotwords.get',$args);
		View::share('data', $result['data']);
		return $this->display();
	}

	/**
	 * 热搜关键词添加-编辑处理
	 */
	public function create() { 
		return $this->display('edit');
	} 

	public function save() {
		$args = Input::all();
		$result = $this->requestApi('hotwords.save',$args); //更新
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('HotLists/index'), $result['data']);
	}

	/**
	 *  删除热搜关键词
	 */
	public function destroy() {
		$args = Input::all();

		if( !empty( $args['id'] ) ) {
			$result = $this->requestApi('hotwords.delete',$args); 
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98005') , u('HotLists/index'), $result['data']);
	}

	public function updateStatus() {
		$args = Input::all();
		$result = $this->requestApi('hotwords.updateStatus',$args);
		if($result['code'] > 0){
			return $this->error($result['msg']);
		}
		return $this->success($result['msg']);
	}
}
