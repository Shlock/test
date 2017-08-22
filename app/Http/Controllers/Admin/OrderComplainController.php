<?php 
namespace YiZan\Http\Controllers\Admin; 

use Input,View,Route,Response;

/**
 * 订单举报
 */
class OrderComplainController  extends AuthController { 
	/**
	 * 订单举报-举报列表
	 */
	public function index() {
		$post = Input::all(); 
		!empty($post['sn']) ? $args['sn'] = strval($post['sn']) : null;
		!empty($post['beginTime']) ? $args['beginTime'] = strval($post['beginTime']) : null;
		!empty($post['endTime']) ? $args['endTime'] = strval($post['endTime']) : null;
		!empty($post['status']) ? $args['status'] = (int)$post['status'] : $args['status'] = '0';
		!empty($post['page']) ? $args['page'] = intval($post['page']) : $args['page'] = 1;
		$result = $this->requestApi('order.complain.lists',$args);

		if( $result['code'] == 0 ) {
			foreach ($result['data']['list'] as $key => $images) {
				if(!empty($images['images'])){
					$arr =explode(",",$images['images']) ; 
					$result['data']['list'][$key]['images'] = $arr;
				}
			}  
			View::share('list', $result['data']['list']);
		}   
		!empty($post['nav']) ? $nav = $post['nav'] : $nav = 0;
		View::share('nav', $nav);
		return $this->display();
	}

	/**
	 * 订单举报-举报处理 
	 */
	public function dispose() {
		$args = Input::all();		
		$data = $this->requestApi('order.complain.dispose',$args); 
		return Response::json($data);
	}

}