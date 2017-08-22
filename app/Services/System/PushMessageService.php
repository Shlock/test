<?php 
namespace YiZan\Services\System;

use YiZan\Models\System\PushMessage;
use YiZan\Models\ReadMessage;
use YiZan\Services\SystemConfig;
use YiZan\Utils\String;
use YiZan\Utils\Time;



use DB, Validator;
/**
 * 推送信息
 */
class PushMessageService extends \YiZan\Services\PushMessageService 
{
	/**
     * 推送信息列表
     * @param  string $type app类型
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          推送信息
     */
	public static function getList($type, $userType,$sendTime,$endsendTime,$page, $pageSize) 
    {
        $list = PushMessage::orderBy('id', 'desc');
        
        if($type == true){
            $list->where('type', $type);
        }
        if($userType == true){
        	$list->where('user_type', $userType);
        }
  		if($sendTime == true){
            $list->where('send_time','>=',Time::toTime($sendTime));
            $list->where('send_time','<=',Time::toTime($endsendTime));
        }
		$totalCount = $list->count();        
		$list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();
        return ["list"=>$list, "totalCount"=>$totalCount];
	}
    
    /**
     * 删除推送信息
     * @param int  $id 推送信息id
     * @return array   删除结果
     */
	public static function delete($id) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		];
        
		PushMessage::where('id', $id)->delete();
        ReadMessage::where("message_id", $id)->delete();
        
		return $result;
	}

    /**
     * [push 推送]
     * @param  [type] $data [推送的数据]
     * @return [type]       [description]
     */
    public static function push($data, $appkey, $master_secret) {
        if (!function_exists('curl_init')) {
            return false;
        }
        $header = array(
            'Content-Type: application/json',
            'Authorization: Basic '.base64_encode($appkey.':'.$master_secret)
        );
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ci, CURLOPT_HEADER, false);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ci, CURLOPT_TIMEOUT, 60);
        curl_setopt($ci, CURLOPT_POST, true);
        curl_setopt($ci, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ci, CURLOPT_URL, 'https://api.jpush.cn/v3/push');
        $result = curl_exec($ci);
        curl_close ($ci);
        return $result;

    }


    
}
