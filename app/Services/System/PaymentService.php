<?php 
namespace YiZan\Services\System;

use YiZan\Models\Payment;
use YiZan\Models\UserPayLog;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use YiZan\Utils\Http;
use DB, Config, Request;

/**
 * 支付方式
 */
class PaymentService extends \YiZan\Services\PaymentService 
{
    /**
     * 支付方式列表
     * @return array   支付方式列表
     */
    public static function getList() {
        return Payment::all();
    }

	/**
     * 更新配置信息
     * @param string $code 支付键名
     * @param array $configs 配置信息
     * @param int $status 状态
     * @return array   修改结果
	 */
	public static function update($code, $config, $status) {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
        );
        
        if(Payment::where("code", $code)->first() == false)
        {
            $result["code"] = 40701; // 支付方式不存在
            
            return $result;
        }
        Payment::where("code", $code)->update(["config"=>json_encode($config)]);
        //Payment::where("code", $code)->update(["status" => $status, "config"=>json_encode($config)]);
        
        return $result;
    }
    /**
     * 更新状态
     * @param string $code 支付键名
     * @param int $status 状态
     * @return array   修改结果
     */
    public static function updatePaymentStatus($code, $status) {
        $result = array (
        	'status'	=> true,
			'code'	    => self::SUCCESS,
			'data'	    => $status,
			'msg'	    => null
		);
        
        if(Payment::where("code", $code)->first() == false)
        {
            $result["code"] = 40701; // 支付方式不存在
    
            return $result;
        }
        Payment::where("code", $code)->update(["status"=>$status]);
        return $result;
    }
}
