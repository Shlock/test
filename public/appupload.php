<?php
error_reporting(E_ERROR);

session_start();

$host="127.0.0.1";
$database="";
$username="root";
$password="";
$prefix="yz_";

$lines = file(__DIR__.'/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) 
{
	// Only use non-empty lines that look like setters
	if (strpos($line, '=') !== false) 
	{
		list($name, $value) = array_map('trim', explode('=', $line, 2));
		
		if($name == "DB_HOST")
		{
			$host = $value;
		}
		else if($name == "DB_DATABASE")
		{
			$database = $value;
		}
		else if($name == "DB_USERNAME")
		{
			$username = $value;
		}
		else if($name == "DB_PASSWORD")
		{
			$password = $value;
		}
		else if($name == "DB_PREFIX")
		{
			$prefix = $value;
		}
	}
}

function Update($code, $val)
{
    global $host, $username, $password, $database, $prefix;
    
	$connect = @mysql_connect($host, $username, $password);
	
	@mysql_select_db($database, $connect);
	
	$val = mysql_real_escape_string($val);
	
	@mysql_query("
		UPDATE {$prefix}system_config
			SET val = '{$val}'
			WHERE code = '{$code}'");
    
	@mysql_close($connect);
}

/* ios 更新*/
if( $_GET["name"] == "iosstaffapp.ipa" || 
    $_GET["name"] == "iosbuyerapp.ipa" ||
    $_GET["name"] == "androidstaffapp.apk" || 
    $_GET["name"] == "androidbuyerapp.apk")
{
    $code = $_GET["name"] == "iosstaffapp.ipa" ? "seller_app_version" :
            $_GET["name"] == "iosbuyerapp.ipa" ? "buyer_app_version" :
            $_GET["name"] == "androidstaffapp.apk" ? "staff_android_app_version" : 
                "buyer_android_app_version";
    
    Update($code, $_GET["version"]);
    
    $server = $_SERVER["SERVER_NAME"] . ($_SERVER["SERVER_PORT"] == 80 ? "" : (":" . $_SERVER["SERVER_PORT"]));
    
    if( $_GET["name"] == "androidbuyerapp.apk")
    {
        Update("buyer_android_app_down_url", "http://{$server}/app/androidbuyerapp.apk");
    }
    else if( $_GET["name"] == "androidstaffapp.apk")
    {
        Update("staff_android_app_down_url", "http://{$server}/app/androidstaffapp.apk");
    }
    else if( $_GET["name"] == "iosbuyerapp.apk")
    {
        Update("buyer_app_down_url", "http://{$server}/buyerapp.php");
    }
    else if( $_GET["name"] == "iosstaffapp.apk")
    {
        Update("staff_app_down_url", "http://{$server}/staffapp.php");
    }
}

echo "Success";