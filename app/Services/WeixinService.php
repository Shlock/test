<?php namespace YiZan\Services;

use YiZan\Models\Payment;
use YiZan\Models\User;
use YiZan\Utils\String;
use YiZan\Utils\Http;
use YiZan\Utils\Image;
use DB,Log;

class WeixinService extends BaseService {

    protected $wxPublic = null;

    public function wxPublic() {
        return SystemConfigService::getConfigByGroup('weixin');
    }
    /**
     * 获取access_token
     * @return string
     */
    public function getAccessToken($force = false) {
        $wxPublic =  self::wxPublic();
        if (UTC_TIME + 1800 < $wxPublic['access_token_expired'] && !$force) {
            $access_token = $wxPublic['access_token'];
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' .$wxPublic['app_id'] . '&secret=' .$wxPublic['app_secret'];
            $token_json = Http::get($url);
            $token_json = empty($token_json) ? false : @json_decode($token_json, true);
            if(!$token_json || empty($token_json['access_token'])){
                return $token_json;
            }
            $access_token = $token_json['access_token'];
            SystemConfigService::updateConfig('access_token', $access_token);
            SystemConfigService::updateConfig('access_token_expired', UTC_TIME + (int)$token_json['expires_in']);
        }
        return $access_token;
    }

    //微信Js配置
    public function getweixin($extend_url){

        $config = Payment::where('code', 'weixinJs')->where('status', 1)->first();
        $payment['appId'] = $config->config['appId'];
        $payment['appSecret'] = $config->config['appSecret'];

        $payment['appId'] = 'wx30efd8fea56037d8';
        $payment['appSecret'] = '8a15535218dd5a5ec486bb02cc8e4a0e';
        $config = $payment;

        $weixinConfig = SystemConfigService::getConfigByGroup('weixin');

//        if (UTC_TIME + 1800 < $weixinConfig['access_token_expired']) {
//            $access_token = $weixinConfig['access_token'];
//
//        } else {

            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' .
                $config['appId'] . '&secret=' .$config['appSecret'];
            $token_json = Http::get($url);
            $token_json = empty($token_json) ? false : @json_decode($token_json, true);
            if(!$token_json || empty($token_json['access_token'])){
                return false;
            }
            $access_token = $token_json['access_token'];
//            SystemConfigService::updateConfig('access_token', $access_token);
//            SystemConfigService::updateConfig('access_token_expired', UTC_TIME + (int)$token_json['expires_in']);
//        }

//        if (UTC_TIME + 1800 < $weixinConfig['jsapi_ticket_expired']) {
//            $jsapi_ticket = $weixinConfig['jsapi_ticket'];
//        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
            $jsapi_json = Http::get($url);
            $jsapi_json = empty($jsapi_json) ? false : @json_decode($jsapi_json, true);
            if(!$jsapi_json || empty($jsapi_json['ticket'])){
                return false;
            }
            $jsapi_ticket = $jsapi_json['ticket'];
//            SystemConfigService::updateConfig('jsapi_ticket', $jsapi_ticket);
//            SystemConfigService::updateConfig('jsapi_ticket_expired', UTC_TIME + (int)$jsapi_json['expires_in']);
//        }

        //js-sdk接口配置信息
        $jsapi_request = array(
            'jsapi_ticket' => $jsapi_ticket,
            'noncestr' => md5(String::randString(16)),
            'timestamp' => UTC_TIME,
            'url' => $extend_url
        );

        $jsapi_request['signature'] = self::weixinSign($jsapi_request, '', 'sha1');
        $jsapi_request['appId'] = $config['appId'];

        return $jsapi_request;
    }

    private static function weixinSign($args, $partnerKey = '', $type = 'md5') {
        $sign = '';
        foreach ($args as $key => $data) {
            $sign .= "{$key}={$data}&";
        }
        if (!empty($partnerKey)) {
            $sign .= "key={$partnerKey}";
        } else {
            $sign = substr($sign, 0, -1);
        }
        return $type($sign);
    }

    /**
     * 获取微信用户信息
     * @param  string $openid 用户openid
     * @return array
     */
    public function getUser($mobile,$nickname,$avatar) {
        $user = User::where('mobile',$mobile)->first(); //先用openId找 如果没找到

        if(empty($user)) {
            $crypt  = String::randString(6);
            $pwd    = md5(md5(String::randString(6,1)) . $crypt);

            $location = RegionService::getCityByIp(CLIENT_IP);
            $user = new User();
            $user['name']   = !empty($nickname) ? $nickname : substr($mobile,0,6).'****'.substr($mobile,-1,1);
            $user['name_match']   = String::strToUnicode($user['name']);
            $user['group_id']   = 1;
            $user['mobile']     = $mobile;
            $user['province_id']        = $location['province'];
            $user['city_id'] = $location['city'];
            $user['reg_ip']  = CLIENT_IP;
            $user['reg_province_id'] = $location['province'];
            $user['reg_city_id'] = $location['city'];

            $image_url = $avatar;

            $ch = curl_init();
            //设置选项，包括URL
            curl_setopt($ch, CURLOPT_URL, $image_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            //执行并获取HTML文档内容
            $content = curl_exec($ch);
            //释放curl句柄
            curl_close($ch);
            //打印获得的数据

            // $content = \YiZan\Utils\Http::get($image_url);
            $name = Image::getFormArgs();
            $path = $name['image_url'];
            $imageUrl = Image::upload($content, $path);
            $user['avatar']     = !empty($imageUrl) ? $imageUrl : '';

            //$user['avatar']     = !empty($avatar) ? $avatar : '';

            $user['reg_time']= UTC_TIME;
            $user['crypt']= $crypt;
            $user['pwd']= $pwd;
            $user->save();

            $user = User::where('id',$user->id)->first()->toArray();
        }
        return $user;
    }

}
