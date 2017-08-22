<?php namespace YiZan\Http\Controllers\Admin;

use YiZan\Utils\Time;
use YiZan\Utils\Common;
use View, Input, Lang, Route, Page, Validator, Session, DB, Response, Redirect;
/**
 * 板块
 */
class PostsCheckController extends AuthController { 

	/**
	 * 待审核列表
	 */
	public function index() { 
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
 
}
