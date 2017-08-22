<?php
namespace YiZan\Http\Controllers\Api\Staff;

use YiZan\Services\AdvService;
use YiZan\Services\RegionService;
use YiZan\Services\PaymentService;
use YiZan\Services\SystemConfigService;
use Input;
/**
 * 配置
 */
class ConfigController extends BaseController {
	/**
	 * 首页轮播广告
	 */
	public function banners() {
		$data = AdvService::getAdvByCode('BUYER_INDEX_BANNER', (int)$this->request('cityId'));
		$this->outputData($data);
	}

	/**
	 * 首页分类
	 */
	public function categorys() {
		$data = AdvService::getAdvByCode('BUYER_INDEX_MENU', (int)$this->request('cityId'));
		$this->outputData($data);
	}

	public function token() {
		$this->createToken();
		$result = [
			'code' 	=> 0,
			'token' => $this->token,
			'data'	=> [
                'city'	=> RegionService::getOpenCityByIp(CLIENT_IP)
			]
		];
		$this->output($result);
	}

	/**
	 * Wap初始化
	 */
	public function init() {
		$this->createToken();

        $citys  = RegionService::getServiceCitys();
        $userAgent = $this->request('userAgent');

        $wapType = 'web';
        if (preg_match("/\sMicroMessenger\/\\d/is", $userAgent)) {
        	 $wapType = 'wxweb';
        }

		$result = [
			'code' 	=> 0,
			'token' => $this->token,
			'data'	=> [
				'citys' 	=> $citys,
                'city' 		=> RegionService::getOpenCityByIp(CLIENT_IP),
				'payments' 	=> PaymentService::getPaymentTypes(),
				'configs' 	=> SystemConfigService::getConfigs()
			]
		];
		$this->output($result);
	}

    /**
     * 得到配置
     */
    public function configByCode()
    {
        $result = SystemConfigService::getConfigByCode($this->request('code'));
        
        $this->outputData($result);
    }

    /**
     * 获取支付配置
     */
    public function getpayment() {
        $payment = PaymentService::getPayment($this->request('code'));
        $this->outputData($payment);
    }
}