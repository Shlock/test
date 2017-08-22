<?php namespace YiZan\Http\Controllers\Seller;

use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Validator, Session, DB, Response, Redirect;
/**
 * 报修
 */
class RepairController extends AuthController {

	/**
	 * 报修列表
	 */
	public function index() {
		$args = Input::all(); 
        !empty($args['nav'])            ? $nav                 = $args['nav'] : $nav = 1;
        !empty($args['name'])      ? $args['name']   = strval($args['name'])    : null;
        !empty($args['build'])      ? $args['build']   = strval($args['build'])    : null;
        !empty($args['roomNum'])      ? $args['roomNum']   = strval($args['roomNum'])    : null;
        !empty($args['status'])    ? $args['status'] = intval($args['status'])              : 0;
        !empty($args['page'])     ? $args['page']  = intval($args['page'])       : $args['page'] = 1;  

        $result = $this->requestApi('repair.lists', $args);
        //print_r($result);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
        View::share('searchUrl', u('Repair/index',['status'=>$args['status'], 'nav'=>$args['nav']]));
        View::share('nav', $nav);
        View::share('args',$args);
		return $this->display();
	}


	/*
	* 查看
	*/
	public function edit() {
		$args = Input::all(); 
        $data = $this->requestApi('repair.get', $args);
        if ($data['code'] == 0) {
            View::share('data', $data['data']);
        }
        //print_r($data);
        View::share('args', $args);
		return $this->display();
	}


	/*
	* 保存
	*/
	public function save() {
		$args = Input::all(); 
        $result = $this->requestApi('repair.save', $args);
		return Response::json($result);
    }

    public function updateStatus() {
        $args = Input::all();
        $result = $this->requestApi('article.updateStatus',$args);
        return Response::json($result);
    }

}
