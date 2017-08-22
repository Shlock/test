<?php 
namespace YiZan\Http\Controllers\Admin; 

//使用的命名空间
use Input,View,Time;

/**
 * 优惠券发放管理
 */
class PromotionSnController extends AuthController {
	/**
	 * 发放列表
	*/
	public function index() {
        $args = Input::get();
		$result = $this->requestApi('promotionsn.lists', $args);
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']);
		}
		return $this->display();
	}
	/**
	 * 删除未发放的优惠券
	*/
	public function destroy() {
		$data = $this->requestApi('promotionsn.delete', Input::all());
		if( $data['code'] > 0 ) {
			return $this->error($data['msg']);
		}
		return $this->success($data['msg']);
	}
}
