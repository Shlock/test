<?php 
namespace YiZan\Http\Controllers\Admin; 

use View, Input, Response; 
use YiZan\Models\User;

/**
 * 会员管理
 */
class UserController extends AuthController {  

	/**
	 * 会员列表
	 */
	public function index() {
		//会员列表接口 
		$result = $this->requestApi('user.lists', Input::all());

		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']); 
		}
		return $this->display(); 
	}

	/**
	 * 获取会员
	 * 根据会员Id 获取会员信息
	 */
	public function edit() {	
		$result = $this->requestApi('user.get', Input::all()); 

		//print_r($result);

		/*返回处理*/
		if($result['code'] > 0 ) {
			return $this->error($result['msg'], u('User/index'));
		} 

		

		$data = $result['data'];
		$data['recommendUser'] = '无推荐人';
		$data['LatLng'] = $data['regLat'].','.$data['regLng'];

		$data['extensionLatLng'] = $data['extensionLng'].','.$data['extensionLat'];

		if($data['recommendUid']){
			$recommendUser = User::where('id', $data['recommendUid'])->first();
			$data['recommendUser'] = $recommendUser->mobile.'['.$recommendUser->name.']';
		}
		View::share('data', $data);
		return $this->display(); 
	}
	/**
	* 
	*   修改会员信息
	*	id				会员编号
	*	mobile			注册的手机号码
	*	name			昵称
	*	pwd				5-20位字符串，为空不修改
	*	avatar			头像，为空不修改 
	*/
	public function update() {
		/*修改会员接口*/		
		$result = $this->requestApi('user.update', Input::all()); 
		/*返回处理*/
		if( $result['code'] == 0 ) {
			return $this->success($result['msg'], u('User/index'));
		} else {
			return $this->error($result['msg']);
		}
	}

	/**
	 * 删除会员信息
	 */
	public function destroy() {
		$result = $this->requestApi('user.delete', Input::all());
		if( $result['code'] > 0 ) {
			return $this->error($result['msg'], url('User/index'));
		}
		return $this->success($result['msg'], url('User/index'));
	}
	
	/**
	 * 搜索会员
	 * 根据会员Id 获取会员信息
	 */
	public function search() {
		/*获取会员接口*/
		$list = $this->requestApi('user.search', Input::all()); 
		return Response::json($list['data']);
	}
	/**
	 * 会员洗车券
	 * 根据会员Id 获取洗车券
	 */
	public function detail() {
		/*获取会员接口*/
		$args =  Input::all();
		$list = $this->requestApi('user.promotion.lists', $args);
		View::share('list', $list['data']['list']);
		View::share('id', $args['id']);
		return $this->display(); 
	}
}
