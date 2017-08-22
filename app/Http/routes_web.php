<?php
$paths = Request::segments();
$web_type = ucfirst(SERVICE_DOMAIN);

if (count($paths) > 0) {
	$request_paths = $paths;
	@include 'routes_web_'.SERVICE_DOMAIN.'.php';

	if (!defined('CONTROLLER_NAME')) {
		if (count($paths) > 1) {
			$as_name 	= implode('.', $paths);
			$uri 		= implode('/', $paths);
			$action 	= array_pop($paths);//取得操作名称

			//声明操作名称常量
			define('ACTION_NAME', $action);

			$controller_name = ucfirst(array_pop($paths));
			//声明控件器名称常量
			define('CONTROLLER_NAME', $controller_name);

			$controller = $controller_name .'Controller';//取得控件器名称
			$controller_path = '';
			foreach($paths as $path){
				$controller_path .= ucfirst($path).'\\';
			}
			Route::any($uri, array('as'=>$as_name, 'uses'=>$web_type.'\\'.$controller_path.$controller.'@'.$action));
		} else {
			App::abort(404);
		}
	}
} else {
	//声明操作名称常量
	define('ACTION_NAME', 'index');

	//声明控件器名称常量
	define('CONTROLLER_NAME', 'Index');
	Route::any('/', array('as'=>'/', 'uses'=>$web_type.'\\IndexController@index'));
}