<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Seller;
use View, Input, Lang, Route, Page, Validator, Response;
/**
 * 系统设置
 */
class SystemConfigController extends AuthController {

	public function index() {   	  
		$data = $this->requestApi('user.get');    
		//print_r($data['data']); 
		if($data['code'] == 0){
			$data['data']['businessLicenceImg'] = $data['data']['authenticate']['businessLicenceImg'];
			View::share('data', $data['data']); 			
		} 

		return $this->display();
	}

	/*
	*	基本信息修改
	*
	*/
	public function updatebasic() {
		$args = Input::all();
        // var_dump($args);
        // exit;
		$data = $this->requestApi('user.updatebasic',$args);    
		if($data['code'] == 0){
			return $this->success($data['msg'] ? $data['msg'] :"更新成功" ,u("SystemConfig/index"));
		}else{ 
			return $this->error($data['msg'] ? $data['msg'] :"更新失败",u("SystemConfig/index"));
		}
	}

	/**
	 * 修改手机号
	 */
	public function changetel() {  
		$data = $this->requestApi('user.get');     
		if($data['code'] == 0){
			View::share('data', $data['data']); 			
		} 	
		return $this->display();
	}

	/**
	 * 修改密码
	 */
	public function changepwd() {
		$data = $this->requestApi('user.get');     
		if($data['code'] == 0){
			View::share('data', $data['data']); 			
		} 	
		return $this->display();
	}
	/**
	 * 修改密码
	 */
	public function updatepwd() {
		$args = Input::all(); 	
		$args['sellerId'] = $args['id'];
		$args['type'] = "change";
		$data = $this->requestApi('user.changepwd',$args);  
		if($data['code']==0){
			$args = [];
			$this->requestApi('user.logout',$args);
			Session::put('seller_token', null);
			Session::put('seller', null);
			return $this->success($data['msg']?$data['msg']:"修改密码成功", u('SystemConfig/changepwd'), $data['data']);
		}else{ 
			return $this->error($data['msg']?$data['msg']:"修改密码失败", u('SystemConfig/changepwd'));
		}
	}
	/**
	 * 修改手机号
	 */
	public function updatetel() {
		$args = Input::all(); 	 
		$args['sellerId']  = $args['id'];
		$data = $this->requestApi('user.changetel',$args);   
		if($data['code']==0){
			return $this->success($data['msg'] ? $data['msg'] :"更换成功", u('SystemConfig/changetel'));
		}else{ 
			return $this->error($data['msg'] ? $data['msg'] :"更换失败", u('SystemConfig/changetel'));
		}
	} 

	/**
	 * 获取员工信息
	 */
	public function search(){ 
		/*获取会员接口*/		
		$list = $this->requestApi('user.search', Input::all()); 
		return Response::json($list['data']);
	}
}
