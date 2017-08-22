<?php namespace YiZan\Http\Controllers\Admin;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Validator, Session, DB, Response, Redirect;
/**
 * 板块
 */
class ForumPlateController extends AuthController {
	/**
	 * 板块列表
	 */
	public function index() {
		$args = Input::all();
        //板块列表
        $result = $this->requestApi('forumplate.lists', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
		return $this->display();
	}

	/**
	 * [create 添加板块]
	 */
	public function create(){
		return $this->display('edit');
	}
	
	/**
	 * [edit 编辑板块]
	 */
	public function edit(){
		$args = Input::all();
        $result = $this->requestApi('forumplate.get', $args);
        if ($seller['code'] == 0) {
            View::share('data', $result['data']);
        }
		return $this->display();
	}

	/**
	 * [save 添加/编辑板块]
	 */
	public function save(){
		$args = Input::all();
		$result = $this->requestApi('forumplate.save',$args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('ForumPlate/index'), $result['data']);
	}

	/**
	 * [destroy 删除板块]
	 */
	public function destroy(){
		$args['id'] = (int)Input::get('id');
		if( $args['id'] > 0 ) {
			$result = $this->requestApi('forumplate.delete',$args); 
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98005'), u('ForumPlate/index'), $result['data']);
	}

}
