<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\Config;
use View, Input, Lang;

/**
 * 系统配置
 */
class ConfigController extends AuthController {
	public function index(){
		$args = Input::all();
		$args['groupCode'] = "admin";
		$result = $this->requestApi('system.config.get',$args);
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']);
		}
	        $folder = scandir(realpath(base_path('resources/views/wap')));
	        array_splice($folder,0,2);
	        $tpls = array();
	        foreach ($folder as $k=>$v) {
	            $tpls[$k] = array( 'text' => $v, 'val'=>$v);
	        }
	        View::share('wapTpls', $tpls);
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
		return $this->success(Lang::get('admin.code.98008'), u('Config/index'), $result['data']);
	}
	

}
