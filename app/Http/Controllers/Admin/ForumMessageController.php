<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Lang, Route, Page,Response;

/**
 * 文章管理
 */
class ForumMessageController extends AuthController {
	/**
	 * 文章列表
	 */
	public function index() {
		$result = $this->requestApi('forummessage.lists');
        View::share('list', $result['data']['list']);
		return $this->display();
	}

	/**
	 * 文章分类-删除分类
	 */
	public function destroy() {
		$args = Input::all();
		if( !empty( $args['id'] ) ) {
			$result = $this->requestApi('forummessage.delete',$args);
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98005'));
	}

	
}
