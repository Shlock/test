<?php 
namespace YiZan\Http\Controllers\Admin; 

use View, Input, Form,Lang; 

/**
 * 消息推送
 */
class UserAppMessageSendController extends AuthController {
	protected function requestApi($method, $args = [],$data = []){
		!empty($this->type) ? $this->type : $this->type = 'buyer'; 
		$args['type'] = $this->type;   
		$data['adminId'] = $this->adminId;
		return parent::requestApi($method, $args,$data = []); 
	} 
	/**
	 * 推送列表
	*/
	public function index() {
		$result = $this->requestApi('pushmessage.lists',Input::all());
		if( $result['code'] == 0 )
			View::share('list', $result['data']['list']);
		View::share('type', ucfirst($this->type != "buyer" ? $this->type : 'User' )); 
		return $this->display();
	}	
	/**
	 * 添加推送
	*/
	public function create() {
		View::share('type', ucfirst($this->type != "buyer" ? $this->type : 'User' )); 
		return $this->display('edit'); 
	}  
	/**
	 *确定推送UserAppMessageSendPostRequest $request
	*/
	public function send() { 
		$args = Input::all();   	 
		!empty($this->type) ? $url = ucfirst($this->type) : $url = 'User'; 
		if(!empty($args['userType']) && empty($args['users'])){
			 return $this->error(Lang::get('admin.messageSend.70304'));
		}
		$data = $this->requestApi('pushmessage.create',$args);
		if( $data['code'] == 0 ) {
			return $this->success($data['msg']?$data['msg']:$data['msg']=Lang::get('admin.code.98001'),u($url.'AppMessageSend/index'));
		}
		else {
			return $this->error($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98002'),'',$args);
		}	
	}  
	/**
	 * 删除推送
	*/
	public function destroy() {		
		$id = (int)Input::get('id');   
		$args = array();
		!empty($this->type) ? $url = ucfirst($this->type) : $url = 'User';  
		if (empty($id)) {
			return $this->error(Lang::get('admin.noId'),u($url.'AppMessageSend/index'));
		}
		$args['id']  = $id;
		$data = $this->requestApi('pushmessage.delete',$args);
		/*返回处理*/
		if( $data['code'] == 0 ) {
			return $this->success($data['msg']?$data['msg']:$data['msg']=Lang::get('admin.code.98005'),u($url.'AppMessageSend/index'));
		}
		else {
			return $this->error($data['msg'] ? $data['msg'] : $data['msg']=Lang::get('admin.code.98006'),'',$args);
		}	
	} 
}
