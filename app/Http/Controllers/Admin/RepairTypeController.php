<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\RepairType;
use View, Input, Lang;

/**
 * 报修类型
 */
class RepairTypeController extends AuthController {

	public function index() {
		$result = $this->requestApi('repairtype.lists');
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']);
		}
		return $this->display();
	}

	public function create(){
		return $this->display('edit');
	}

	public function edit(){
		$result = $this->requestApi('repairtype.get', Input::all());
		View::share('data', $result['data']);
		return $this->display();
	}

	public function save() { 
		$result = $this->requestApi('repairtype.save', Input::all());

		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('RepairType/index'));
	} 

	public function destroy() {
		$args = Input::all(); 
		$result = $this->requestApi('repairtype.delete',$args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98005'), u('RepairType/index') );
	}

	

}
