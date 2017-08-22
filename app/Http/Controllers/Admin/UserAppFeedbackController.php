<?php 
namespace YiZan\Http\Controllers\Admin; 

use View, Input, Form, Response; 

/**
 * 买家APP意见反馈
 */
class UserAppFeedbackController extends AuthController {

 	protected function requestApi($method, $args =[],$data = []){
		!empty($this->type) ? $this->type : $this->type = 'buyer'; 
		$args['type'] = $this->type;  		
		$data['adminId'] = $this->adminId;
		return parent::requestApi($method, $args,$data = []); 
	} 
	/**
	 * 意见反馈
	*/
	public function index() { 
		$post = Input::all(); 
		$args = array();   
		!empty($post['page'])    ? $args['page']  = intval($post['page'])  : $args['page'] = 1; 
		$result = $this->requestApi('feedback.lists',$args);

		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']);
		}
		View::share('type', ucfirst($this->type != "buyer" ? $this->type : 'User' )); 
		return $this->display();
	}	
	/**
	 * 反馈处理 UserAppFeedbackPostRequest $request
	*/
	public function edit() {
		!empty($this->type) ? $url = ucfirst($this->type) : $url = 'User'; 
		$args = Input::all();
		$data = $this->requestApi('feedback.dispose',$args);
		if( $data['code'] == 0 ) {
			return $this->success($data['msg'], u($url.'AppFeedback/index'), $data['data']);
		}
		else {
			return $this->error($data['msg'],u($url.'AppFeedback/index'));
		}
	}
	/**
	 * 删除反馈
	*/
	public function destroy() {
		$id = (int)Input::get('id');   
		$args = array();
		!empty($this->type) ? $url = ucfirst($this->type) : $url = 'User'; 
		if (empty($id)) {
			return $this->error(Lang::get('admin.noId'), u($url.'AppFeedback.index'));
		}
		$args['id']  = $id;
		$data = $this->requestApi('feedback.delete',$args);
		if( $data['code'] > 0 ) {
			return $this->error($data['msg'], u($url.'AppFeedback/index'));
		}
		return $this->success($data['msg'], u($url.'AppFeedback/index'), $data['data']);
	}
	/**
	* 修改状态
	*/
	public function updateStatus() {
		$args = Input::all();
		$result = $this->requestApi('feedback.updateStatus',$args);
		return Response::json($result);
	}
}
