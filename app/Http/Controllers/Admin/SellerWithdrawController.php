<?php 
namespace YiZan\Http\Controllers\Admin;
use View, Input, Lang, Time;

/**
 * 商家提现
 */
class SellerWithdrawController extends AuthController {
	public function index() {
	    $args = Input::all();
        $nav = (int)$args['nav'] > 0 ? (int)$args['nav'] : 1;
        $args['beginTime'] = !empty($args['beginTime']) ? Time::toTime($args['beginTime']) : 0;
        $args['endTime'] = !empty($args['endTime']) ? Time::toTime($args['endTime']) : 0;
		$result = $this->requestApi('withdraw.lists',$args);
        //print_r($result);
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']);
		}
        View::share('nav', $nav);
        View::share('url', u('SellerWithdraw/index',['nav'=>$nav, 'status' => (int)$args['status']]));
		return $this->display();
	}

	public function create(){
		return $this->display('edit');
	}
	
	public function edit(){
	    return $this->display();
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
		return $this->success(Lang::get('admin.code.98008'), u('City/index'), $result['data']);
	}

	
	public function dispose() {
	    $result = $this->requestApi('seller.withdraw.dispose', Input::all());
		/*返回处理*/ 
		if($result['code']==0){
			return $this->success($result['msg']);
		}else{ 
			return $this->error($result['msg']);
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
		return $this->success(Lang::get('admin.code.98005'), u('City/index'), $result['data']);
	}

	

}
