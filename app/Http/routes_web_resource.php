<?php
if (Request::has('img') && Request::has('wheco')) {
	define('ACTION_NAME', 'Image');
	define('CONTROLLER_NAME', 'index');

	$as_name 	= implode('.', $paths);
	$uri 		= implode('/', $paths);

	Route::any($uri, array('as'=>$as_name,'uses'=>'Resource\ImageController@index'));
}