<?php
error_reporting(E_ERROR);
include base_path().'/app/Utils/Common.php';
if (Request::server('HTTP_HOST') == '192.168.1.201') {
    define('SERVICE_DOMAIN', 'api');
    
    include 'routes_api.php';
} else {
    $host = explode('.', Request::server('HTTP_HOST'));
    $domain = array_shift($host);
    $host   = implode('.', $host);
    $domains = array(
        'www'       => 'www',
        'wap'       => 'wap',
        'admin'     => 'admin',
        'api'       => 'api',
        'seller'    => 'seller',
        'staff'     => 'staff',
        'callback'  => 'callback',
		'resource'  => 'resource',
    );

    if(isset($domains[$domain])){
        $domain = $domains[$domain];
        define('SERVICE_DOMAIN', $domain);
        if ($domain == 'api') {
            include 'routes_api.php';
        } else {
            include 'routes_web.php';
        }
    } else {
        App::abort(404);
    }
}


