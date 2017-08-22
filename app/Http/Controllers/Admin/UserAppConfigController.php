<?php 
namespace YiZan\Http\Controllers\Admin;

use Input,View,From,Lang;
/**
* 手机App会员配置
**/
class UserAppConfigController extends AuthController {
	protected function requestApi($method, $args =[] ,$data = []){
		!empty($this->groupCode) ? $this->groupCode : $this->groupCode = 'buyer'; 
		$args['groupCode'] = $this->groupCode;  
		return parent::requestApi($method, $args,$data = []); 
	} 
	/**
	 * 编辑配置
	 */
	public function index() { 
		$result = $this->requestApi('system.config.get',Input::all());
		if( $result['code'] == 0 ) {
			View::share('data', $result['data']);
		} 
		return $this->display();
	}
	/**
	 * 编辑配置
	 *UserAppConfigPostRequest $request
	 */
	public function edit() {		
		$post = Input::all();
		$i = 0;
		foreach ($post as $key => $value) {
			$args['configs'][$i]['code'] = $key;
			$args['configs'][$i]['val'] = $value;
			$i++;
		} 
		$data = $this->requestApi('system.config.update',$args);   
		/*返回处理*/
		if( $data['code'] == 0 ) {
			!empty($this->type) ? $url = ucfirst($this->type) : $url = 'User'; 
			return $this->success($data['msg']?$data['msg']:$data['msg']=Lang::get('admin.code.98003'), u($url.'AppConfig/index'));
		}
		else {
			return $this->error($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98004'),'',$args);
		}	
	}
}
