<?php namespace YiZan\Http\Controllers\Admin;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Validator, Session, DB, Response, Redirect;
/**
 * 帖子举报
 */
class ForumComplainController extends AuthController {
	
	/**
	 * 帖子举报列表
	 */
	public function index() {
		$args = Input::all(); 
        $result = $this->requestApi('forumcomplain.lists', $args); 
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
		return $this->display();
	}
 
	/**
	 * [destroy 删除帖子举报]
	 */
	public function destroy(){
		$args['id'] = (int)Input::get('id');
		if( $args['id'] > 0 ) {
			$result = $this->requestApi('forumcomplain.delete',$args); 
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98005'), u('ForumComplain/index'), $result['data']);
	}

	/**
	 * 处理帖子举报
	 */
	public function dispose(){
		$args =  Input::all();
		if( $args['id'] > 0 ) {
			$result = $this->requestApi('forumcomplain.dispose',$args); 
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('ForumComplain/index'), $result['data']);
	}
	
}
