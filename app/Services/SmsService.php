<?php

namespace YiZan\Services;

use YiZan\Utils\Http;
use Config;

class SmsService extends BaseService {

    /**
     * 发送验证码
     * @param  string $code   发送数字
     * @param  string $mobile 手机号码
     * @return array          发送结果
     */
    public static function sendCode($code, $mobile) {
        $content = "{$code} （短信验证码，请勿泄露）";
        return self::httpSend($content, $mobile, Config::get('app.sms.user_name'), Config::get('app.sms.user_pwd'), 2);
    }

    /**
     * 发送文本
     * @param  string $content 文本内容
     * @param  string $mobile  手机号码
     * @return array           发送结果
     */
    public static function sendSms($content, $mobile) {
        return array('status' => 1); //self::httpSend($content, $mobile, Config::get('app.sms.user_name'), Config::get('app.sms.user_pwd'));
    }

    /**
     * 提交短信
     * @param  string $content 发送内容
     * @param  string $mobile  手机号码
     * @param  string $user    用户名
     * @param  string $pwd     密码
     * @return array           发送结果
     */
    private static function httpSend($content, $mobile, $user, $pwd, $is_adv = 0) {
        $data = array(
            'userid' => Config::get('app.sms.company_id'),
            'account' => $user,
            'password' => $pwd,
            'mobile' => $mobile,
            'content' => $content,
            'action' => 'send',
            'sendTime' => '',
            'extno' => '',
        );
        $post = Http::post(Config::get('app.sms.url'), $data);
        $xml = simplexml_load_string($post);
        $json = json_encode($xml);
        $result = json_decode($json, true);
        if ($result['returnstatus'] == 'Success') {
            return array('status' => 1);
        } else {
            return array('status' => 0);
        }
    }

    /**
     * 提交短信
     * @param  string $content 发送内容
     * @param  string $mobile  手机号码
     * @param  string $user    用户名
     * @param  string $pwd     密码
     * @return array           发送结果
     */
    private static function httpSendBak($content, $mobile, $user, $pwd, $is_adv = 0) {
        $data = array(
            'user_name' => $user,
            'password' => $pwd,
            'content' => $content,
            'mobile' => $mobile,
            'is_adv' => $is_adv
        );
        $params = json_encode($data);
        return json_decode(Http::post(Config::get('app.sms.url'), $params), true);
    }

}
