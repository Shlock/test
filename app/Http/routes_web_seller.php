<?php
switch ($request_paths[0]) {
	case 'login':
		define('ACTION_NAME', 'login');
		define('CONTROLLER_NAME', 'Public');

		Route::any('login',array('as'=>'login','uses'=>'Seller\PublicController@login'));
		break;
}