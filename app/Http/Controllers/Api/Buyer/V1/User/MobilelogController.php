<?php
namespace YiZan\Http\Controllers\Api\Buyer\User;
use Illuminate\Support\Facades\Log;
use YiZan\Http\Controllers\Api\Buyer\UserAuthController;
use YiZan\Services\UserMobileLogService;

/**
 * 移动端用户活动日志
 */
class MobilelogController extends UserAuthController {
    /**
     * 心跳活动日志
     * api: /buyer/v1/user.mobilelog.heartbeat
     */
    public function heartbeat() {
        $result = UserMobileLogService::record(
            $this->userId,
            $this->request('lat'),
            $this->request('lng'),
            $this->request('device_no'),
            $this->request('device_type'),
            $this->request('os_version'),
            $this->request('ip'),
            $this->request('time'),
            'heartbeat'
        );
        $this->output($result);
    }

    /**
     * 登录活动日志
     * api: /buyer/v1/user.mobilelog.login
     */
    public function login() {
        $result = UserMobileLogService::record(
            $this->userId,
            $this->request('lat'),
            $this->request('lng'),
            $this->request('device_no'),
            $this->request('device_type'),
            $this->request('os_version'),
            $this->request('ip'),
            $this->request('time'),
            'login'
        );
        $this->output($result);
    }


}
