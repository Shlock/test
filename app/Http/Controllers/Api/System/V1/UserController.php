<?php 
namespace YiZan\Http\Controllers\Api\System;

use YiZan\Services\System\UserService;
use YiZan\Models\UserVerifyCode;

/**
 * 会员
 */
class UserController extends BaseController {
    /**
     * 会员列表
     */
    public function lists() {
        $data = UserService::getLists(
                $this->request('mobile'), 
                $this->request('name'), 
                (int)$this->request('status'),
                (int)$this->request('userType'),
                max((int)$this->request('page'), 1),
                max((int)$this->request('pageSize'), 20),
                $this->request('recommendUserName','')        // 新增推荐注册用户查询
            );
        $this->outputData($data);
    }

    /**
     * 会员搜索
     */
    public function search() {
        $data = UserService::searchUser($this->request('name'));
        $this->outputData($data);
    }

    /**
     * 获取会员
     */
    public function get() {
        $data = UserService::getById((int)$this->request('id'));
        if (!$data) {
            $this->outputCode(20101);
        }
        $this->outputData($data->toArray());
    }

    /**
     * 更新会员
     */
    public function update() {
        list($lng, $lat) = explode(',', $this->request('extensionLatLng'));
        $result = UserService::updateUser(
                (int)$this->request('id'),
                $this->request('mobile'),
                $this->request('name'),
                $this->request('pwd'),
                $this->request('avatar'),
                (int)$this->request('status'),
                $this->request('isExtensionWorker'),
                $this->request('extensionAddress'),
                $lat,
                $lng,
                $this->request('extensionRange')
            );
        $this->output($result);
    }

    /**
     * 删除会员
     */
    public function delete() {
        $status = UserService::removeUser($this->request('id'));
        if (!$status) {
            $this->outputCode(20108);
        }
        $this->outputCode(0);
    }
    /**
     * 提款手机号码验证
     */
    public function verify() 
    {
        $result = UserService::sendVerifyCode($this->request('mobile'), UserVerifyCode::TYPE_WITHDRAW);
        $this->output($result);
    }
}