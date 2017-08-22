<?php
namespace YiZan\Utils;

class Ucpaas 
{
    public static $messages = 
    [
        "000000"=>"ok",
        "100000"=>"金额不为整数",
        "100001"=>"余额不足",
        "100002"=>"数字非法",
        "100003"=>"不允许有空值",
        "100004"=>"枚举类型取值错误",
        "100005"=>"访问IP不合法",
        "100006"=>"手机号不合法",
        "100015"=>"号码不合法",
        "100500"=>"HTTP状态码不等于200",
        "100007"=>"查无数据",
        "100008"=>"手机号码为空",
        "100009"=>"手机号为受保护的号码",
        "100010"=>"登录邮箱或手机号为空",
        "100011"=>"邮箱不合法",
        "100012"=>"密码不能为空",
        "100013"=>"没有测试子账号",
        "100014"=>"金额过大,不要超过12位数字",
        "100016"=>"余额被冻结",
        "100017"=>"余额已注销",
        "100018"=>"通话时长需大于60秒",
        "100699"=>"系统内部错误",
        "100019"=>"应用餘額不足",
        "100020"=>"字符长度太长",
        "100104"=>"callId不能为空",
        "100105"=>"日期格式错误",
        "101100"=>"请求包头Authorization参数为空",
        "101101"=>"请求包头Authorization参数Base64解码失败",
        "101102"=>"请求包头Authorization参数解码后账户ID为空",
        "101103"=>"请求包头Authorization参数解码后时间戳为空",
        "101104"=>"请求包头Authorization参数解码后格式有误",
        "101105"=>"主账户ID存在非法字符",
        "101106"=>"请求包头Authorization参数解码后时间戳过期",
        "101107"=>"请求地址SoftVersion参数有误",
        "101108"=>"主账户已关闭",
        "101109"=>"主账户未激活",
        "101110"=>"主账户已锁定",
        "101111"=>"主账户不存在",
        "101112"=>"主账户ID为空",
        "101113"=>"请求包头Authorization参数中账户ID跟请求地址中的账户ID不一致",
        "101114"=>"请求地址Sig参数为空",
        "101115"=>"请求token校验失败",
        "101116"=>"主账号sig加密串不匹配",
        "101117"=>"主账号token不存在",
        "102100"=>"应用ID为空",
        "102101"=>"应用ID存在非法字符",
        "102102"=>"应用不存在",
        "102103"=>"应用未审核通过",
        "102104"=>"测试应用不允许创建client",
        "102105"=>"应用不属于该主账号",
        "102106"=>"应用类型错误",
        "102107"=>"应用类型为空",
        "102108"=>"应用名为空",
        "102109"=>"行业类型为空",
        "102110"=>"行业信息错误",
        "102111"=>"是否允许拨打国际填写错误",
        "102112"=>"是否允许拨打国际不能为空",
        "102113"=>"创建应用失败",
        "102114"=>"应用名称已存在",
        "103100"=>"子账户昵称为空",
        "103101"=>"子账户名称存在非法字符",
        "103102"=>"子账户昵称长度有误",
        "103103"=>"子账户clientNumber为空",
        "103104"=>"同一应用下，friendlyname重复",
        "103105"=>"子账户friendlyname只能包含数字和字母和下划线",
        "103106"=>"client_number长度有误",
        "103107"=>"client_number不存在或不属于该主账号",
        "103108"=>"client已经关闭",
        "103109"=>"client充值失败",
        "103110"=>"client计费类型为空",
        "103111"=>"clientType只能取值0,1",
        "103112"=>"clientType为1时，charge不能为空",
        "103113"=>"clientNumber未绑定手机号",
        "103114"=>"同一应用下同一手机号只能绑定一次",
        "103115"=>"单次查询记录数不能超过100",
        "103116"=>"绑定手机号失败",
        "103117"=>"子账号是否显号(isplay)不能为空",
        "103118"=>"子账号是否显号(display)取值只能是0(不显号)和1(显号)",
        "103119"=>"应用下该子账号不存在",
        "103120"=>"friendlyname不能为空",
        "103121"=>"查询client参数不能为空",
        "103122"=>"client不属于应用",
        "103123"=>"未上线应用不能超过100个client",
        "103124"=>"已经是开通状态",
        "103125"=>"子账号余额不足",
        "103126"=>"未上线应用或demo只能使用白名单中号码",
        "103127"=>"测试demo不能创建子账号",
        "103128"=>"校验码不能为空",
        "103129"=>"校验码错误或失效",
        "103130"=>"校验号码失败",
        "103131"=>"解绑失败,信息错误或不存在绑定关系",
        "104100"=>"主叫clientNumber为空",
        "104101"=>"主叫clientNumber未绑定手机号",
        "104102"=>"验证码为空",
        "104103"=>"显示号码不合法",
        "104104"=>"语音验证码位4-8位",
        "104105"=>"语音验证码位4-8位",
        "104106"=>"语音通知类型错误",
        "104107"=>"语音通知内容为空",
        "104108"=>"语音ID非法",
        "104109"=>"文本内容存储失败",
        "104110"=>"语音文件不存在或未审核",
        "104111"=>"号码与绑定的号码不一致",
        "104112"=>"开通或关闭呼转失败",
        "104113"=>"不能同时呼叫同一被叫",
        "104114"=>"内容包含敏感词",
        "104115"=>"语音通知发送多语音ID不能超过5个",
        "104116"=>"呼转模式只能取1,2,3,4",
        "104117"=>"呼转模式为2,4则必须填写forwardPhone",
        "104118"=>"呼转模式为2、4则前转号码与绑定手机号码不能相等",
        "104119"=>"群聊列表格式不合法",
        "104120"=>"群聊呼叫模式只能是1免费,2直拨,3智能拨打",
        "104121"=>"群聊ID不能为空",
        "104122"=>"群聊超过最大方数",
        "104123"=>"群聊ID发送错误",
        "104124"=>"群聊操作失败服务出错",
        "104125"=>"呼转号码不存在",
        "104126"=>"订单号不能为空",
        "104127"=>"订单号不存在",
        "104128"=>"号码释放失败或号码已经自动释放",
        "104129"=>"显手机号必须是呼叫列表中的号码",
        "104130"=>"主被叫不能相同",
        "104131"=>"开通国际漫游禁止回拨呼叫",
        "105100"=>"短信服务请求异常",
        "105101"=>"url关键参数为空",
        "105102"=>"号码不合法",
        "105103"=>"没有通道类别",
        "105104"=>"该类别为冻结状态",
        "105105"=>"没有足够金额",
        "105106"=>"不是国内手机号码并且不是国际电话",
        "105107"=>"黑名单",
        "105108"=>"含非法关键字",
        "105109"=>"该通道类型没有第三方通道",
        "105110"=>"短信模板ID不存在",
        "105111"=>"短信模板未审核通过",
        "105112"=>"短信模板替换个数与实际参数个数不匹配",
        "105113"=>"短信模板ID为空",
        "105114"=>"短信内容为空",
        "105115"=>"短信类型长度应为1",
        "105116"=>"同一天同一用户不能发超过3条相同的短信",
        "105117"=>"模板ID含非法字符",
        "105118"=>"短信模板有替换内容，但参数为空",
        "105119"=>"短信模板替换内容过长，不能超过70个字符",
        "105120"=>"手机号码不能超过100个",
        "105121"=>"短信模板已删除",
        "105122"=>"同一天同一用户不能发超过N条验证码(n为用户自己配置)",
        "105123"=>"短信模板名称为空",
        "105124"=>"短信模板内容为空",
        "105125"=>"创建短信模板失败",
        "105126"=>"短信模板名称错误",
        "105127"=>"短信模板内容错误",
        "105128"=>"短信模板id为空",
        "105129"=>"短信模板id不存在",
        "103123"=>"未上线应用不能超过100个client",
        "103124"=>"已经是开通状态",
        "103125"=>"子账号余额不足",
        "103126"=>"未上线应用或demo只能使用白名单中号码",
        "103127"=>"测试demo不能创建子账号",
        "105128"=>"短信模板id为空",
        "105129"=>"短信模板id不存在",
        "105130"=>"30秒内不能连续发同样的内容",
        "105131"=>"30秒内不能给同一号码发送相同模板消息",
        "105132"=>"验证码短信参数长度不能超过10位"
    ];

