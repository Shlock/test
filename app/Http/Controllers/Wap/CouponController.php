<?php 
namespace YiZan\Http\Controllers\Wap;
use Illuminate\Support\Facades\Response;
use Input, View, Redirect, Request, Lang;
/**
 * 优惠券控制器
 */
class CouponController extends UserAuthController {
	public function __construct() {
		parent::__construct();

		View::share('nav','coupon');
	}

	/**
	 * 我的优惠券
	 */
	public function index() {
		$args = Input::all();
		//可用
		$list = $this->requestApi('user.promotion.lists',$args);
        //print_r($list);exit;
		if($list['code'] == 0)
			View::share('list',$list['data']);
		View::share('args',$args);
        if (Input::ajax()) {
            return $this->display('item');
        } else {
            $data = $this->requestApi('user.promotion.lists',['status'=>2]);
            View::share('count', $data['data']['count']);
            return $this->display();
        }

	}

	/**
	 * 使用优惠券
	 */
	public function usepromotion() {
        $args = Input::get();
        $args['status'] = 2;
        $list = $this->requestApi('user.promotion.lists',$args);
        if($list['code'] == 0)
            View::share('list',$list['data']['list']);
        View::share('args',$args);
        if (Input::ajax()) {
            return $this->display('use_item');
        } else {
            return $this->display();
        }
	}

	/**
	 * 优惠券兑换
	 */
	public function excoupon() {
		$args = Input::get();
        $args['type'] = (int)$args['id'] > 0 ? 1 : 2;
        $args['promotionId'] = (int)$args['id'];
		$result = $this->requestApi('user.promotion.exchange',$args);
        return Response::json($result);
			
	}

    /**
     * 领券(暂时停用)
     */
    public function get() {
        exit;
        $args = Input::get();
        $args['status'] = 2;
        $result = $this->requestApi('user.promotion.lists',$args);
        //print_r($result['data']);
        View::share('list', $result['data']['list']);
        View::share('args', $args);
        if (Input::ajax()) {
            return $this->display('get_item');
        } else {
            return $this->display();
        }

    }


	
	/**
	 * [wxpay 微信支付]
	 */
	public function wxpay(){
       	$args = Input::all();
        $url = u('Coupon/pay',$args);
        $openid = Session::get('wxpay_open_id');
        if(empty($openid)){
            $url = u('Weixin/authorize', ['url' => urlencode($url)]);
        }else{
            $url .= '&openId='.$openid;
        }
        return Redirect::to($url);
    }

	/**
	 * 优惠券支付
	 */
	public function pay() {
		$args = Input::all();
		if (isset($args['payment']) && $args['payment'] == 'weixinJs') {
			Session::put('wxpay_open_id', $args['openId']);
			Session::put('pay_payment', 'weixinJs');
			return Redirect::to(u('Coupon/pay',['activityId' => $args['activityId']]));
		}

		
		if (!isset($args['payment'])) {
			$args['payment'] = Session::get('pay_payment');
			$args['openId'] = Session::get('wxpay_open_id');
		}

		$args['extend']['url'] = Request::fullUrl();

		if (!empty($args['openId'])) {
			$args['extend']['openId'] = $args['openId'];
		}
		$pay = $this->requestApi('activity.pay', $args);

		if($pay['code'] == 0){
			if (isset($pay['data']['payRequest']['html'])) {
				echo $pay['data']['payRequest']['html'];
				exit;
			}
			View::share('pay',$pay['data']['payRequest']);
		}

		$result = $this->requestApi('activity.detail',array('activityId' => $args['activityId']));
		if($result['code'] == 0){
			View::share('data',$result['data']);
		}
		View::share('payment',$args['payment']);
		View::share('activityId',$args['activityId']);
		return $this->display();
	}


	/**
	 * 完成支付
	 */
	public function payfinish() {
		$args = Input::all();
		//重复参数
		if( Session::get('payfinish_auth') == md5($args['sn'].$args['activityId']) ){
			return Redirect::to(u('Coupon/finish',['type' => 1])); 
			exit;
		}

		Session::put('payfinish_auth',md5($args['sn'].$args['activityId']));
		Session::save();

		return Redirect::to(u('Coupon/finish',['type' =>2]));
	}

	/**
	 *支付结果页面
	 */
	public function finish() {
		$args = Input::all();
		if($args['type'] == 1){
			$data['msg'] = Lang::get('wap.code.100000');
			$data['title'] = Lang::get('wap.error.800000');
		}
		elseif($args['type'] == 2){
			$data['msg'] = Lang::get('wap.code.100001');
			$data['title'] = Lang::get('wap.success.900000');
		}
		View::share('data',$data);
		return $this->display();
	}
}