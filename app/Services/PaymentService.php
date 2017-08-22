<?php namespace YiZan\Services;

use YiZan\Models\Payment;
use YiZan\Models\UserPayLog;
use YiZan\Models\SellerMoneyLog;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use YiZan\Utils\Http;
use YiZan\Models\SellerPayLog;
use YiZan\Models\User;
use YiZan\Services\OrderService;
use DB,
    Config,
    Request,
    YiZan\Utils\Time,
    YiZan\Models\Refund,
    YiZan\Models\Order,
    YiZan\Models\UserRefundLog;

class PaymentService extends BaseService {
    /**
     * 获取支持的支付方式
     * @return array 支付方式数组
     */
    public static function getPayments($type, $isConfig = false) {
        $list = Payment::where('status', 1)->where('type', 'like', '%,'.$type.',%')
                       ->orderBy('sort')
                       ->get()
                       ->toArray();
        foreach ($list as $key => $item) {
            $item[$item['code'].'Config'] = $item['config'];
            unset($item['config']);
            $list[$key] = $item;
        }
        return $list;
    }

    /**
     * 获取客户端的支付方式
     * @return array 支付方式数组
     */
    public static function getPaymentTypes() {
        $payments = [];
        $list = Payment::where('status', 1)->get();
        foreach ($list as $key => $item) {
            $types = explode(',', $item->type);
            $item = $item->toArray();
            unset($item['config']);
            foreach ($types as $type) {
                if (empty($type)) {
                    continue;
                }
                $payments[$type][$item['code']] = $item;
            }
        }
        return $payments;
    }

    /**
     * 获取支持的支付方式
     * @return array 支付方式数组
     */
    public static function getPayment($code) {
        $payment = Payment::where('status', 1)->where('code', $code)->first();
        if ($payment) {
            $payment = $payment->toArray();
        }
        return $payment;
    }

    public static function createPayLog($order, $payment, $extend = [], $userId = 0) {
        // print_r($order);die;
        DB::beginTransaction();
        try {
            $goodsName = $order->goods_name;
            unset($order->goods_name);
            $userPayLog = new UserPayLog;
            $user = User::find($order->user_id);
            $isFullBalancePay = 0; //是否全额余额支付
            if ($payment != 'balancePay') {
                $payment = Payment::where('code', $payment)->where('status', 1)->first();
                if (!$payment) {
                    return -60019;
                }
                $userPayLog->payment_type   = $payment->code;
                //如果使用混合支付则扣除余额 修改$order->pay_money 修改要支付的金额 生成余额支付日志
                if($extend['balancePay'] && $user->balance > 0){
                    if($user->balance >= $order->pay_fee ){
                        $userPayLog->payment_type   = 'balancePay'; 
                        $isFullBalancePay = 1;
                    } else {
                        $balancePayLog = new UserPayLog;
                        $balancePayLog->payment_type   = 'balancePay';
                        $balancePayLog->pay_type       = 1;//表示消费
                        $balancePayLog->user_id        = $order->user_id;
                        $balancePayLog->order_id       = $order->id;
                        $balancePayLog->activity_id    = $order->activity_id > 0 ? $order->activity_id : 0;
                        $balancePayLog->seller_id      = $order->seller_id;
                        $balancePayLog->money          = $user->balance;
                        $balancePayLog->balance        = 0;
                        $balancePayLog->content        = '余额支付';
                        $balancePayLog->create_time    = UTC_TIME;
                        $balancePayLog->create_day     = UTC_DAY;
                        $balancePayLog->status         = 1;
                        $balancePayLog->sn = Helper::getSn();
                        $balancePayLog->save();
                        //扣除会员余额
                        $balance = $user->balance;
                        $user->balance = 0;
                        $user->save();
                        //修改订单已支付金额 
                        $order->pay_money = $balance;
                        $order->save();
                    }
                } 
                $order->pay_fee = $order->pay_fee - $order->pay_money;   
            } else {
                $userPayLog->payment_type   = $payment; 
            } 
            if ($userId > 0) {  //会员充值
                $userPayLog->user_id        = $userId;
                $userPayLog->order_id       = 0;
                $userPayLog->seller_id      = 0;
                $userPayLog->activity_id    = 0;
                $userPayLog->money          = (float)$order; 
                $userPayLog->content        = '会员充值';
                $userPayLog->pay_type       = 2;  //充值类型
            } else { //支付
                $userPayLog->user_id        = $order->user_id;
                $userPayLog->order_id       = $order->id;
                $userPayLog->activity_id    = $order->activity_id > 0 ? $order->activity_id : 0;
                $userPayLog->seller_id      = $order->seller_id;
                $userPayLog->money          = $order->pay_fee;
                $userPayLog->balance        = $user->balance;
                $userPayLog->content        = $goodsName;
                $userPayLog->pay_type       = 1;  //消费类型
            }
            $userPayLog->create_time    = UTC_TIME;
            $userPayLog->create_day     = UTC_DAY;
            $userPayLog->status         = 0;
            $userPayLog->sn = Helper::getSn();
            $userPayLog->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return -60319;
        }
        $userPayLog->order = $order;
        if ($payment != 'balancePay' && !$isFullBalancePay) {
            $payment_type = $payment->code.'Pay';
            $pay_request = self::$payment_type($userPayLog, $payment, $extend);
            //return $pay_request;
            if (!$pay_request) {
                return -60023;
            }
        }
        $userPayLog['id'] = $userPayLog->id;
        $userPayLog = $userPayLog->toArray();
        $userPayLog['payRequest'] = $pay_request;

        if ($payment == 'balancePay' || $isFullBalancePay) {
            $balancePayResult = OrderService::balanceOrder($order, $userPayLog); 
            if($balancePayResult['code'] > 0){
                return -$balancePayResult['code'];
            }
        } 
        return $userPayLog;
    }

