<?php 
namespace YiZan\Services;

use YiZan\Models\System\PushMessage;
use YiZan\Services\SystemConfig;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Models\Order;
use DB, Validator, View;
/**
 * 推送信息
 */
class PushMessageService extends BaseService {
    /**
     * 通知
     * @param  [type] $userId     [description]
     * @param  [type] $mobile     [description]
     * @param  [type] $title      [description]
     * @param  [type] $noticeTpe  [description]
     * @param  [type] $noticeArgs [description]
     * @param  string $clientType [description]
     * @param string $pushArgs 推送参数
     * @param string $pushType 推送类型 1:普通信息 2:html args为url地址 3:订单信息 args为订单ID, 4:接受或拒绝订单 args为订单ID
     * @return [type]             [description]
     */
    public static function notice($userId, $mobile, $noticeTpe, $noticeArgs = [], $sentType = ['sms', 'app'], $clientType = 'buyer',$pushType = '1',$pushArgs = '0', $sound="") {
        $newClientType = $clientType;
        $clientType = $clientType == 'seller' ? 'staff' : $clientType;
        View::share($noticeArgs);
        $title = View::make('api.'.$noticeTpe.'.title')->render();
        foreach ($sentType as $type) {
            $content = View::make('api.'.$noticeTpe.'.content.'.$type)->render();
            switch ($type) {
                case 'sms':
                    SmsService::sendSms($content, $mobile);
                break;
                case 'app':
                    //极光推送
                    $config = SystemConfigService::getConfigByGroup($clientType);
                    $appkey = $config[$clientType.'_push_appkey'];
                    $master_secret = $config[$clientType.'_push_master_secret'];
                    $data = (object)array();
                    $data->platform = 'all';
                    $data->audience = (object)array();
                    //$data->audience->tag = array($clientType);
                    $data->audience->alias = array($clientType.'_'.$userId);
                    $data->notification = (object)array();
                    $data->notification->alert = $title;
                    $data->notification->android = (object)array();
                    $data->notification->android->alert = $content;
                    $data->notification->android->title = $title;
                    $data->notification->android->extras = (object)array();
                    $data->notification->android->extras->type = (int)$pushType;
                    $data->notification->android->extras->args = $pushArgs;
                    $data->notification->android->extras->sound = $sound;
                    $data->notification->android->sound = $sound;
                    $data->notification->ios = (object)array();
                    $data->notification->ios->extras = (object)array();
                    $data->notification->ios->alert = $content;
                    $data->notification->ios->extras->type = (int)$pushType;
                    $data->notification->ios->extras->args = $pushArgs;
                    $data->notification->ios->extras->sound = $sound;
                    $data->notification->ios->sound = $sound;
                    $data->notification->ios->badge = "+1";

                    $data->message = (object)array();
                    $data->message->title = $title;
                    $data->message->msg_content = $content;
                    $data->message->extras = (object)array();
                    $data->message->extras->type = (int)$pushType;
                    $data->message->extras->args = $pushArgs;
                    $data->message->extras->sound = $sound;
                    $data->message->sound = $sound;

                    $data->options = (object)array();
                    $data->options->apns_production = true;
                    self::push(json_encode($data), $appkey, $master_secret);
                break;
            } 
        }
        self::createMessage($newClientType, $title, $content, 1, $userId, $pushType, $pushArgs);
    }

    /**
     * 
     *
     */
    public static function createMessage($type, $title, $content, $userType, $users, $sendType, $args) {
        $push = new PushMessage();
  
        $push->type         = $type;
        $push->title        = $title;
        $push->content      = $content;
        $push->user_type    = $userType;
        $push->users        = $users;
        $push->args         = $args;
        $push->send_type    = $sendType;
        $push->send_time    = Time::getTime();
        $push->seller_id    = 0;
        if ($sendType == 3 || $sendType == 4) {
            $push->seller_id = Order::where('id', $args)->pluck('seller_id');
        }
        $push->save();
        
        self::PushReadMessage($push->id, $type, $userType, $users);
    }

    /**
     * 添加推送信息
     * @param string $type 类型 buyer 买家 seller卖家
     * @param string $title 标题
     * @param string $content 内容
     * @param int $userType 要推送的会员类型
     * @param string $users 要推送的会员编号
     * @param string $args 推送参数
     * @param string $sendType 推送类型 1:普通信息 2:html args为url地址 3:订单信息 args为订单ID
     * @return array   创建结果
     */
    public static function create($type, $title, $content, $userType, $users, $args, $sendType) 
    {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );
        
