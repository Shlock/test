<?php 
namespace YiZan\Http\Controllers\Seller;

use View, Input, Lang;
/**
 * 菜单审核
 */
class GoodsAuditController extends AuthController {
	/**
	 * 菜单审核列表
	 */
	public function index() {
		$args = Input::all();
		$result = $this->requestApi('goods.auditlists', $args);
		if( $result['code'] == 0 ){
			View::share('list', $result['data']['list']);
		}
		return $this->display();
	}
}