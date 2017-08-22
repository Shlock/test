<?php 
namespace YiZan\Http\Controllers\Staff;
use Redirect;

/**
 * 微信控制器
 */
class WeixinController extends BaseController {
	public function authorize(){
        $url = urldecode($_REQUEST['url']);
        if (empty($url)) {
        	return $this->error('参数错误');
        }

        $result = $this->requestApi('config.getpayment',['code' => 'weixinJs']);
        if (!$result || $result['code'] != 0) {
        	return $this->error('获取微信配置信息失败', $url);
        }

        $payment = $result['data'];
        $config = $payment['config'];

        Session::set('authorize_return_url', $url);
        Session::save();

        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$config['appId'].
                '&redirect_uri='.urlencode(u('Weixin/accesstoken'))
                .'&response_type=code&scope=snsapi_base&state=YZ#wechat_redirect';
        return Redirect::to($url);
    }

    public function accesstoken() {
        $code = $_REQUEST['code'];
        
        $url = Session::get('authorize_return_url');
        if (empty($code)) {
            return $this->error('授权失败', $url);
        }

        $result = $this->requestApi('config.getpayment',['code' => 'weixinJs']);
        if (!$result || $result['code'] != 0) {
        	return $this->error('获取微信配置信息失败', $url);
        }

        $payment = $result['data'];
        $config = $payment['config'];

        $wxurl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$config['appId'].
            '&secret='.$config['appSecret'].'&code='.$code.'&grant_type=authorization_code';
        $result = @file_get_contents($wxurl);
        $result = empty($result) ? false : @json_decode($result, true);
        if (!$result) {
            return $this->error('授权失败', $url);
        } elseif (isset($result['errcode']) && $result['errcode'] != 0) {
        	return $this->error('授权失败:'.$result['errmsg'], $url);
        }
        $openid = $result['openid'];
        $url = $url.'&openId='.$openid;
        return Redirect::to($url);
    }
}