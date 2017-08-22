<?php 
namespace YiZan\Http\Controllers\Wap;
use Input, View, Cache;
/**
 * 更多控制器
 */
class MoreController extends BaseController {

	public function __construct() {
		parent::__construct();
        
        View::share('IsLogin', $this->userId > 1);
        
		View::share('nav','more');
	}
	/**
	 * 更多
	 */
	public function index() {
		View::share('seo_title','更多');
		return $this->display();	
	}

	/**
	 * 更多详细
	 */
	public function detail() {
		$code = Input::get('code');
		if (!isset($code)) {
			 return $this->error('非法请求');
		}
		switch ($code) {
			case '1':
				$configcode = 'wap_disclaimer';//免责声明
				break;
			case '2':
				$configcode = 'wap_service'; //服务范围
				break;
			case '3':
				$configcode = 'wap_about_us';//关于我们
				break;
			case '4':
				$configcode = 'wap_order_notice';//订单须知
				break;
			case '5':
				$configcode = 'coupon_exchange_explain'; //优惠券使用说明
				break;
			case '6':
				$configcode = 'wap_refund_agreement'; //退款说明
				break;
			case '7':
				$configcode = 'wap_help'; //使用帮助
				break;
			case '8':
				$configcode = 'wap_terrace_referral'; //平台洗车服务介绍
				break;
			case '9':
				$configcode = 'wap_lookopen_district'; //查看开通小区
				break;
			default:
				break;
		}
		//echo '123';
		//echo $this->getConfig($configcode);
		View::share('about', $this->getConfig($configcode));
		return $this->display();	
	}
	/**
     * 免责声明
     */
	public function disclaimer() {
        $this->showHtml('注册协议',$this->getConfig('wap_disclaimer'));
	}
	
    /**
     * 关于我们
     */
	public function aboutus() 
    {
        $this->showHtml('关于我们',$this->getConfig('wap_about_us'));
	}
	/*优惠券使用说明*/
	public function instructions() 
    {
        $this->showHtml('优惠券使用说明',$this->getConfig('coupon_exchange_explain'));
	}

	/**
     * 订单须知
     */
	public function notice() {
        $this->showHtml('订单须知',$this->getConfig('wap_order_notice'));
	}
    /**
     * 使用帮助
     */
    public function help()
    {
        $this->showHtml('使用帮助',$this->getConfig('wap_help'));
    }
    /**
     * 服务介绍
     */
    public function introduce()
    {
        $this->showHtml('服务介绍',$this->getConfig('wap_terrace_referral'));
    }
    /**
     * 退款协议
     */
    public function refund()
    {
        $this->showHtml('退款协议',$this->getConfig('wap_refund_agreement'));
    }

	/**
     * 关于我们(员工)
     */
	public function staffaboutus() {
        $this->showHtml('关于我们',$this->getConfig('staff_about_us'));
	}

	/**
     * 免责声明
     */
	public function staffdisclaimer() {
        $this->showHtml('免责声明',$this->getConfig('staff_disclaimer'));
	}
	/**
     * 订单须知
     */
	public function staffordernotice() {
        $this->showHtml('订单须知',$this->getConfig('staff_order_notice'));
	}
	/**
     * 服务范围
     */
	public function staffservice() {
        $this->showHtml('服务范围',$this->getConfig('staff_service'));
	}

	/**
	 * 不再提醒 
	 * 来源 Order/createMoreInfo
	 */
	public function notShowNotes() {
		Cache::forever('notShowNotes'.$this->userId, true);
	}


    public function showHtml($title, $content) {
        echo '<!DOCTYPE html>
            <html lang="zh-CN">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
                <title>'.$title.'</title>
            </head>
            <body>'.$content.'</body>
            </html>';
    }
	
}