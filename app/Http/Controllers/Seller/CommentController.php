<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Comment;
use View, Input, Lang, Route, Page, Validator, Session, Response;
/**
 * 评价
 */
class CommentController extends AuthController {
	
	public function index() {
		$post = Input::all(); 
		!empty($post['beginTime'])  	? $args['beginTime'] 	= strtotime($post['beginTime'])  : null;
		!empty($post['endTime']) 		? $args['endTime'] 		= strtotime($post['endTime']) 	 : null;
		!empty($post['orderSn'])   	? $args['orderSn'] 	=  $post['orderSn'] : null;
        !empty($post['result'])   	? $args['result'] 	=  $post['result'] : null;
		!empty($post['page']) 			? $args['page'] 		= intval($post['page']) : $args['page'] = 1;
		$result = $this->requestApi('order.rate.lists', $args);
		if($result['code']==0){
			View::share('list',$result['data']['list']);	
		}
		return $this->display();
	}

	/**
	 * 回复
	 */
	public function reply() {
		$args = Input::all();
		$result = $this->requestApi('order.rate.get', $args); 
		if($result['code']==0){
			View::share('data',$result['data']);			
		}   
		View::share('id',$args);
		return $this->display();
	}
	/**
	 * 提交回复
	 */
	public function ajaxreply() {
		$args = Input::all();		
		$result = $this->requestApi('order.rate.reply', $args);
		return Response::json($result);
	}
	
}