    public static $host = "api.ucpaas.com";
    
    public static $softVersion = "2014-06-30";

    public static $accountSid = "919fadcc49968f49d52adba639a11361";

    public static $authToken = "4e07a519941209e620a69eab0c13cfe7";
    
    public static $appId = "9a1be1a925c24e0f85ebb0142b800a94";
    
    public static $templateId = "7671";
    
    public static $displayNum = "13000000000";

    /**
     * 发送短信
     * @param string $host 主机名
     * @param string $softVersion 版本号
     * @param string $accountSid 账户
     * @param string $authToken 密码
     * @param string $appId 应用编号
     * @param string $templateId 模板
     * @param string $param 参数
     * @param string $to 发送到谁
     * @return bool 是否发送成功
     */
    public static function sendCompleteMessages($host, $softVersion, $accountSid, $authToken, $appId, $templateId, $param, $to)
    {
        $dateTime = Time::toDate(Time::getTime(), "YmdHis");
        
        $sigParameter = strtoupper(md5("{$accountSid}{$authToken}{$dateTime}"));

        $authorization = base64_encode("{$accountSid}:{$dateTime}");
        
        $url = "https://{$host}/{$softVersion}/Accounts/{$accountSid}/Messages/templateSMS?sig={$sigParameter}";
        
        $content = "
            {
	            \"templateSMS\" : 
	            {
		            \"appId\"       : \"{$appId}\",
		            \"param\"       : \"{$param}\",
		            \"templateId\"  : \"{$templateId}\",
		            \"to\"          : \"{$to}\"
                }
            }";
        
        $contentLength = strlen($content);
        
        $options = 
        [
            'http' => 
            [
                'method' => 'POST',
                'header' => "Content-type:application/json;charset=utf-8\r\n" .
                    "Content-length:{$contentLength}\r\n".
                    "Authorization:{$authorization}\r\n".
                    "Host:api.ucpaas.com\r\n".
                    "Accept:application/json\r\n",
                'content' => $content
            ]
        ];

        $result = file_get_contents($url, false, stream_context_create($options));

        $result = json_decode($result);
        
        return $result->resp->respCode == "000000";
    }
    /**
     * 发送短信
     * @param string $param 参数
     * @param string $to 发送到谁
     * @return bool 是否发送成功
     */
    public static function sendMessages($param, $to)
    {
        return self::sendCompleteMessages(
            self::$host,
            self::$softVersion,
            self::$accountSid,
            self::$authToken,
            self::$appId,
            self::$templateId,
            $param, 
            $to);
    }
    /**
     * 发送语音
     * @param string $host 主机名
     * @param string $softVersion 版本号
     * @param string $accountSid 账户
     * @param string $authToken 密码
     * @param string $appId 应用编号
     * @param string $displayNum 显示号码
     * @param string $verifyCode 验证码
     * @param string $to 发送到谁
     * @return bool 是否发送成功
     */
    public static function sendCompleteCalls($host, $softVersion, $accountSid, $authToken, $appId, $displayNum, $verifyCode, $to)
    {
        $dateTime = Time::toDate(Time::getTime(), "YmdHis");
        
        $sigParameter = strtoupper(md5("{$accountSid}{$authToken}{$dateTime}"));

        $authorization = base64_encode("{$accountSid}:{$dateTime}");

        $url = "https://api.ucpaas.com/{$softVersion}/Accounts/{$accountSid}/Calls/voiceCode?sig={$sigParameter}";

        $content = "
            {
                \"voiceCode\"  : 
                {
		        \"appId\"      : \"{$appId}\",
		        \"to\"         : \"{$to}\",
		        \"verifyCode\" : \"{$verifyCode}\",
		        \"displayNum\" : \"{$displayNum}\"
                }
            }";

        $contentLength = strlen($content);

        $options = 
        [
            'http' => 
            [
                'method' => 'POST',
                'header' => "Content-type:application/json;charset=utf-8\r\n" .
                    "Content-length:{$contentLength}\r\n".
                    "Authorization:{$authorization}\r\n".
                    "Host:api.ucpaas.com\r\n".
                    "Accept:application/json\r\n",
                'content' => $content
            ]
        ];

        $result = file_get_contents($url, false, stream_context_create($options));
        
        $result = json_decode($result);
        
        return $result->resp->respCode == "000000";
    }
    /**
     * 发送语音
     * @param string $param 参数
     * @param string $to 发送到谁
     * @return bool 是否发送成功
     */
    public static function sendCalls($verifyCode, $to)
    {
        return self::sendCompleteCalls(
            self::$host,
            self::$softVersion,
            self::$accountSid,
            self::$authToken,
            self::$appId,
            self::$displayNum,
            $verifyCode, 
            $to);
    }
}