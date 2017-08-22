<?php 
namespace YiZan\Http\Controllers\Admin; 

use View, Input, Form ,Validator , Lang ,Response; 
/**
 *
 */
class KeyWordsController extends AuthController {    

	public function index(){
		$args = Input::all();
		$args['groupCode'] = "word";
		$result = $this->requestApi('system.config.get',$args);
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']);
		} 
		return $this->display();
	}


	public function save(){
		$data = Input::all();
		$i = 0; 
		foreach ($data as $key => $value) {
			$args[$i]['code'] = trim($key);
			$args[$i]['val'] = trim($value);
			$i++;
		}
		$args['configs'] = $args;
		$result = $this->requestApi('system.config.update',$args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('KeyWords/index'), $result['data']);
	}
}