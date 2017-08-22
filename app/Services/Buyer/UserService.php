<?php namespace YiZan\Services\Buyer;

use YiZan\Models\User;
use YiZan\Models\UserPayLog;
use YiZan\Services\RegionService;
use YiZan\Services\PaymentService;
use Lang, DB, Time;

class UserService extends \YiZan\Services\UserService {

    /**
     * 检测验证码
     * @param $mobile 手机号码
     * @param $verifyCode 验证码
     */
    public static function verifymobile($mobile, $verifyCode) {
        $result = [
            'code'	=> 0,
            'data' => true,
            'msg' => Lang::get('api.success.verify_success')
        ];
        if(self::checkVerifyCode($verifyCode, $mobile) == false)
        {
            $result['code'] = 10104;
            $result['data'] = false;
        }
        return $result;
    }

    public static function updateIp($userId) {
        $location = RegionService::getCityByIp(CLIENT_IP);
        $user = User::find($userId);
        $user->login_time         = UTC_TIME;
        $user->login_province_id  = $location['province'];
        $user->login_city_id      = $location['city'];
        $user->login_ip           = CLIENT_IP;

        $user->save();
    }



    /**
     * 充值
     * @param  [type] $userId  [description]
     * @param  [type] $payment [description]
     * @return [type]          [description]
     */
    public static function payCharge($userId, $payment, $extend = [], $money) {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.user_pay_order')
        );
        $user = User::find($userId);
        if (!$user) {
            $result['code'] = 99996;
            return $result;
        }
        
        $payLog = PaymentService::createPayLog($money, $payment, $extend, $userId);
        
        if (is_numeric($payLog)) 
        {
            $result['code'] = abs($payLog);
            
            return $result;
        }
        
        $result['data'] = $payLog;

        return $result;
    }

    public static function getBalance($userId, $page, $pageSize = 20){
        $paylogs = UserPayLog::where('user_id', $userId) 
                             ->where('status', 1)
                             ->skip(($page - 1) * $pageSize)
                             ->take($pageSize)
                             ->orderBy('id', 'DESC')
                             ->get()
                             ->toArray();
        foreach ($paylogs as $key => $value) {
            $paylogs[$key]['createTime'] = Time::toDate($value['createTime'], 'Y-m-d H:i:s');
            $paylogs[$key]['payTypeStr'] = Lang::get('wap.pay_type.'.$value['payType']);
        }
        return $paylogs;
    }

    public static function balance($userId){
        $balance = User::where('id', $userId)
                       ->pluck('balance');
        return ['balance'=>number_format($balance, 2)];
    }

}
