<?php 
namespace YiZan\Http\Controllers\Wap;

use View;
class GoodsCartController extends AuthController {

	/**
	 * 购物车列表
	 */
	public function index() { 
		// $result_addr = $this->requestApi('user.address.lists');
		// View::share('addr', $result_addr['data'][0]);
		$result_cart = $this->requestApi('shopping.lists');
		// print_r($result_cart['data']);
		View::share('cart', $result_cart['data']);
		View::share('nav', 'goodscart');
		return $this->display();
	}
}