        $rules = array(
            'content'         => ['required']
        );

        $messages = array
        (
            'content.required'      => 70302    // 内容不能为空
        );
        
        $validator = Validator::make(
            [
                'content'      => $content
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails()) 
        {
            $messages = $validator->messages();
            
            $result['code'] = $messages->first();
            
            return $result;
        }

        //极光推送
        $config = SystemConfigService::getConfigByGroup($type);
        $appkey = $config[$type.'_push_appkey'];
        $master_secret = $config[$type.'_push_master_secret'];
        if ($appkey == '' || $master_secret == '') {
            $result['code'] = 70308;
        }
        $users_array = explode(',', $users);
        $data = (object)array();
        $data->platform = 'all';
        $data->audience = (object)array();
        if ($userType == 1) {
            $alias = array();
            foreach ($users_array as $k=>$v) {
                $alias[$k] = $type.'_'.$v;
            } 
            $data->audience->alias = $alias;    
        } else {
            $data->audience->tag = array($type);
        }
        $data->notification = (object)array();
        $data->notification->alert = $title;
        $data->notification->android = (object)array();
        $data->notification->android->alert = $content;
        $data->notification->android->title = $title;
        $data->notification->android->extras = (object)array();
        $data->notification->android->extras->type = (int)$sendType;
        $data->notification->android->extras->args = $args;
        $data->notification->ios = (object)array();
        $data->notification->ios->extras = (object)array();
        $data->notification->ios->alert = $content;
        $data->notification->ios->extras->type = (int)$sendType;
        $data->notification->ios->extras->args = $args;
        $data->notification->ios->badge = "+1";

        $data->message = (object)array();
        $data->message->title = $title;
        $data->message->msg_content = $content;
        $data->message->extras = (object)array();
        $data->message->extras->type = (int)$sendType;
        $data->message->extras->args = $args;

        $data->options = (object)array();
        $data->options->apns_production = true;
        self::push(json_encode($data), $appkey, $master_secret);
        
        $push = new PushMessage();
  
        $push->type         = $type;
        $push->title        = $title;
        $push->content      = $content;
        $push->user_type    = $userType;
        $push->users        = $users;
        $push->args         = $args;
        $push->send_type    = $sendType;
        $push->send_time    = Time::getTime();
     
        $push->save();
        
        self::PushReadMessage($push->id, $type, $userType, $users == false ? "" : $users);
        
        return $result;
    }
    /**
     * 指定用户
     * */
    const DESIGNATED_USER = 1;
    /**
     * 所有用户
     * */
    const ALL_USER = 0;
    /**
     * 插入到阅读表
     * @param int $messageId 消息编号
     * @param string $type 卖家或者买家
     * @param int $userType 指定类型
     * @param string $users 用户 
     */
    public static function PushReadMessage($messageId, $type, $userType, $users)
    {
        if($users === null) return;
        
        self::replaceIn($users);
        
        $dbPrefix = DB::getTablePrefix();

        switch($type)
        {
            case "seller":
                $sql = "
INSERT INTO {$dbPrefix}read_message
(
    message_id,
    seller_id
)
SELECT {$messageId},
        id
    FROM {$dbPrefix}seller " . ($userType == self::DESIGNATED_USER && $users == true ? "WHERE user_id IN ({$users})" : "");
                break;
            
            case "buyer":
                $sql = "
INSERT INTO {$dbPrefix}read_message
(
    message_id,
    user_id
)
SELECT {$messageId},
        id
    FROM {$dbPrefix}user " . ($userType == self::DESIGNATED_USER && $users == true ? "WHERE id IN ({$users})" : "");
                break;
            
            case "staff":
                $sql = "
INSERT INTO {$dbPrefix}read_message
(
    message_id,
    staff_id
)
SELECT {$messageId},
        id
    FROM {$dbPrefix}seller_staff " . ($userType == self::DESIGNATED_USER && $users == true ? "WHERE user_id IN ({$users})" : "");
                break;
            
            default:
                return;
        }
        
        DB::unprepared($sql);
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
