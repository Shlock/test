<?php 
namespace YiZan\Http\Controllers\Seller;

use View, Request, Lang, Cache, Redirect;

abstract class AuthController extends BaseController {
	public function __construct() {
		parent::__construct();
		if ($this->sellerId < 1) {//如果未登录时,则退出
			Redirect::to(u('Public/login'))->send();
		}
		View::share('login_seller', $this->seller);
		//验证权限
	}

	/**
	 * [display description]
	 * @param  string $actionName     [description]
	 * @param  string $controllerName [description]
	 * @return [type]                 [description]
	 */
	protected function display($actionName = '', $controllerName = '') {
		//当不为AJAX请求时,获取页面菜单
		if (!Request::ajax() && !Request::wantsJson()) {
			$this->_menuInit();
		}
		return parent::display($actionName, $controllerName);
	}

	/**
	 * 初始化页面菜单
	 * @return [type] [description]
	 */
	private function _menuInit() {
		if ($this->seller['type'] == 3) { //物业公司单独模板
			$seller_auth = Lang::get('_seller_auth_property');
		} else {
			$seller_auth = Lang::get('_seller_auth');
		}
		
		//if (Cache::has('_seller_controller_navs')) {
		if (false) {
			$seller_controller_navs = Cache::get('_seller_controller_navs');
		} else {
			$seller_controller_navs = [];
			foreach ($seller_auth as $key => $nav) {
				if (isset($nav['nodes']) || isset($nav['controllers'])) {
					foreach ($nav['nodes'] as $nkey => $node) {
						foreach ($node['controllers'] as $ckey => $controller) {
							$seller_controller_navs[$ckey] = ['keys' => [$key, $nkey], 'controller' => $controller];
						}
					}
                    foreach ($nav['controllers'] as $ckey => $controller) {
                        $seller_controller_navs[$ckey] = ['keys' => [$key], 'controller' => $controller];
                    }
				} else {
					$seller_controller_navs[$nav['code']] = ['keys' => [$key]];
				}
			}
			Cache::forever('_seller_controller_navs', $seller_controller_navs);
		}
        //print_r($seller_controller_navs);
		//获取当前操作器导航
		$keys = $seller_controller_navs[CONTROLLER_NAME]['keys'];
        //print_r($keys);
		$controller_navs = [];
		$key = array_shift($keys);
		$seller_auth[$key]['selected'] = true;
		$seller_menus = &$seller_auth[$key];

		$controller = $seller_auth[$key];
		$controller_navs[] = ['name' => $controller['name'], 'url' => url($controller['url'])];
		if (count($keys) > 0) {
			$key1 = array_shift($keys);
			$controller = $controller['nodes'][$key1];
			$controller_navs[] = ['name' => $controller['name'], 'url' => url($controller['url'])];
			$seller_auth[$key]['nodes'][$key1]['selected'] = true;
			$seller_auth[$key]['nodes'][$key1]['controllers'][CONTROLLER_NAME]['selected'] = true;
		} else {
			$seller_auth[$key]['controllers'][CONTROLLER_NAME]['selected'] = true;
		}

		if (isset($seller_controller_navs[CONTROLLER_NAME]['controller'])) {
			$controller = $seller_controller_navs[CONTROLLER_NAME]['controller'];
			$controller_navs[] = ['name' => $controller['name'], 'url' => url($controller['url'])];
		}
		View::share('controller_navs', $controller_navs);

		//获取当前操作
		$controller_action = $controller['actions'][ACTION_NAME];
		View::share('controller_action', $controller_action);

		View::share('seller_auth', $seller_auth);
		View::share('seller_menus', $seller_menus);
	}
}
