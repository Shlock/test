<?php

namespace YiZan\Http\Controllers\Admin;

use View,
    Request,
    Lang,
    Cache,
    Redirect;

abstract class AuthController extends BaseController {

    public function __construct() {
        parent::__construct();
        if ($this->adminId < 1) {//如果未登陆时,则退出
            Redirect::to(u('Public/login'))->send();
        }
        View::share('login_admin', $this->adminUser);
        //验证权限
        if (!$this->checkAuth()) {
            if (Request::ajax()) {
                die('{"status":false,"msg":"没有访问权限，请重新选择操作","url":"","data":[]}');
            } else {
                die($this->error("没有访问权限，请重新选择操作")->render());
            }
        }
    }

    /**
     * Summary of checkAuth
     * @return mixed
     */
    protected function checkAuth() {
        if ($this->adminUser["name"] == "admin" ||
                strcasecmp("Index", CONTROLLER_NAME) == 0) {
            return true;
        }

        $access = $this->adminUser["role"]["access"];
        $role_auths = $this->getRoleControllerActionNavs($this->adminUser["role"]['id'], $this->adminUser["role"]["access"]);
        if (isset($role_auths['actions'][strtolower(CONTROLLER_NAME . '/' . ACTION_NAME)])) {
            return true;
        }
        return false;
    }

    protected function display($actionName = '', $controllerName = '') {
        //当不为AJAX请求时,获取页面菜单
        if (!Request::ajax() && !Request::wantsJson()) {
            $this->_menuInit();
        }
        return parent::display($actionName, $controllerName);
    }

    protected function getRoleControllerActionNavs($roldId, $accessList) {
        if ($this->operationVersion != 'common') {
            $admin_auth = Lang::get('_admin_auth_' . $this->operationVersion);
        } else {
            $admin_auth = Lang::get('_admin_auth');
        }

        $cache_key = '_admin_controller_action_navs';
        //
        if (Cache::has($cache_key)) {
            $admin_controller_action_navs = Cache::get($cache_key);
        } else {
            $admin_controller_action_navs = [];
            foreach ($admin_auth as $key => $nav) {
                if (isset($nav['nodes'])) {
                    foreach ($nav['nodes'] as $nkey => $node) {
                        foreach ($node['controllers'] as $ckey => $controller) {
                            foreach ($controller['actions'] as $akey => $action) {
                                $action['navs'] = [$key, $nkey];
                                $admin_controller_action_navs[strtolower($ckey . '/' . $akey)] = $action;
                            }
                        }
                    }
                }

                if (isset($nav['controllers'])) {
                    foreach ($nav['controllers'] as $ckey => $controller) {
                        foreach ($controller['actions'] as $akey => $action) {
                            $action['navs'] = [$key];
                            $admin_controller_action_navs[strtolower($ckey . '/' . $akey)] = $action;
                        }
                    }
                }
            }
            Cache::forever($cache_key, $admin_controller_action_navs);
        }

        $cache_key = '_admin_controller_action_navs_' . $roldId;
        //
        if (Cache::has($cache_key)) {
            return Cache::get($cache_key);
        } else {
            $role_admin_controller_action_navs = ['navs' => [], 'controllers' => [], 'actions' => []];
            foreach ($accessList as $access) {
                $action_key = strtolower($access["controller"] . '/' . $access["action"]);
                if (isset($admin_controller_action_navs[$action_key])) {
                    $action = $admin_controller_action_navs[$action_key];
                    $navs = $action['navs'];
                    $nav = array_shift($navs);
                    if (!isset($role_admin_controller_action_navs['navs'][$nav])) {
                        $role_admin_controller_action_navs['navs'][$nav] = [
                            'name' => $admin_auth[$nav]['name'],
                            'url' => '',
                            'nodes' => []
                        ];
                    }

                    $node = '';
                    if (count($navs) > 0) {
                        $node = array_shift($navs);
                        if (!isset($role_admin_controller_action_navs['navs'][$nav]['nodes'][$node])) {
                            $role_admin_controller_action_navs['navs'][$nav]['nodes'][$node] = [
                                'name' => $admin_auth[$nav]['nodes'][$node]['name'],
                                'url' => ''
                            ];
                        }
                    }

                    if (!isset($role_admin_controller_action_navs['controllers'][$access['controller']])) {
                        if (empty($node)) {
                            $name = $admin_auth[$nav]['controllers'][$access['controller']]['name'];
                        } else {
                            $name = $admin_auth[$nav]['nodes'][$node]['controllers'][$access['controller']]['name'];
                        }

                        $role_admin_controller_action_navs['controllers'][$access['controller']] = [
                            'name' => $name,
                            'url' => ''
                        ];
                    }

                    if (isset($action['show_menu'])) {
                        $url = $access["controller"] . '/' . $access["action"];
                        if (empty($role_admin_controller_action_navs['navs'][$nav]['url'])) {
                            $role_admin_controller_action_navs['navs'][$nav]['url'] = $url;
                        }

                        if (!empty($node) &&
                                empty($role_admin_controller_action_navs['navs'][$nav]['nodes'][$node]['url'])) {
                            $role_admin_controller_action_navs['navs'][$nav]['nodes'][$node]['url'] = $url;
                        }

                        if (empty($role_admin_controller_action_navs['controllers'][$access['controller']]['url'])) {
                            $role_admin_controller_action_navs['controllers'][$access['controller']]['url'] = $url;
                        }
                    }
                    $expands = $this->getActionExpands($access["action"], $action);
                    unset($action['expand']);

                    $role_admin_controller_action_navs['actions'][$action_key] = $action;

                    foreach ($expands as $expand) {
                        $role_admin_controller_action_navs['actions'][strtolower($access["controller"] . '/' . $expand)] = $action;
                    }
                }
            }
            Cache::forever($cache_key, $role_admin_controller_action_navs);
            return $role_admin_controller_action_navs;
        }
    }