    /**
     * 创建商家充值
     * @param int       $sellerId   商家编号
     * @param int       $money      充值金额
     * @param string    $payment    充值方式
     */
    public static function createSellerPayLog($sellerId, $money, $payment){
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];

        $payment = Payment::where('code', $payment)->where('status', 1)->first();

        if (!$payment) {
            $result['code'] = 50707;
            return $result;
        }

        try {
            $sellerPayLog = new SellerPayLog();
            $sellerPayLog->sn = Helper::getSn();
            $sellerPayLog->seller_id = $sellerId;
            $sellerPayLog->money = $money;
            $sellerPayLog->payment_type = $payment->code;
            $sellerPayLog->content = '商家充值';
            $sellerPayLog->create_time = UTC_TIME;
            $sellerPayLog->create_day = UTC_DAY;
            $sellerPayLog->status = 0;
            $sellerPayLog->save();
            $payment_type = $payment->code.'Pay';
            $pay_request = self::$payment_type($sellerPayLog, $payment, null, true);
            if (!$pay_request) {
                $result['code'] = 60023;
                return $result;
            }
            $sellerPayLog = $sellerPayLog->toArray();
            $sellerPayLog['payRequest'] = $pay_request;
            $result['data'] = $sellerPayLog;
        } catch (Exception $e) {
            $result['code'] = 50706;
            return $result;
        }
        return $result;
    }

    /**
     * Summary of createRefundLog
     * @param \stdClass $refund 退款单子
     * @param array $extend 扩展信息
     */
    public static function createRefundLog($refund, $extend = [])
    {
        $payment = Payment::where('code', $refund->payment_type)->where('status', 1)->first();

        if (!$payment)
        {
            return -60019;
        }

        $userRefundLog = new UserRefundLog();
        $userRefundLog->user_id         = $refund->user_id;
        $userRefundLog->refund_id       = $refund->id;
        $userRefundLog->seller_id       = $refund->seller_id;
        $userRefundLog->payment_type    = $refund->payment_type;
        $userRefundLog->money           = $refund->money;
        $userRefundLog->trade_no        = $refund->trade_no;
        $userRefundLog->content         = $refund->content;
        $userRefundLog->create_time     = UTC_TIME;
        $userRefundLog->create_day      = UTC_DAY;
        $userRefundLog->status          = 0;

        do
        {
            try
            {
                $userRefundLog->sn = Helper::getSn();
                $userRefundLog->save();
                $bln = true;
            }
            catch (Exception $e)
            {
                $bln = false;
            }
        }
        while(!$bln);

        if(strripos($payment->code, "weixin") === 0)
        {
            $pay_request = self::weixinRefund($userRefundLog, $payment, $extend);
        }
        else if(strripos($payment->code, "alipay") === 0)
        {
            $pay_request = self::alipayRefund($userRefundLog, $payment, $extend);
        }
        else if(strripos($payment->code, "union") === 0)
        {
            $pay_request = self::unionRefund($userRefundLog, $payment, $extend);
        }

        if (!$pay_request)
        {
            return -60028;
        }

        $userRefundLog = $userRefundLog->toArray();

        $userRefundLog['payRequest'] = $pay_request;

        return $userRefundLog;
    }

    /**
     * 创建订单支付参数（会员订单，商家订单）
     * @param array $userPayLog     支付日志
     * @param array $payment        支付方式
     * @param array $extend         扩展
     * @param int   $isSeller       是否是商家
     */
    private static function alipayPay($userPayLog, &$payment, $extend = [], $isSeller) {
        //服务器异步通知页面路径
        if($isSeller){
            $notify_url = Config::get('app.callback_url').'payment/alipay/appnotifys';
        } else {
            $notify_url = Config::get('app.callback_url').'payment/alipay/appnotify';
        }
        $config = $payment->config;
        $parameters = [
            'service' => 'mobile.securitypay.pay',
            'partner' => $config['partnerId'],
            '_input_charset' => 'utf-8',
            'notify_url' => $notify_url,
            'out_trade_no' => $userPayLog->sn,
            'subject' => str_replace('&', '', $userPayLog->content),
            'payment_type' => '1',
            'seller_id' => $config['sellerId'],
            'total_fee' => $userPayLog->money,
            'body' => str_replace('&', '', $userPayLog->content)
        ];

        ksort($parameters);
        reset($parameters);

        $packages = '';
        foreach ($parameters as $key => $val) {
            $packages .= '&'.$key.'="'.$val.'"';
        }
        $packages = substr($packages, 1);

        require_once base_path().'/vendor/alipay/alipay_rsa.function.php';
        $sign = urlencode(rsaSign($packages, $config['partnerPrivKey'], false));
        $pay_request = [];
        $pay_request['packages'] = $packages.'&sign_type="RSA"&sign="'.$sign.'"';
        return $pay_request;
    }

    /**
     * 创建订单支付参数（会员订单，商家订单）
     * @param array $userPayLog     支付日志
     * @param array $payment        支付方式
     * @param array $extend         扩展
     * @param int   $isSeller       是否是商家
     */
    private static function unionpayPay($userPayLog, &$payment, $extend = []) {
        //服务器异步通知页面路径
        $notify_url = Config::get('app.callback_url').'payment/unionpay/notify';
        $config = $payment->config;
        require_once base_path().'/vendor/unionpay/utf8/func/common.php';
        $params = array(

            //以下信息非特殊情况不需要改动
            'version' => '5.0.0',                 //版本号
            'encoding' => 'utf-8',                //编码方式
            'certId' => getSignCertId(),          //证书ID
            'txnType' => '01',                    //交易类型
            'txnSubType' => '01',                 //交易子类
            'bizType' => '000201',                //业务类型
            'frontUrl' =>  $notify_url,           //前台通知地址
            'backUrl' => $notify_url,             //后台通知地址
            'signMethod' => '01',                 //签名方法
            'channelType' => '08',                //渠道类型，07-PC，08-手机
            'accessType' => '0',                  //接入类型
            'currencyCode' => '156',              //交易币种，境内商户固定156

            //TODO 以下信息需要填写
            'merId' => $config['merId'],        //商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'orderId' => $userPayLog->sn,   //商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime' => Time::toDate(UTC_TIME, 'YmdHis'),  //订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
            'txnAmt' => $userPayLog->money*100, //交易金额，单位分，此处默认取demo演示页面传递的参数
            'reqReserved' => $payment->code,        //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据
        );
        sign ($params);

        $uri = SDK_FRONT_TRANS_URL;
        $html_form = create_html ( $params, $uri );
        return ['html' => $html_form];
    }

    /**
     * 创建订单支付参数（会员订单，商家订单）
     * @param array $userPayLog     支付日志
     * @param array $payment        支付方式
     * @param array $extend         扩展
     * @param int   $isSeller       是否是商家
     */
    private static function unionappPay($userPayLog, &$payment, $extend = []) {
        //服务器异步通知页面路径
        $notify_url = Config::get('app.callback_url').'payment/unionpay/notify';
        $config = $payment->config;
        require_once base_path().'/vendor/unionpay/utf8/func/common.php';
        $params = array(

            //以下信息非特殊情况不需要改动
            'version' => '5.0.0',                 //版本号
            'encoding' => 'utf-8',                //编码方式
            'certId' => getSignCertId(),          //证书ID
            'txnType' => '01',                    //交易类型
            'txnSubType' => '01',                 //交易子类
            'bizType' => '000201',                //业务类型
            'frontUrl' =>  $notify_url,           //前台通知地址
            'backUrl' => $notify_url,             //后台通知地址
            'signMethod' => '01',                 //签名方法
            'channelType' => '08',                //渠道类型，07-PC，08-手机
            'accessType' => '0',                  //接入类型
            'currencyCode' => '156',              //交易币种，境内商户固定156

            //TODO 以下信息需要填写
            'merId' => $config['merId'],        //商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'orderId' => $userPayLog->sn,   //商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime' => Time::toDate(UTC_TIME, 'YmdHis'),  //订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
            'txnAmt' => $userPayLog->money*100, //交易金额，单位分，此处默认取demo演示页面传递的参数
            'reqReserved' => $payment->code,        //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据
        );
        sign ($params);

        $uri = SDK_App_Request_Url;
        $result = Http::post($uri, $params);
        parse_str($result, $result_arr);
        $pay_request['tn'] = $result_arr['tn'];
        $pay_request['packages'] = $result;
        return $pay_request;
    }

    /**
     * 创建订单支付参数（会员订单，商家订单）
     * @param array $userPayLog     支付日志
     * @param array $payment        支付方式
     * @param array $extend         扩展
     * @param int   $isSeller       是否是商家
     */
    private static function alipayWebPay($userPayLog, &$payment, $extend = [], $isSeller)
    {
        require_once base_path().'/vendor/alipay/alipay_submit.class.php';

        //支付类型
        $payment_type = "1";
        //必填，不能修改
        //服务器异步通知页面路径
        if($isSeller){
            $notify_url = Config::get('app.callback_url').'payment/alipay/webpcnotifys';
            //页面跳转同步通知页面路径
            $return_url = '';//'http://www.'.Config::get('app.domain').'/Order/detail?id='.$userPayLog->order_id.'&activityId='.$userPayLog->activity_id;
        } else {
            $notify_url = Config::get('app.callback_url').'payment/alipay/webpcnotify';
            //页面跳转同步通知页面路径
            if ($userPayLog->order_id < 1) { //充值跳转
                $return_url = 'http://www.'.Config::get('app.domain').'/UserCenter/balance';
            } else {
                $return_url = 'http://www.'.Config::get('app.domain').'/Order/detail?id='.$userPayLog->order_id.'&activityId='.$userPayLog->activity_id;
            }
        }
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

        //商品展示地址
        $show_url = "";

        //防钓鱼时间戳
        $anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数

        //客户端的IP地址
        $exter_invoke_ip = "";
        //非局域网的外网IP地址，如：221.0.0.1

        $config = $payment->config;

        $alipay_config['partner']       = $config['partnerId'];
        $alipay_config['seller_email']  = $config['sellerId'];
        $alipay_config['key']           = $config['partnerKey'];
        $alipay_config['sign_type']     = 'MD5';
        $alipay_config['input_charset'] = 'utf-8';
        $alipay_config['cacert']        = base_path().'/vendor/alipay/cacert.pem';
        $alipay_config['transport']     = 'https';

        /************************************************************/

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "partner" => trim($alipay_config['partner']),
            "seller_email" => trim($alipay_config['seller_email']),
            "payment_type"  => $payment_type,
            "notify_url"    => $notify_url,
            "return_url"    => $return_url,
            "out_trade_no"  => $userPayLog->sn,
            "subject"   => str_replace('&', '', $userPayLog->content),
            "total_fee" => $userPayLog->money,
            "body"  => str_replace('&', '', $userPayLog->content),
            "show_url"  => $show_url,
            "anti_phishing_key" => $anti_phishing_key,
            "exter_invoke_ip"   => $exter_invoke_ip,
            "_input_charset"    => trim(strtolower($alipay_config['input_charset']))
        );

        //建立请求
        $alipaySubmit = new \AlipaySubmit($alipay_config);

        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");

        return ['html' => $html_text];
    }

    /**
     * 创建订单支付参数（会员订单）
     * @param array $userPayLog     支付日志
     * @param array $payment        支付方式
     * @param array $extend         扩展
     */
    private static function alipayWapPay($userPayLog, &$payment, $extend = [])
    {
        require_once base_path().'/vendor/alipay/alipay_submit.class.php';

        //服务器异步通知页面路径
        $notify_url = Config::get('app.callback_url').'payment/alipay/webnotify';
        if ($userPayLog->order_id < 1) {  //充值
            //页面跳转同步通知页面路径
            $call_back_url = 'http://wap.'.urlencode(Config::get('app.domain')).'/UserCenter/balance';

            //操作中断返回地址
            $merchant_url = 'http://wap.'.urlencode(Config::get('app.domain')).'/UserCenter/balance';
        } else {
            //页面跳转同步通知页面路径
            $call_back_url = urlencode('http://wap.'.Config::get('app.domain').'/Order/detail?id='.$userPayLog->order_id.'&activityId='.$userPayLog->activity_id );

            //操作中断返回地址
            $merchant_url = urlencode('http://wap.'.Config::get('app.domain').'/Order/detail?id='.$userPayLog->order_id.'&activityId='.$userPayLog->activity_id);
        }


        $config = $payment->config;

        $req_data = '<direct_trade_create_req>
                        <notify_url>' . $notify_url . '</notify_url>
                        <call_back_url>' . $call_back_url . '</call_back_url>
                        <seller_account_name>' . $config['sellerId'] . '</seller_account_name>
                        <out_trade_no>' . $userPayLog->sn . '</out_trade_no>
                        <subject>' . str_replace('&', '', $userPayLog->content) . '</subject>
                        <total_fee>' . $userPayLog->money . '</total_fee>
                        <merchant_url>' . $merchant_url . '</merchant_url>
                    </direct_trade_create_req>';


        $alipay_config['partner']       = $config['partnerId'];
        $alipay_config['sellerEmail']   = $config['sellerId'];
        $alipay_config['key']           = $config['partnerKey'];
        $alipay_config['sign_type']     = 'MD5';
        $alipay_config['input_charset'] = 'utf-8';
        $alipay_config['cacert']        = base_path().'/vendor/alipay/cacert.pem';
        $alipay_config['transport']     = 'https';

        //构造要请求的参数数组，无需改动
        $para_token = array(
            "service"        => "alipay.wap.trade.create.direct",
            "partner"        => $alipay_config['partner'],
            "sec_id"         => $alipay_config['sign_type'],
            "format"         => 'xml',
            "v"              => '2.0',
            "req_id"         => $userPayLog->sn,
            "req_data"       => $req_data,
            "_input_charset" => $alipay_config['input_charset']
        );

        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestHttp($para_token);
        $html_text = urldecode($html_text);
        $para_html_text = $alipaySubmit->parseResponse($html_text);
        $request_token = $para_html_text['request_token'];
        $req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
        $parameter = array(
            "service"        => "alipay.wap.auth.authAndExecute",
            "partner"        => $alipay_config['partner'],
            "sec_id"         => $alipay_config['sign_type'],
            "format"         => 'xml',
            "v"              => '2.0',
            "req_id"         => $userPayLog->sn,
            "req_data"       => $req_data,
            "_input_charset" => $alipay_config['input_charset']
        );


        $alipaySubmit = new \AlipaySubmit($alipay_config);
        return ['html' => $alipaySubmit->buildRequestForm($parameter, 'get', '确认')];
    }

    /**
     * 支付宝退款
     */
    private static function alipayRefund($userRefundLog, &$payment, $extend = [])
    {
        require_once base_path().'/vendor/alipay/alipay_submit.class.php';

        //服务器异步通知页面路径
        $notify_url = Config::get('app.callback_url').'payment/alipay/refundnotify';
        //需http://格式的完整路径，不允许加?id=123这类自定义参数

        $config = $payment->config;

        $alipay_config['partner']       = $config['partnerId'];
        $alipay_config['seller_email']  = $config['sellerId'];
        $alipay_config['key']           = $config['partnerKey'];
        $alipay_config['sign_type']     = 'MD5';
        $alipay_config['input_charset'] = 'utf-8';
        $alipay_config['cacert']        = base_path().'/vendor/alipay/cacert.pem';
        $alipay_config['transport']     = 'https';

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "refund_fastpay_by_platform_pwd",
            "partner" => trim($alipay_config['partner']),
            "notify_url"    => $notify_url,
            "seller_email"  => $alipay_config['seller_email'],
            "refund_date"   => Time::toDate(Time::getTime()),
            "batch_no"  => $userRefundLog->sn,
            "batch_num" => 1,
            "detail_data"   => "{$userRefundLog->trade_no}^{$userRefundLog->money}^{$userRefundLog->content}",
            "_input_charset"    => trim(strtolower($alipay_config['input_charset']))
        );

        //建立请求
        $alipaySubmit = new \AlipaySubmit($alipay_config);

        return ['html' => $alipaySubmit->buildRequestForm($parameter, 'get', '确认')];
    }

    /**
     * 银联退款
     */
    private static function unionRefund($userRefundLog, &$payment, $extend = [])
    {
        $resultData = ['code'=>90000,'msg'=>'退款成功'];
        //服务器异步通知页面路径
        $notify_url = Config::get('app.callback_url').'payment/unionpay/refundnotify';
        //需http://格式的完整路径，不允许加?id=123这类自定义参数

        require_once base_path().'/vendor/unionpay/utf8/func/common.php';
        $config = $payment->config;
        $params = array(

            //以下信息非特殊情况不需要改动
            'version' => '5.0.0',             //版本号
            'encoding' => 'utf-8',            //编码方式
            'certId' => getSignCertId (),     //证书ID
            'signMethod' => '01',             //签名方法
            'txnType' => '31',                //交易类型
            'txnSubType' => '00',             //交易子类
            'bizType' => '000201',            //业务类型
            'accessType' => '0',              //接入类型
            'channelType' => '07',            //渠道类型
            'backUrl' => $notify_url, //后台通知地址
            
            //TODO 以下信息需要填写
            'orderId' => $userRefundLog->sn,     //商户订单号，8-32位数字字母，不能含“-”或“_”，可以自行定制规则，重新产生，不同于原消费，此处默认取demo演示页面传递的参数
            'merId' => $config['merId'],         //商户代码，请改成自己的测试商户号，此处默认取demo演示页面传递的参数
            'origQryId' => $userRefundLog->trade_no, //原消费的queryId，可以从查询接口或者通知接口中获取，此处默认取demo演示页面传递的参数
            'txnTime' => Time::toDate(UTC_TIME, 'YmdHis'),     //订单发送时间，格式为YYYYMMDDhhmmss，重新产生，不同于原消费，此处默认取demo演示页面传递的参数
            'txnAmt' => $userRefundLog->money*100,       //交易金额，退货总金额需要小于等于原消费
            // 请求方保留域，透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据
        );
        sign ($params);

        $url = SDK_BACK_TRANS_URL;
        
        $result = Http::post($url, $params);
        
        if (! $result) { //没收到200应答的情况 
            $resultData['code'] = 90001;
            $resultData['msg'] = '银联服务器未响应'; 
            return $resultData;
        }

        $result_arr = convertStringToArray( $result ); 
        
        //printResult ( $url, $params, $result ); //页面打印请求应答数据

        if ( !verify ( $result_arr ) ){
            $resultData['code'] = 90001;
            $resultData['msg'] = '验证签名失败'; 
            return $resultData;
        }

        if ($result_arr["respCode"] == "00"){
            //交易已受理，等待接收后台通知更新订单状态，如果通知长时间未收到也可发起交易状态查询
            DB::beginTransaction();
            try{
                UserRefundLog::where('id', $userRefundLog->id)
                             ->update(['status'=>1]);

                $refund = Refund::where("id", $userRefundLog->refund_id)->first(); 
                $refund->status = 1; 
                $refund->save(); 
                $order = Order::where("id", $refund->order_id)->with('user')->first(); 
                $order->status = ORDER_STATUS_REFUND_SUCCESS; 
                $order->save(); 
                $order = $order->toArray();
                //更新商家扩展,资金日志
                SellerMoneyLog::where('type','order_refund')->where('related_id', $order->id)->update(['status' => 1]);
                SellerMoneyLog::where('type','order_pay')->where('related_id', $order->id)->update(['status' => 4]);
                //SellerService::decrementExtend($order->seller_id, 'wait_confirm_money', $order->seller_fee);
                //通知客户
                PushMessageService::notice($order['user']['id'], $order['user']['mobile'], 'order.refundpay', $order);
                DB::commit();
            }catch(Exception $e){
                DB::rollback();
                $resultData['code'] = 90001;
                $resultData['msg'] = '退款成功，处理订单失败'; 
                return $resultData;
            }
        } else if ($result_arr["respCode"] == "03"
                || $result_arr["respCode"] == "04"
                || $result_arr["respCode"] == "05" ){
            //后续需发起交易状态查询交易确定交易状态
            //TODO
                $resultData['code'] = 90001;
                $resultData['msg'] = $result_arr['respMsg']; 
                return $resultData;
        } else {
            //其他应答码做以失败处理
             //TODO
                $resultData['code'] = 90001;
                $resultData['msg'] = $result_arr['respMsg']; 
                return $resultData;
        }
        return $resultData;
    }

    /**
     * 创建订单支付参数（会员订单）
     * @param array $userPayLog     支付日志
     * @param array $payment        支付方式
     * @param array $extend         扩展
     */
    private static function weixinPay($userPayLog, &$payment, $extend = []) {
        $config = $payment->config;
        $order_data = array(
            'appid' => $config['appId'],
            'body' => $userPayLog->content,
            'mch_id' => $config['partnerId'],
            'nonce_str' => md5(String::randString(16)),
            'notify_url' => Config::get('app.callback_url').'payment/weixin/notify',
            'out_trade_no' => $userPayLog->sn,
            'spbill_create_ip' => CLIENT_IP,
            'total_fee' => round($userPayLog->money * 100),
            'trade_type' => 'APP'
        );
        $xml  = '<xml>';
        $sign = '';
        foreach ($order_data as $key => $data) {
            if ($key == 'body') {
                $xml .= "\n<{$key}><![CDATA[{$data}]]></{$key}>";
            } else {
                $xml .= "\n<{$key}>{$data}</{$key}>";
            }
            $sign .= "{$key}={$data}&";
        }
        $sign = strtoupper(md5("{$sign}key={$config['partnerKey']}"));
        $xml .= "\n<sign>{$sign}</sign>\n</xml>";

        $response = Http::post('https://api.mch.weixin.qq.com/pay/unifiedorder', $xml);
        if (empty($response)) {
            return false;
        }

        $response = @simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$response || !isset($response->return_code) || $response->return_code != 'SUCCESS') {
            return false;
        }

        $pay_request = [
            'appid'     => $config['appId'],
            'noncestr'  => md5(String::randString(16)),
            'package'   => 'Sign=WXPay',
            'partnerid' => $config['partnerId'],
            'prepayid'  => strval($response->prepay_id),
            'timestamp' => UTC_TIME + date('Z')
        ];

        $sign = '';
        foreach ($pay_request as $key => $data) {
            $sign .= "{$key}={$data}&";
        }
        $pay_request['sign'] = strtoupper(md5("{$sign}key={$config['partnerKey']}"));
        $pay_request['packages'] = 'Sign=WXPay';
        return $pay_request;
    }

    /**
     * 创建订单支付参数（会员订单）
     * @param array $userPayLog     支付日志
     * @param array $payment        支付方式
     * @param array $extend         扩展
     */
    private static function weixinsellerPay($userPayLog, &$payment, $extend = []) {
        $config = $payment->config;
        $order_data = array(
            'appid' => $config['appId'],
            'body' => $userPayLog->content,
            'mch_id' => $config['partnerId'],
            'nonce_str' => md5(String::randString(16)),
            'notify_url' => Config::get('app.callback_url').'payment/weixin/notifys',
            'out_trade_no' => $userPayLog->sn,
            'spbill_create_ip' => CLIENT_IP,
            'total_fee' => round($userPayLog->money * 100),
            'trade_type' => 'APP'
        );
        $xml  = '<xml>';
        $sign = '';
        foreach ($order_data as $key => $data) {
            if ($key == 'body') {
                $xml .= "\n<{$key}><![CDATA[{$data}]]></{$key}>";
            } else {
                $xml .= "\n<{$key}>{$data}</{$key}>";
            }
            $sign .= "{$key}={$data}&";
        }
        $sign = strtoupper(md5("{$sign}key={$config['partnerKey']}"));
        $xml .= "\n<sign>{$sign}</sign>\n</xml>";

        $response = Http::post('https://api.mch.weixin.qq.com/pay/unifiedorder', $xml);
        if (empty($response)) {
            return false;
        }

        $response = @simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$response || !isset($response->return_code) || $response->return_code != 'SUCCESS') {
            return false;
        }

        $pay_request = [
            'appid'     => $config['appId'],
            'noncestr'  => md5(String::randString(16)),
            'package'   => 'Sign=WXPay',
            'partnerid' => $config['partnerId'],
            'prepayid'  => strval($response->prepay_id),
            'timestamp' => UTC_TIME + date('Z')
        ];

        $sign = '';
        foreach ($pay_request as $key => $data) {
            $sign .= "{$key}={$data}&";
        }
        $pay_request['sign'] = strtoupper(md5("{$sign}key={$config['partnerKey']}"));
        $pay_request['packages'] = 'Sign=WXPay';
        return $pay_request;
    }

    /**
     * 微信退款
     */
    private static function weixinRefund($userRefundLog, &$payment, $extend = [])
    {
        $config = $payment->config;

        $nonce_str = md5(String::randString(16));

        $refund_fee = (int)($userRefundLog->money * 100);

        $refund_data =
            [
                "appid"=>$config['appId'],
                "mch_id"=>$config['partnerId'],
                "nonce_str"=>$nonce_str,
                "op_user_id"=>$config['partnerId'],
                "out_refund_no"=>$userRefundLog->sn,
                "refund_fee"=>$refund_fee,
                "total_fee"=>$refund_fee,
                "transaction_id"=>$userRefundLog->trade_no
            ];

        $stringSign = "";

        foreach ($refund_data as $key => $value)
        {
            $stringSign .= "{$key}={$value}&";
        }

        $sign = strtoupper(md5("{$stringSign}key={$config['partnerKey']}"));

        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<xml>
   <appid>{$config['appId']}</appid>
   <mch_id>{$config['partnerId']}</mch_id>
   <nonce_str>{$nonce_str}</nonce_str>
   <op_user_id>{$config['partnerId']}</op_user_id>
   <out_refund_no>{$userRefundLog->sn}</out_refund_no>
   <out_trade_no></out_trade_no>
   <refund_fee>{$refund_fee}</refund_fee>
   <total_fee>{$refund_fee}</total_fee>
   <transaction_id>{$userRefundLog->trade_no}</transaction_id>
   <sign>{$sign}</sign>
</xml>";

        $pemPath = "";

        if(strpos($payment->type, "app") !== false)
        {
            $pemPath = "app/";
        }
        else  if(strpos($payment->type, "wxweb") !== false)
        {
            $pemPath = "wap/";
        }

        $response = Http::postSsl('https://api.mch.weixin.qq.com/secapi/pay/refund',
            base_path()."/vendor/weixinpay/{$pemPath}apiclient_cert.pem",
            base_path()."/vendor/weixinpay/{$pemPath}apiclient_key.pem",
            $xml);

        if (empty($response))
        {
            return false;
        }

        $response = @simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);

        if (!$response ||
            !isset($response->return_code) ||
            $response->return_code != 'SUCCESS')
        {
            return false;
        }


        $userRefundLog->status = 1;

        $userRefundLog->save();

        $refund = Refund::where("id", $userRefundLog->refund_id)->first();

        $refund->status = 1;

        $refund->save();

        $order = Order::where("id", $refund->order_id)->with('user')->first();

        $order->status = ORDER_STATUS_REFUND_SUCCESS;

        $order->save();

        $order = $order->toArray();

        try
        {
            //更新商家扩展,资金日志
            SellerMoneyLog::where('type','order_refund')->where('related_id', $order->id)->update(['status' => 1]);
            SellerMoneyLog::where('type','order_pay')->where('related_id', $order->id)->update(['status' => 4]);
            //SellerService::decrementExtend($order->seller_id, 'wait_confirm_money', $order->pay_fee);
            //通知客户
            PushMessageService::notice($order['user']['id'], $order['user']['mobile'], 'order.refundpay', $order);
        }
        catch (Exception $e)
        {
        }

        return true;
    }

    //微信JS支付
    private static function weixinJsPay($userPayLog, &$payment, $extend = []) {
        $config = $payment->config;
        $order_data = array(
            'appid' => $config['appId'],
            'body' => $userPayLog->content,
            'mch_id' => $config['partnerId'],
            'nonce_str' => md5(String::randString(16)),
            'notify_url' => Config::get('app.callback_url').'payment/weixin/jsnotify',
            'openid'     => $extend['openId'],
            'out_trade_no' => $userPayLog->sn,
            'spbill_create_ip' => CLIENT_IP,
            'total_fee' => round($userPayLog->money * 100),
            'trade_type' => 'JSAPI'
        );
        $xml  = '<xml>';
        $sign = '';
        foreach ($order_data as $key => $data) {
            if ($key == 'body') {
                $xml .= "\n<{$key}><![CDATA[{$data}]]></{$key}>";
            } else {
                $xml .= "\n<{$key}>{$data}</{$key}>";
            }
            $sign .= "{$key}={$data}&";
        }
        $sign = strtoupper(md5("{$sign}key={$config['partnerKey']}"));
        $xml .= "\n<sign>{$sign}</sign>\n</xml>";

        $response = Http::post('https://api.mch.weixin.qq.com/pay/unifiedorder', $xml);
        //return array('code'=> $response);
        if (empty($response)) {
            return false;
        }

        
        $response = @simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$response || !isset($response->return_code) || $response->return_code != 'SUCCESS') {
            return false;
        }

        $weixinConfig = SystemConfigService::getConfigByGroup('weixin');
        if (UTC_TIME + 1800 < $weixinConfig['access_token_expired']) {
            $access_token = $weixinConfig['access_token'];
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' .
                $config['appId'] . '&secret=' .$config['appSecret'];
            $token_json = Http::get($url);
            $token_json = empty($token_json) ? false : @json_decode($token_json, true);
            if(!$token_json || empty($token_json['access_token'])){
                return false;
            }
            $access_token = $token_json['access_token'];
            SystemConfigService::updateConfig('access_token', $access_token);
            SystemConfigService::updateConfig('access_token_expired', UTC_TIME + (int)$token_json['expires_in']);
        }

        if (UTC_TIME + 1800 < $weixinConfig['jsapi_ticket_expired']) {
            $jsapi_ticket = $weixinConfig['jsapi_ticket'];
        } else {
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
            $jsapi_json = Http::get($url);
            $jsapi_json = empty($jsapi_json) ? false : @json_decode($jsapi_json, true);
            if(!$jsapi_json || empty($jsapi_json['ticket'])){
                return false;
            }
            $jsapi_ticket = $jsapi_json['ticket'];
            SystemConfigService::updateConfig('jsapi_ticket', $jsapi_ticket);
            SystemConfigService::updateConfig('jsapi_ticket_expired', UTC_TIME + (int)$jsapi_json['expires_in']);
        }

        //js-sdk接口配置信息
        $jsapi_request = array(
            'jsapi_ticket' => $jsapi_ticket,
            'noncestr' => md5(String::randString(16)),
            'timestamp' => UTC_TIME,
            'url' => $extend['url']
        );
        $jsapi_request['signature'] = self::weixinSign($jsapi_request, '', 'sha1');
        $jsapi_request['appId'] = $config['appId'];

        //支付配置
        $pay_request = [
            'appId'     => $config['appId'],
            'nonceStr'  => md5(String::randString(16)),
            'package'   => 'prepay_id='.$response->prepay_id,
            'signType'  => 'MD5',
            'timeStamp' => UTC_TIME + date('Z')
        ];

        $pay_request['paySign'] = strtoupper(self::weixinSign($pay_request, $config['partnerKey']));
        $pay_request['jsapi']   = $jsapi_request;
        return $pay_request;
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
}
