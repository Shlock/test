<?php 
namespace YiZan\Http\Controllers\Admin; 

use View, Input, Form,Lang; 

/**
*广告位
*/
class UserAppAdvPositionController extends AuthController {  

	protected function requestApi($method, $args = [],$data = []){
		!empty($this->clietnType) ? $this->clietnType : $this->clietnType = 'buyer'; 
		$args['clientType'] = $this->clietnType;  
		return parent::requestApi($method, $args,$data = []); 
	} 
	/**
	 * 广告位 列表
	*/
	public function index() {
		$args = Input::all();   
		$result = $this->requestApi('adv.position.lists',$args);  
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']);
		}
		return $this->display();
	}
	/**
	 * 添加广告位
	*/
	public function create() {
		return $this->display('edit'); 
	} 
	/**
	 * 获取广告位
	*/
	public function edit() {
		$args = Input::all();  			 
		$result = $this->requestApi('adv.position.get',$args); 
		View::share('data', $result['data']); 
		return $this->display();
	}
	/**
	 * 更新广告位
	*/
	public function update() { 
		!empty($this->clietnType) ? $url = ucfirst($this->clietnType) : $url = 'User'; 
		$args = Input::all();  
		!empty($args['id']) ?   $args['id']  = intval($args['id'])  :  $args['id'] = 'null';  
		if($args['id'] > 0 ){
			$data = $this->requestApi('adv.position.update',$args);
			if( $data['code'] == 0 ) {
				return $this->success(
					$data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98003'),
					u($url.'AppAdvPosition/edit',[ 'id'=>$args['id'] ])
				);
			}
			else {
				return $this->error($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98004'),'',$args);
			}	
		}else{  
			$args['createTime'] = time();
			$data = $this->requestApi('adv.position.create',$args);
			if( $data['code'] == 0 ) {
				return $this->success(
					$data['msg'] ? $data['msg'] : $data['msg'] = Lang::get('admin.code.98001'), 
					u($url.'AppAdvPosition/create')
				);
			}
			else {
				return $this->error($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98002'),'',$args);
			}	
		}
	} 
	/**
	 * 删除广告位
	*/
	public function destroy() {		 
		$id = (int)Input::get('id');   
		$args = array();
		!empty($this->clietnType) ? $url = ucfirst($this->clietnType) : $url = 'User'; 
		!empty($id) ? $args['id']  = $id   : $this->error(Lang::get('admin.noId'),u($url.'AppAdv/index'));
		$data = $this->requestApi('adv.position.delete',$args);
		if( $data['code'] == 0 ) {
			return $this->success($data['msg']?$data['msg']:$data['msg']=Lang::get('admin.code.98005'),u($url.'AppAdvPosition/edit'),$data['data']);
		}
		else {
			return $this->error($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98006'),'',$args);
		}	
	} 
}
