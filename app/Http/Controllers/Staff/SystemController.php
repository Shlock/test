<?php namespace YiZan\Http\Controllers\Staff;
/**
 * app相关信息
 */
class SystemController extends \YiZan\Http\Controllers\YiZanViewController 
{
	public function __construct()
    {
		
	}
    public function init()
	{
		header("Content-type:text/json; charset=utf-8");

        $root = array();
        
        $root["sina_app_api"] = 0;
        $root["qq_app_api"] = 0;
        $root["wx_app_api"] = 1;
        $root["statusbar_hide"] = 0;
        $root["statusbar_color"] = "#55ACEF";
        $root["topnav_color"] = "#55ACEF";
        $root["ad_img"] = "";
        $root["ad_http"] = "";
        $root["ad_open"] = 0;
        $root["site_url"] = u("", ["show_prog"=>1]);
        $root["version"]["serverVersion"] = "0";
        $root["version"]["has_upgrade"] = 0;
        $root["reload_time"] = 60;
        $root["top_url"][0] = u("");
        
		die(json_encode($root));
	}
}
