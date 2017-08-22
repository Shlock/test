<?php 
namespace YiZan\Http\Controllers\Api\System\Admin;

use YiZan\Services\UserService;
use YiZan\Services\UserAddressService;
use YiZan\Http\Controllers\Api\System\BaseController;
use Lang, Validator;

/**
 * 操作日志
 */
class LogController extends BaseController 
{
    /**
     * 操作日志列表
     */
    public function lists() {

        $this->outputData(["list"=>
            [[
                "id"=>1, 
                "admin"=>[
                    "id"=>1,
                    "role"=>["id"=>1, "name"=>"test", "status"=>true, "access"=>[]],
                    "name"=>"test",
                    "loginTime"=>time(),
                    "loginIp"=>"127.0.0.1",
                    "loginConut"=>10,
                    "createTime"=>time(),
                    "status"=>true
                ], 
                "api"=>"test", 
                "ip"=>"127.0.0.1",
                "status"=>true,
                "errorMsg"=>"test",
                "request"=>"test",
                "logTime"=>time()
            ]],
            "totalCount"=>20]);
    }

    /**
     * 删除日志
     */
    public function delete()
    {
        $this->output(["result"=>true]);
    }
    
    /**
     * 清除日志
     */
    public function clear()
    {
        $this->output(["result"=>true]);
    }
}