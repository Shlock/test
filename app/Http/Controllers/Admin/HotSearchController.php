<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\HotSearch;
use View, Input, Lang;

/**
 * 开通城市管理
 */
class HotSearchController extends AuthController {
	public function index() {
        $args['page'] = Input::get('page') ? Input::get('page') : 0;
		$result = $this->requestApi('city.lists',$args);
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']);
		}
        $zx = array("1", "18", "795", "2250");
        View::share('zx', $zx);

        return $this->display();
	}

	public function create(){
		return $this->display('edit');
	}

	public function save() {
		$city_id = 0;
		if (Input::get('areaId') > 0) {
			$city_id = Input::get('areaId');
		} elseif(Input::get('cityId') > 0) {
			$city_id = Input::get('cityId');
		} elseif(Input::get('provinceId') > 0) {
			$city_id = Input::get('provinceId');
		}

		if($city_id < 1) return $this->error(Lang::get('admin.code.27006'));
		$result = $this->requestApi('city.create',['cityId' => $city_id, 'sort' => Input::get('sort')]);

		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('HotSearch/index'), $result['data']);
	}

	/**
	 * 设置默认城市
	 */
	public function isdefault() {
		$args = Input::all();
		if($args['id']) {
			$result = $this->requestApi('city.setdefault',$args);
			if($result['code']>0){
				return $this->error($result['msg']);
			}
			return $this->success(Lang::get('admin.code.98003'), u('HotSearch/index'), $result['data']);
		}
	}

	public function destroy() {
		$args = Input::all();
		if( empty($args['id']) ) return $this->error(Lang::get('admin.code.27007'));
		$args['cityId'] = $args['id'];
		$result = $this->requestApi('city.delete',$args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98005'), u('HotSearch/index'), $result['data']);
	}

	

}
