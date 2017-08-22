<?php
switch ($request_paths[0]) {
	case 'login':
		define('ACTION_NAME', 'login');
		define('CONTROLLER_NAME', 'User');
		Route::any('login',array('as'=>'login','uses'=>'Wap\UserController@login'));
		break;
}