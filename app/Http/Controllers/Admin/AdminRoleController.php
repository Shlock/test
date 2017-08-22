<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\AdminRole;
use View, Input, Lang, Response;

/**
 * 管理员组
 */
class AdminRoleController extends AuthController {
	protected $_role = [];

	public function index() {
		$post = Input::all();
		!empty($post['page']) ? $args['page'] = intval($post['page']) : $args['page'] = 1;
		!empty($post['pageSize']) ? $args['pageSize'] = intval($post['pageSize']) : $args['pageSize'] = 20;
		$result = $this->requestApi('admin.role.lists',$args);
		if($result['code']==0) {
			View::share('list', $result['data']['list']);
		}
		return $this->display();
	}

	public function edit() {
		$args = Input::all();
		if( !empty($args['id']) ) {
			$args['id'] = $args['id'];
			$result = $this->requestApi('admin.role.get',$args);
			if( $result['code']>0 ) {
				return $this->error($result['msg']);
			}
			View::share('data', $result['data']);
			foreach ($result['data']['access'] as $key => $value) {
				$access[$value['controller']][$value['action']] = 1;
			}
			View::share('access',$access);
		}
		if($this->operationVersion != 'common'){
			$role = Lang::get('_admin_auth_'.$this->operationVersion);			
		}else{
			$role = Lang::get('_admin_auth');
		}
		$role = $this->readRole($role);
		View::share('role', $role);
		return $this->display();
	}

	public function create() {
		if($this->operationVersion != 'common'){
			$role = Lang::get('_admin_auth_'.$this->operationVersion);			
		}else{
			$role = Lang::get('_admin_auth');
		}
		$role = $this->readRole($role);
		View::share('role', $role);
		return $this->display('edit');
	}

	public function readRole($items) {
		$list = [];
	    foreach($items as $item) {
	    	if ( isset($item['nodes']) && is_array($item['nodes']) ) {
	    		$this->readRole($item['nodes']['controllers']);
	    		$list[] = $item;
	    	}
	    	else if ( isset($item['controllers']) && is_array($item['controllers']) ) {
	    		$this->readRole($item['controllers']);
	    		$list[] = $item;
	    	}
	    }
	    return $list;
	}

	public function save() {
		$data = Input::all();
		$args['id'] = $data['id'];
		$args['name'] = $data['name'];
		unset($data['id']);
		unset($data['name']);
		$i = 0;
		foreach ($data as $key => $controller) {
			foreach ($controller as $key2 => $action) {
				$args['access'][$i]['controller'] = $key;
				$args['access'][$i]['action'] = $action;
				$i++;
			}
		}
		unset($data);
		if( empty($args['name']) ) return $this->error(Lang::get('admin.system.10204'));

		if( !empty($args['id']) ) {
			$result = $this->requestApi('admin.role.update',$args);
		}
		else {
			$result = $this->requestApi('admin.role.create',$args);
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('AdminRole/edit',['id'=>$args['id']]),$result['data']);
	}

	public function destroy() {
		$args = Input::all();
		if( !empty( $args['id'] ) ) {
			$result = $this->requestApi('admin.role.delete',$args); 
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98005'), u('AdminRole/index'), $result['data']);
	}

	public function updateStatus() {
		$args = Input::all();
		$result = $this->requestApi('admin.role.updateStatus',$args);
		return Response::json($result);
	}

}
