<?php 
namespace YiZan\Http\Controllers\Api\Staff;
use YiZan\Services\RegionService;
use YiZan\Services\PaymentService;
use YiZan\Services\SystemConfigService;
use Config;

class AppController extends BaseController {
	/**
	 * APP初始化
	 */
	public function init() 
    {
		$this->createToken();
		$configs = SystemConfigService::getConfigByGroup('staff');
        $payments = PaymentService::getPayments('seller');

        foreach($payments as $key => $payment)
        {
            unset( $payments[$key]["alipayConfig"]);

            unset( $payments[$key]["weixinConfig"]);

            $payments[$key]["isDefault"] = 0;

            switch ($payment['code'])
            {
                case 'alipay':
                case 'alipayWap':
                    $payments[$key]["icon"] = asset('wap/images/ico/zf3.png');
                    break;
                case 'weixin':
                case 'weixinJs':
                    $payments[$key]["icon"] = asset('wap/images/ico/zf2.png');
                    break;
                case 'cashOnDelivery':
                    $payments[$key]["icon"] = asset('wap/images/ico/zf5.png');
                    break;
            }

        }

		$result = [
			'code' 	=> 0,
			'token' => $this->token,
			'key'	=> base_convert(Config::get('app.iv_rule'), 10, 24),
			'data'	=> [
                'appVersion' => $this->request("deviceType") == "ios" ? $configs['staff_app_version'] : $configs['staff_android_app_version'],
                'forceUpgrade' => (boolean)$configs['staff_force_upgrade'],
                'upgradeInfo' => $this->request("deviceType") == "ios" ? $configs['staff_upgrade_info'] : $configs['staff_android_upgrade_info'],
                'appDownUrl' => $this->request("deviceType") == "ios" ? $configs['staff_app_down_url'] : $configs['staff_android_app_down_url'],
                'serviceTel' => $configs['staff_service_tel'],
                'aboutUs' => $configs['staff_about_us'],
                'aboutUrl'=>u('wap#more/aboutus'),
                'protocolUrl'=>u('wap#more/disclaimer'),
                'helpUrl'=>u('wap#more/help'),
                'restaurantTips'=>asset('wap/images/ico/zf1.png'),
                'shareQrCodeImage'=>asset('wap/images/ico/zf1.png'),
                'oss'=>Config::get('app.image_config.oss'),
                'citys' => RegionService::getServiceCitys(),
                'payments' => $payments,
			]
		];
		$this->output($result);
	}
}