    private function getActionExpands($akey, $action) {
        $expands = [];
        if ($akey == 'edit') {
            $expands = ['detail', 'save', 'update', 'updateStatus'];
        } elseif ($akey == 'create') {
            $expands = ['detail', 'save', 'updateStatus'];
        }
        $expands = isset($action['expand']) ? array_merge($action['expand'], $expands) : $expands;
        return array_unique($expands);
    }

    private function formatUrl(&$data, $old, $role_auths) {
        $key = strtolower($old['url']);
        if (isset($role_auths['actions'][$key])) {
            $data['url'] = $old['url'];
        }
    }

    /**
     * 初始化页面菜单
     * @return [type] [description]
     */
    private function _menuInit() {

        if ($this->operationVersion != 'common') {
            $admin_auth = Lang::get('_admin_auth_' . $this->operationVersion);
        } else {
            $admin_auth = Lang::get('_admin_auth');
        }
        $role_auths = $this->getRoleControllerActionNavs($this->adminUser["role"]['id'], $this->adminUser["role"]["access"]);
        //获取当前操作导航
        $action = $role_auths['actions'][strtolower(CONTROLLER_NAME . '/' . ACTION_NAME)];
        $controller_navs = [];
        $navs = $action['navs'];

        $nav_key = array_shift($navs);
        View::share('self_nav', $nav_key);

        $admin_auth[$nav_key]['selected'] = true;
        $nav = $role_auths['navs'][$nav_key];
        $this->formatUrl($nav, $admin_auth[$nav_key], $role_auths);
        $controller_navs[] = $nav;

        if (count($navs) > 0) {
            $node = array_shift($navs);
            $admin_auth[$nav_key]['nodes'][$node]['selected'] = true;
            $nav = $role_auths['navs'][$nav_key]['nodes'][$node];
            $this->formatUrl($nav, $admin_auth[$nav_key]['nodes'][$node], $role_auths);
            $controller_navs[] = $nav;

            $admin_auth[$nav_key]['nodes'][$node]['controllers'][CONTROLLER_NAME]['selected'] = true;
            $nav = $role_auths['controllers'][CONTROLLER_NAME];
            $this->formatUrl($nav, $admin_auth[$nav_key]['nodes'][$node]['controllers'][CONTROLLER_NAME], $role_auths);
            $controller_navs[] = $nav;
        } else {
            $nav = $role_auths['controllers'][CONTROLLER_NAME];
            $this->formatUrl($nav, $admin_auth[$nav_key]['controllers'][CONTROLLER_NAME], $role_auths);
            $controller_navs[] = $nav;

            $admin_auth[$nav_key]['controllers'][CONTROLLER_NAME]['selected'] = true;
            $admin_auth[$nav_key]['controllers'][CONTROLLER_NAME]['url'] = $nav['url'];
        }

        View::share('controller_navs', $controller_navs);
        //获取当前操作
        View::share('controller_action', $action);

//        print_r($admin_auth[$nav_key]);

        View::share('admin_auth', $admin_auth);
        View::share('admin_menus', $admin_auth[$nav_key]);
        View::share('role_auths', $role_auths);
    }

}
