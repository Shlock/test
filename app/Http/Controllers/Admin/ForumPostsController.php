<?php namespace YiZan\Http\Controllers\Admin;

use YiZan\Utils\Time;
use YiZan\Utils\Common;
use View, Input, Lang, Route, Page, Validator, Session, DB, Response, Redirect;
/**
 * 板块
 */
class ForumPostsController extends AuthController {
	/**
	 * 帖子列表
	 */
	public function index() {
		$args = Input::all();
        $plateLists = $this->requestApi('forumplate.lists', ['isTotal'=>1]);
        View::share('plates', $plateLists['data']['list']);
        $result = $this->requestApi('forumposts.lists', $args); 
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
		return $this->display();
	} 

	/**
	 * 待审核列表
	 */
	public function check() { 
		$args = Input::all();
		$args['type'] = $args['type'] ? $args['type'] : 1;
        $result = $this->requestApi('forumposts.auditlists', $args); 
        $posts_config = $this->requestApi('config.get', ['code'=>'posts_check']);  
        View::share('posts_config', $posts_config['data']['val']);
        View::share('type', $args['type']); 
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
		return $this->display();
	} 

	/**
	 * 发帖审核
	 */
	public function postsConfig(){
		$args = Input::all(); 
		$data = $this->requestApi('config.updateconfig', $args);
		print_r($data);
	}

	public function detail(){
		$args = Input::all();
		$args['type'] = 1;
        $result = $this->requestApi('forumposts.get', $args);  
        // print_r($result['data']);//exit;
        if ($seller['code'] == 0) {
            View::share('data', $result['data']);
        }
		return $this->display();
	}

	/**
	 * [edit 编辑板块]
	 */
	public function edit(){
		$args = Input::all();
		$args['type'] = 0;
        $result = $this->requestApi('forumposts.get', $args);
        $result['data']['images'] = explode(',', $result['data']['images']);
        if ($seller['code'] == 0) {
            View::share('data', $result['data']);
        }
		return $this->display();
	}

	/**
	 * [save 添加/编辑帖子]
	 */
	public function save(){
		$args = Input::all();
		$result = $this->requestApi('forumposts.save',$args);
		// print_r($result);
		if ($result['code'] == 0) {
			$data = $result['data'];
			if ($data['pid'] == 0) {
				$url = u('ForumPosts/detail',['id'=>$data['id']]);
			} else {
				$url = u('ForumPosts/detail',['id'=>$data['pid']]);
			}
		} else {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), $url, $args);
	}

	/**
	 * [destroy 删除帖子]
	 */
	public function destroy(){
		$args['id'] = (int)Input::get('id');
		if( $args['id'] > 0 ) {
			$result = $this->requestApi('forumposts.delete',$args); 
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98005'));
	} 

	public function updateStatus(){
		$args = Input::all();
		if ($args['field'] == 'status') {
			$args['status'] = $args['val'];
		}
		$result = $this->requestApi('forumposts.updatestatus',$args); 
		if($result['code'] == 0){
			return $this->success($result['msg']);
		}
		return $this->error($result['msg']);
	}

	/**
	 * [update 更新信息]
	 */
	public function update(){
		$args = Input::all();
        $result = $this->requestApi('forumposts.update', $args); 
        return Response::json($result);
	} 

}
