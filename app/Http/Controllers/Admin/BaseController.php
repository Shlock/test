<?php namespace YiZan\Http\Controllers\Admin;

use YiZan\Http\Controllers\YiZanViewController;
use View, Input, Response;

class BaseController extends YiZanViewController {
	/**
	 * API调用类型
	 * @var string
	 */
	protected $apiType = 'system';

	/**
	 * 调用模板
	 * @var string
	 */
	protected $tpl = 'admin';

	/**
	 * 管理员信息
	 * @var array
	 */
	protected $adminUser;

	/**
	 * 管理员编号
	 * @var int
	 */
	protected $adminId;

	/**
	 * 树形无限极分类数组
	 * @var Array
	 */
	protected $_cates = [];

	/**
	 * 初始化信息
	 */
	public function __construct() {
		parent::__construct();
		//设置后台管理员
		$this->setAdminUser(Session::get('admin_user'));
		View::share('site_config', $this->getConfig());
	}

	/**
	 * 设置后台管理员
	 * @param array $user 管理员信息
	 */
	protected function setAdminUser($adminUser) {
		if ($adminUser) {
			$this->adminUser 	= $adminUser;
			$this->adminId 		= $adminUser['id'];
			View::share('login_admin', $this->adminUser);
		}
		Session::put('admin_user', $adminUser);
	}

	/**
	 * 调用API
	 * @param  string 	$method 接口名称
	 * @param  array  	$args   参数
	 * @param  array  	$data   提交数据
	 * @return array          	API返回数据
	 */
	protected function requestApi($method, $args = [], $data = []){
		$data['adminId'] = $this->adminId;
		return parent::requestApi($method, $args, $data);
	}

	/**
	 * 获取模板路径
	 * @param  string $controllerName 控制器名称
	 * @param  string $actionName     方法名称
	 * @return string                 模板路径
	 */
	protected function getDisplayPath($controllerName, $actionName) {
		if ($this->operationVersion != 'common'){
			$tpl_path = base_path()."/resources/views/{$this->tpl}/{$this->operationVersion}/{$controllerName}/{$actionName}.blade.php";
			if(file_exists($tpl_path)) {
				return "{$this->tpl}.{$this->operationVersion}.{$controllerName}.{$actionName}";
			}
		}
		return "{$this->tpl}.common.{$controllerName}.{$actionName}";
	}

	/**
	 * 树形无限极分类显示
	 * @param  int $pid  顶级ID
	 * @param  array  $items 相关参数
	 * @param  int $level  层级
	 * @param  string $levelrel  层级扁平化
	 * @return Array $list 层级多维数组
	 * @return Array $this->_cates 层级二维数组
	 */
	protected function generateTree($pid, $items, $level = 0, $levelrel=''){
		$list = [];
	    foreach($items as $item) {
	    	if ($item['pid'] == $pid) {
	    		$item['level'] = $level;
	    		if($pid==0) {
	    			$item['levelname'] = $item['name'];
	    			$item['levelrel']  = $item['name'];
	    		}else{
	    			$item['levelname'] = str_repeat("&nbsp;&nbsp;&nbsp;", $level)."├".$item['name']; //带有空格的层级关系
	    			$item['levelrel']  = $levelrel.'|'.$item['name'];//带父级及以上的层级关系
	    		}
	    		$this->_cates[] = $item;
	    		$item['childs'] = $this->generateTree($item['id'], $items, $level + 1, $item['levelrel']);
	    		$list[] = $item;
	    	}
	    }
	    return $list;
	}
	/**
	 * 修改状态
	 */
	public function updateStatus() {
		$args = Input::all();
		$result = $this->requestApi('common.updateStatus',$args);
		return Response::json($result);
	}
}
