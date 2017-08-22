<?php 
namespace YiZan\Http\Controllers\Staff;
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
			default:
				break;
		}
		View::share('about', $this->getConfig($configcode));
		return $this->display();	
	}
	/**
     * 免责声明
     */
	public function disclaimer() {
		echo $this->getConfig('wap_disclaimer');
	}
	
    /**
     * 关于我们
     */
	public function aboutus() 
    {
    	echo $this->getConfig('wap_about_us');
	}
	/*优惠券使用说明*/
	public function instructions() 
    { 
    	echo $this->getConfig('coupon_exchange_explain');
	}

	/**
     * 订单须知
     */
	public function notice() {
		echo $this->getConfig('wap_order_notice');
	}
    /**
     * 使用帮助
     */
    public function help()
    {
    }
    /**
     * 服务介绍
     */
    public function introduce()
    {
    }
    /**
     * 退款协议
     */
    public function refund()
    {
    }
	/**
     * 关于我们(员工)
     */
	public function staffaboutus() {
		echo $this->getConfig('staff_about_us');
	}

	/**
     * 免责声明
     */
	public function staffdisclaimer() {
		echo $this->getConfig('staff_disclaimer');
	}
	/**
     * 订单须知
     */
	public function staffordernotice() {
		echo $this->getConfig('staff_order_notice');
	}
	/**
     * 服务范围
     */
	public function staffservice() {
		echo $this->getConfig('staff_service');
	}

	/**
	 * 不再提醒 
	 * 来源 Order/createMoreInfo
	 */
	public function notShowNotes() {
		Cache::forever('notShowNotes'.$this->userId, true);
	}
}