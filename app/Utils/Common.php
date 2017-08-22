<?php
function u($url, $args = array()){
    if (strpos($url, '#') !== false) {
		$urls = explode('#', $url, 2);
		$url = $urls[1];
		URL::forceRootUrl(Request::getScheme().'://'.$urls[0].'.'.Config::get('app.domain'));
	}
	$url = URL::to($url) . (count($args) > 0 ? '?' : '') . http_build_query($args);
	URL::forceRootUrl('');
    return $url;
}

function formatImage($url,$withd = 0,$height = 0, $isCat = 1){
	$url .= '@';
	if($withd > 0){
		$url .= $withd.'w_';
	}
	if($height > 0){
		$url .= $height.'h_';
	}
	if($isCat > 0){
		$url .= '1e_1c_';
	}
	return $url.'1o.jpg';
}

function yzday($time){
	return YiZan\Utils\Time::toDate($time, 'Y-m-d');
}

function yzhour($time){
	return YiZan\Utils\Time::toDate($time, 'Y-m-d H:i');
}

function yztime($time, $format = 'Y-m-d H:i:s'){
	return YiZan\Utils\Time::toDate($time, $format);
}

function formatTime($time){
	$sub = UTC_TIME - $time;
	if($sub < 60){
    	$timestr = $sub . '秒钟';
    } else if($sub < 3600){
    	$timestr = (int)($sub/60) . '分钟';
    } else if($sub < 3600 * 24){
    	$timestr = (int)($sub/3600) . '小时';
    } else {
    	$timestr = (int)($sub/(3600 * 24)) . '天';
    }
    return $timestr;
}

//格式化html
function yzHtmlSpecialchars($string, $flags = NULL){
	if(is_array($string)){
		foreach($string as $key => $val){
			$string[$key] = yzHtmlSpecialchars($val, $flags);
		}
	}else{
		if($flags === NULL){
			$string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
			if(strpos($string, '&amp;#') !== false){
				$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
			}
		}else{
			if(PHP_VERSION < '5.4.0'){
				$string = htmlspecialchars($string, $flags);
			}else{
				$string = htmlspecialchars($string, $flags,'UTF-8');
			}
		}
	}
	return $string;
}

//还原html格式化后的字符串
function yzHtmlSpecialcharsDecode($string){ 
	if(is_array($string)){
		foreach($string as $key => $val){
			$string[$key] = yzHtmlSpecialcharsDecode($val, $flags);
		}
	}else{
		$string = htmlspecialchars_decode($string);
	}
	return $string; 
}