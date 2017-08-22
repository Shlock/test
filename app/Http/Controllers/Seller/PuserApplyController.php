<?php namespace YiZan\Http\Controllers\Seller;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Validator, Session, DB, Response, Redirect;
/**
 * 业主审核
 */
class PuserApplyController extends AuthController {

	/**
	 * 业主列表
	 */
	public function index() {
		$args = Input::all();
        $result = $this->requestApi('propertyuser.lists', $args);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
        //print_r($result);
		return $this->display();
	}

	public function edit() {
		$args = Input::all(); 
        $data = $this->requestApi('propertyuser.get', ['puserId'=>$args['id']]);
        if( $result['code'] == 0 ){
            View::share('data', $data['data']);
        }
        $result = $this->requestApi('propertyuser.check', ['puserId'=>$args['id']]);
        //print_r($result);
        View::share('checkmsg', $result['msg']);
		return $this->display();
	}

	public function update() {
		$args = Input::all(); 
		// var_dump($args);
		// exit;
        $result = $this->requestApi('propertyuser.updateStatus', $args);
        return Response::json($result);
        // if ($args['status'] == 1) {
        // 	$url = u('PropertyFee/create');
        // } else {
        // 	$url = u('PuserApply/index');
        // }
  		// if( $result['code'] > 0 ) {
		// 	return $this->error($result['msg']);
		// }
		// return $this->success($result['msg'], $url, $result['data']);
	}


}
