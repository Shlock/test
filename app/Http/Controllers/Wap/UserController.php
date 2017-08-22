<?php

namespace YiZan\Http\Controllers\Wap;

use YiZan\Utils\ImgVerify;
use View,
    Route,
    Input,
    Lang,
    Redirect,
    Response,
    Request,
    Time;

/**
 * 用户登录注册控制器
 */
class UserController extends BaseController {

    public function __construct() {
        parent::__construct();
        if ($this->userId > 0 && ACTION_NAME != 'app') {
            header('Location:' . u('UserCenter/index'));
            exit;
        }
    }

    /**
     * 用户中心首页
     */
    public function index() {
        View::share('user', $this->user);
        $result = $this->requestApi('seller.check', ['id' => $this->userId]);
        View::share('seller', $result['data']);
        View::share('title', "- 用户中心");
        return $this->display();
    }

    /**
     * 用户登录
     */
    public function login() {
        $args = Input::all();
        if (!isset($args['quicklogin'])) {
            $args['quicklogin'] = 2;
        }
        $return_url = Session::get('return_url');
        View::share('return_url', !empty($return_url) ? $return_url : u('UserCenter/index'));
        if ($this->tpl != 'wap.run') {
            View::share('is_show_top', false);
        }
        View::share('quicklogin', $args['quicklogin']);
        View::share('top_title', '登录');
        return $this->display();
    }

    /**
     * 执行登录
     */
    public function dologin() {
        $data = Input::all();
        $result = $this->requestApi('user.login', $data);
        if ($result['code'] == 0) {
            $this->setUser($result['data']);
            $this->setSecurityToken($result['token']);
            Session::set('return_url', '');
            Session::save();
        }
        return Response::json($result);
    }

    /**
     * [reg 用户注册]
     */
    public function reg() {
        $return_url = Session::get('return_url');
        View::share('return_url', !empty($return_url) ? $return_url : u('UserCenter/index'));
        return $this->display();
    }

    public function regshare() {
        $return_url = Session::get('return_url');
        View::share('return_url', !empty($return_url) ? $return_url : u('UserCenter/index'));
        return $this->display();
    }

    public function share() {
        $userId = Input::get('sharedByUserId');
        return view('wap.community.user.share', ['uid' => $userId]);
    }

    /**
     * [doreg 执行注册]
     */
    public function doreg() {
        $data = Input::all();
        $data['type'] = 'reg';
        $result = $this->requestApi('user.reg', $data);
        if ($result['code'] == 0) {
            $this->setUser($result['data']);
            $this->setSecurityToken($result['token']);
            Session::set('return_url', '');
            Session::save();
        }
        return Response::json($result);
    }

    /**
     * [reg 修改密码]
     */
    public function repwd() {
        return $this->display();
    }

    /**
     * [doreg 执行修改密码]
     */
    public function dorepwd() {
        $data = Input::all();
        $data['type'] = 'repwd';
        $result = $this->requestApi('user.repwd', $data);
        if ($result['code'] == 0) {
            $this->setUser($result['data']);
            $this->setSecurityToken($result['token']);
        }
        return Response::json($result);
    }

    /**
     * 生成验证码
     */
    public function verify() {
        if ($this->checkVerify()) {
            $mobile = Input::get('mobile');
            $type = Input::get('type') ? Input::get('type') : 'reg';
            $result = $this->requestApi('user.mobileverify', array('mobile' => $mobile, 'type' => $type),array('isReg'=>md5($mobile%date('d'))));
            return Response::json($result);
        } else {
            return Response::json(array(
                'code' => 9001,
                'msg' => '图片验证码不正确'
            ));
        }
    }

    /**
     * 生成验证码
     */
    public function voiceverify() {
        $mobile = Input::get('mobile');
        $result = $this->requestApi('user.mobileverify', array('mobile' => $mobile, "voice" => true));
        return Response::json($result);
    }

    public function app() {
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $iphone = (strpos($agent, 'iphone')) ? true : false;
        $ipad = (strpos($agent, 'ipad')) ? true : false;
        $android = (strpos($agent, 'android')) ? true : false;
        $config = $this->getConfig();
        if ($iphone || $ipad) {
            if (strpos($agent, 'micromessenger')) {
                return $this->display();
            } else {
                Redirect::to($config['buyer_app_down_url'])->send();
            }
        }
        if ($android) {
            Redirect::to($config['buyer_android_app_down_url'])->send();
        }
    }

    /**
     * 生成图形验证码
     */
    public function imgverify() {
        $this->createVerify();
        $imgVerify = new ImgVerify();
        $imgVerify->doimg();
        $code = $imgVerify->getCode();
        Session::set('imgVerify', $code);
        Session::save();
        exit;
    }

    public function createVerify() {
        Session::set("user_reg", md5(Request::getClientIp() . $_SERVER['HTTP_USER_AGENT']));
        Session::set("user_reg_time", UTC_TIME);
        Session::save();
    }

    private function checkVerify() {
        if (!Request::ajax()) {
            return false;
        }

        $imgVerify = Session::get('imgVerify');
        if (strtolower($imgVerify) !== strtolower(Input::get('imgverify'))) {
            return false;
        }
        Session::set('imgverify', "now");
        Session::save();
        return true;
    }

}
