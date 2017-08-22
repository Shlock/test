<?php

namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\UserService;
use YiZan\Services\Buyer\DistrictService;
use YiZan\Services\Buyer\UserAddressService;
use YiZan\Services\UserMobileService;
use YiZan\Services\Buyer\ReadMessageService;
use YiZan\Services\Buyer\PropertyService;
use YiZan\Services\Buyer\PuserDoorService;
use YiZan\Services\Buyer\PropertyUserService;
use YiZan\Services\Buyer\DoorOpenLogService;
use YiZan\Services\RegionService;
use Lang,
    Validator,
    Log,
    Input;
use YiZan\Models\OnlineLog;
use Session;

class UserController extends BaseController {

    /**
     * 手机号码验证
     */
    public function mobileverify() {
        header("Access-Control-Allow-Origin: *");
        $chk = md5($this->request('mobile') % date('d'));
        $user = UserService::getByMobile($this->request('mobile'));
        if ($user) {
            $result = UserService::sendVerifyCode($this->request('mobile'), $this->request('type'));
            $this->output($result);
        } elseif ($chk == $this->request('isReg')) {
            $result = UserService::sendVerifyCode($this->request('mobile'), $this->request('type'));
            $this->output($result);
        } else {
            $this->output(array(
                'msg' => '请前往My菜公众号注册',
            ));
        }
    }

    public function mobileverify2() {
        header("Access-Control-Allow-Origin: *");
        $chk = md5($this->request('mobile') % date('d'));
        $user = UserService::getByMobile($this->request('mobile'));
        if ($user) {
            $result = UserService::sendVerifyCode($this->request('mobile'), $this->request('type'));
            $this->output($result);
        } elseif ($chk == $this->request('isReg')) {
            $result = UserService::sendVerifyCode(Input::get('mobile'), Input::get('type'));
            $this->output($result);
        } else {
            $this->output(array(
                'msg' => '请前往My菜公众号注册',
            ));
        }
    }

    /**
     * 会员登录
     */
    public function login() {
        if ($this->request('verifyCode') == true) {
            $this->verifylogin();
            return;
        }
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.user_login')
        );

        $rules = array(
            'mobile' => ['required', 'regex:/^1[0-9]{10}$/'],
            'pwd' => ['required', 'min:6', 'max:20']
        );

        $messages = array(
            'mobile.required' => '10101',
            'mobile.regex' => '10102',
            'pwd.required' => '10105',
            'pwd.min' => '10106',
            'pwd.max' => '10106',
        );

        $mobile = $this->request('mobile');
        $pwd = strval($this->request('pwd'));

        $validator = Validator::make([
                    'mobile' => $mobile,
                    'pwd' => $pwd
                        ], $rules, $messages);

        //验证信息
        if ($validator->fails()) {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            $this->output($result);
        }

        $user = UserService::getByMobile($mobile);

        //未找到会员时
        if (!$user) {
            $result['code'] = 10108;
            $this->output($result);
        }

        $pwd = md5(md5($pwd) . $user->crypt);

        //登录密码错误
        if ($user->pwd != $pwd) {
            $result['code'] = 10109;
            $this->output($result);
        }
        //状态锁定
        if ($user->status != 1) {
            $result['code'] = 10122;
            $this->output($result);
        }
        //更新登陆时间IP信息
        UserService::updateIp($user->id);
        $this->createToken($user->id, $user->pwd);
        $propertyUser = PropertyUserService::getByUserId($user->id);
        $user = [
            "id" => $user->id,
            "mobile" => $user->mobile,
            "name" => $user->name,
            "avatar" => $user->avatar,
            "address" => UserAddressService::getAddressList($user->id),
            "defaultMobile" => UserMobileService::getMobile($user->id),
            "propertyUser" => $propertyUser,
            "balance" => $user->balance,
            "totalMoney" => $user->total_money,
        ];

        $result['data'] = $user;
        $result['token'] = $this->token;
        $result['userId'] = $user['id'];


