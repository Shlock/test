<?php

$paths = Request::segments();
if (count($paths) == 3) {
	global $yzLoader;

	$api_type = ucfirst($paths[0]);
	$api_version = ucfirst($paths[1]);
	$paths = $paths[2];

	define('API_PATH', $paths);
	$paths = explode('.', $paths);

	$yzLoader->setPsr4('YiZan\\Http\\Controllers\\Api\\'.$api_type.'\\', 
		array(base_path() . '/app/Http/Controllers/Api/'.$api_type.'/' . $api_version));

	$action 	= array_pop($paths);//取得操作名称
	//声明操作名称常量
	define('ACTION_NAME', $action);

	define('API_TABLE', strtolower(implode('_', $paths)));

	$controller_name = ucfirst(array_pop($paths));
	//声明控件器名称常量
	define('CONTROLLER_NAME', $controller_name);

	$controller = $controller_name .'Controller';//取得控件器名称
	foreach($paths as $path){
		$controller = ucfirst($path).'\\'.$controller;
	}
	
	Route::any(Request::path(), array('as'=>API_PATH, 'uses'=>'Api\\'.$api_type.'\\'.$controller.'@'.$action));
}
else 
{
	App::abort(404);
}