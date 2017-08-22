<?php namespace YiZan\Utils;

class Http {
	/**
	 * 发起GET请求
	 * @param  string  $url     请求链接
	 * @param  array   $params  请求参数
	 * @param  integer $timeout 超时时间
	 * @return string           响应数据
	 */
	public static function get($url, $params = array(), $timeout = 10) {
		return self::request($url, $params, 'GET', [], $timeout);
	}

	/**
	 * 发起POST请求
	 * @param  string  $url     请求链接
	 * @param  array   $params  请求参数
	 * @param  boolean $multi   是否为文件上传
	 * @param  integer $timeout 超时时间
	 * @return string           响应数据
	 */
	public static function post($url, $params = array(), $multi = array(), $timeout = 10) {
		return self::request($url, $params, 'POST', $multi, $timeout);
	}

	/**
	 * 根据IP获取所在地址
	 * @param  string  $ip  要解析的IP
	 * @return array        IP所在地址信息
	 */
	public static function getIpLocation($ip) {
		$location = @file_get_contents('http://whois.pconline.com.cn/ipJson.jsp?ip='.$ip);
		if (!empty($location)) {
			$location = trim($location);
			$index = strpos($location, 'IPCallBack(');
			if ($index > 0) {
				$location = iconv('GBK', 'UTF-8', substr($location,$index + 11, - 3));
				$location = @json_decode($location, true);
				if ($location && isset($location['pro']) && isset($location['city'])) {
					if (empty($location['city'])) {
						$location['city'] = $location['addr'];
					}
					return array('province'=>$location['pro'], 'city'=>$location['city']);
				}
			}
		}
		return array('province'=>'', 'city'=>'');
	}
    /**
     * Summary of postSsl
     * @param mixed $url 
     * @param mixed $sslPem 
     * @param mixed $vars 
     * @param mixed $second 
     * @param mixed $aHeader 
     * @return mixed
     */
    public static function postSsl($url, $sslPem, $keyPem, $vars, $second = 30,$aHeader=array())
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second); 
        
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
 
        ;
        curl_setopt($ch,CURLOPT_URL,$url);
        
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        
        //第一种方法，cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT,  $sslPem);
        
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY, $keyPem);
                
        if( count($aHeader) >= 1 )
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }
        
        curl_setopt($ch,CURLOPT_POST, 1);
        
        curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
        
        $data = curl_exec($ch);
        
        if($data)
        {
            curl_close($ch);
            return $data;
        }
        else 
        { 
            $error = curl_errno($ch);
            echo $sslPem; 
            echo "call faild, errorCode:{$error}\n"; 
            curl_close($ch);
            return false;
        }
    }
	/**
	 * 发起一个HTTP/HTTPS的请求, 基于curl
	 * @param $url 接口的URL
	 * @param $params 接口参数   array('content'=>'test'); 即GET、POST的提交参数
	 * @param $method 请求类型   GET|POST
	 * @param $multi 文件信息
	 * @return string
	 */
	
	/**
	 * 发起一个HTTP/HTTPS的请求, 基于curl
	 * @param  string  $url     请求链接
	 * @param  array   $params  请求参数
	 * @param  string  $method  请求类型 GET|POST
	 * @param  array   $multi   文件信息
	 * @param  integer $timeout [description]
	 * @return [type]           [description]
	 */
	private static function request( $url , $params = array(), $method = 'GET' , $multi = array(), $timeout = 10) {

		if (is_array($params)) 
		{
			ksort($params);
			
			$content = http_build_query($params);
			
			$content_length = strlen($content);
			
			$options = array(
				'http' => array(
						'method' => 'POST',
						'header' =>
						"Content-type: application/x-www-form-urlencoded\r\n" .
						"Content-length: $content_length\r\n",
						'content' => $content
						)
					);
			return file_get_contents($url, false, stream_context_create($options));
		}

		if (!function_exists('curl_init')) {
			return false;
		}
		$method = strtoupper($method);
		$ci = curl_init();
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ci, CURLOPT_HEADER, false);

		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $timeout);

		switch ($method) {
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, TRUE);
				if (!empty($params)) {
					if($multi) {
						foreach($multi as $key => $file) {
							$params[$key] = '@'.$file;
						}
					}

					if(is_array($params)) {
						curl_setopt($ci, CURLOPT_POSTFIELDS, http_build_query($params));
					} else {
						curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
					}
				}
				break;
			case 'GET':
				if (!empty($params)) {
					$url = $url . (strpos($url, '?') ? '&' : '?') . (is_array($params) ? http_build_query($params) : $params);
				}
				break;
		}
		curl_setopt($ci, CURLOPT_URL, $url);
		$response = curl_exec($ci);
		curl_close ($ci);
		return $response;
	}
}