        // 记录用户登录的相关的信息
        $onlineLog = new OnlineLog();
        $onlineLog->uid = $user['id'];
        list($lat, $lng) = explode(',', $this->request('mapPoint', '0,0'));
        $onlineLog->lat = $lat;
        $onlineLog->lng = $lng;
        $onlineLog->device_no = $this->request('device_no', 'weixin');
        $onlineLog->device_type = $this->request('device_type', 'web');
        $onlineLog->os_version = $this->request('os_version', '-');
        $onlineLog->ip = CLIENT_IP;
        $onlineLog->time = date('Y-m-d H:i:s');
        $onlineLog->log_type = 'login';
        $onlineLog->save();

        $this->output($result);
    }

    public function ping() {

        $onlineLog = new OnlineLog();
        $onlineLog->uid = $this->request('userId', 0);
        list($lat, $lng) = explode(',', $this->request('mapPoint', '0,0'));
        $onlineLog->lat = $lat;
        $onlineLog->lng = $lng;
        $onlineLog->device_no = $this->request('device_no', '');
        $onlineLog->device_type = $this->request('device_type', '');
        $onlineLog->os_version = $this->request('os_version', '');
        $onlineLog->ip = CLIENT_IP;
        $onlineLog->time = date('Y-m-d H:i:s');
        $onlineLog->log_type = 'ping';
        $onlineLog->save();
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => 'ping success'
        );
        $this->output($result);
    }

    /**
     * 会员登录
     */
    public function verifylogin() {
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.user_login')
        );

        $rules = array(
            'mobile' => ['required', 'regex:/^1[0-9]{10}$/']
        );

        $messages = array(
            'mobile.required' => '10101',
            'mobile.regex' => '10102'
        );

        $mobile = $this->request('mobile');

        $verifyCode = $this->request('verifyCode');

        $validator = Validator::make([
                    'mobile' => $mobile
                        ], $rules, $messages);

        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            $this->output($result);
        }

        if (UserService::checkVerifyCode($verifyCode, $mobile) == false) {
            $result['code'] = 10104; // 验证码不正确

            $this->output($result);
        }

        $user = UserService::getByMobile($mobile);

        //未找到会员时
        if (!$user) {
            $result['code'] = 10108;
            $this->output($result);
        }
        //状态锁定
        if ($user->status != 1) {
            $result['code'] = 10122;
            $this->output($result);
        }
        UserService::updateIp($user->id);
        $this->createToken($user->id, $user->pwd);

        $user = [
            "id" => $user->id,
            "mobile" => $user->mobile,
            "name" => $user->name,
            "avatar" => $user->avatar,
            "address" => UserAddressService::getAddressList($user->id),
            "defaultMobile" => UserMobileService::getMobile($user->id),
        ];

        $result['data'] = $user;
        $result['token'] = $this->token;
        $result['userId'] = $user['id'];
        $this->output($result);
    }

    /**
     * 会员退出
     */
    public function logout() {
        $this->outputCode(0, Lang::get('api.success.user_logout'));
    }

    /**
     * 会员注册
     */
    public function reg() {
        $result = UserService::createUser(
                        $this->request('mobile'), $this->request('verifyCode'), $this->request('pwd'), $this->request('type', 'reg'), $this->request('sharedByUserId', 0), $this->request('regLat'), $this->request('regLng'), $this->request('regAddress')
        );
        if ($result['code'] == 0) {
            $user = $result['data'];
            $this->createToken($user->id, $user->pwd);
            $user = $user->toArray();
            $user['address'] = []; //UserAddressService::getAddress($user['id']);
            $result['data'] = $user;
            $result['token'] = $this->token;
            $result['userId'] = $user['id'];
            $this->output($result);
        }
        $this->output($result);
    }

    /**
     * 修改密码
     */
    public function repwd() {
        $result = UserService::createUser(
                        $this->request('mobile'), $this->request('verifyCode'), $this->request('pwd'), $this->request('type', 'repwd')
        );
        if ($result['code'] == 0) {
            $user = $result['data'];
            $this->createToken($user->id, $user->pwd);
            $user = $user->toArray();
            $user['address'] = UserAddressService::getAddress($user['id']);
            $result['data'] = $user;
            $result['token'] = $this->token;
            $result['userId'] = $user['id'];
            $this->output($result);
        }
        $this->output($result);
    }

    /**
     * 修改密码(新)
     */
    public function renewpwd() {
        $result = UserService::repwd(
                        $this->userId, $this->request('oldPwd'), $this->request('pwd')
        );
        if ($result['code'] == 0) {
            $user = $result['data'];
            $this->createToken($user->id, $user->pwd);
            $user = $user->toArray();
            $user['address'] = UserAddressService::getAddress($user['id']);
            $result['data'] = $user;
            $result['token'] = $this->token;
            $result['userId'] = $user['id'];
            $this->output($result);
        }
        $this->output($result);
    }

    /**
     * 更新手机号
     */
    public function updatemobile() {

        $result = UserService::updateMobile(
                        $this->userId, $this->request('oldMobile'), $this->request('mobile'), $this->request('verifyCode')
        );
        $this->output($result);
    }

    /**
     * 卖家状态
     */
    public function msgStatus() {
        $reslut = ReadMessageService::hasNewMessage($this->userId);

        $info = array(
            'code' => 0,
            'data' => ["hasNewMessage" => $reslut],
            'msg' => ""
        );

        $this->output($info);
    }

    /*
     * 小区身份认证
     */

    public function villagesauth() {
        $result = PropertyUserService::auth(
                        $this->userId, $this->request('villagesid'), $this->request('buildingid'), $this->request('roomid'), $this->request('username'), $this->request('usertel')
        );
        $this->output($result);
    }

    /**
     * 获取开门钥匙信息
     */
    public function getdoorkeys() {
        $districtId = (int) $this->request('villagesid');
        $user = $this->user->toArray();
        if ($districtId == 0) {
            $propertyUser = $user['propertyUser'][0];
        } else {
            foreach ($user['propertyUser'] as $userInfo) {
                if ($userInfo['districtId'] == $districtId) {
                    $propertyUser = $userInfo;
                    break;
                }
            }
        }
        $result = PuserDoorService::getUserDoors(
                        $propertyUser['id']
        );
        $this->outputData($result);
    }

    /**
     * 修改门禁信息
     */
    public function editdoorinfo() {
        $result = PuserDoorService::updateUserDoor(
                        $this->user['propertyUser']->id, $this->request('doorid'), $this->request('doorname')
        );
        $this->outputData($result);
    }

    /**
     * 小区身份认证检查
     */
    public function checkvillagesauth() {
        $result = PropertyUserService::getByUserId(
                        $this->userId
        );
        if (empty($result)) {
            $msg = '您还未申请身份认证';
        } else {
            if ($result['status'] == 0) {
                $msg = '身份认证审核中';
            } else if ($result['status'] == 1) {
                $msg = '身份认证成功';
            } else {
                $msg = '身份认证失败，请完善信息后再试';
            }
        }
        $this->outputData($result, $msg);
    }

    /**
     * 小区门禁申请
     */
    public function applyaccess() {
        $result = PropertyUserService::applyDoorAccess(
                        $this->userId, $this->request('districtId')
        );
        $this->output($result);
    }

    /**
     * 记录开门日志
     */
    public function opendoor() {
        $result = DoorOpenLogService::doorOpenRecord(
                        $this->user['propertyUser']->id, $this->request('errorCode'), $this->request('districtId'), $this->request('doorId'), $this->request('buildId'), $this->request('roomId')
        );
        $this->output($result);
    }

    public function getbalance() {
        $result['paylogs'] = UserService::getBalance(
                        $this->userId, max((int) $this->request('page'), 1)
        );
        $this->outputData($result);
    }

    public function balance() {
        $result = UserService::balance($this->userId);
        $this->outputData($result);
    }

    public function charge() {
        $result = UserService::payCharge(
                        $this->userId, $this->request('payment'), $this->request('extend'), $this->request('money')
        );
        $this->output($result);
    }

    /**
     * 检测用户是否注册
     */
    public function checkuser() {
        $result = UserService::checkUser(
                        $this->request('mobile'), $this->request('nickname'), $this->request('avatar')
        );

        if ($result['code'] == 0) {
            $user = $result['data'];
            $this->createToken($user['id'], $user['pwd']);
            $user['address'] = []; //UserAddressService::getAddress($user['id']);
            $result['data'] = $user;
            $result['token'] = $this->token;
            $result['userId'] = $user['id'];
            $this->outputData($result);
        }
        $this->outputData($result);
    }

}
