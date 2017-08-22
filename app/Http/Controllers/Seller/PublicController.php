<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Http\Controllers\YiZanController; 
use YiZan\Utils\ImgVerify;
use View, Route, Input, Lang, Redirect, Response, Request, Time;
/**
 * 后台公共页面控制器
 */
class PublicController extends BaseController {
	/**
	 * 管理员登录
	 */
	public function login() {
		if( Session::get('seller_token') && Session::get('seller') ) {
			return Redirect::to('Index/index');
		}
		return $this->display();
	}
	/**
	 * 卖家注册页面
	 */
	public function register() { 
		$args = Input::all();
		$args['groupCode'] = 'seller_reg';
		$result = $this->requestApi('system.config.get',$args);   
		if($result['code']==0) {
			View::share('config',$result['data']);  
		}
		//获取商家分类
		$cateIds = $this->requestApi('sellerstaff.cateall');
		// var_dump($cateIds['data']);
        if($cateIds['code'] == 0)
            View::share('cateIds',$cateIds['data']);

		return $this->display();
	}

	/**
	 * 获取开通城市
	 * @return [type] [description]
	 */
	public function getOpenCitys(){
		$region_result = $this->requestApi('city.lists');
		return Response::json($region_result['data']);
	}

	/**
	* 	提交注册信息
	*/
	public function doregister() {
		$args = Input::all();
		// var_dump($args);
		// exit;
		$result = $this->requestApi('user.reg',$args);   
		return Response::json($result);
	}	
	/*
	* 	提交注册信息
	*/
	public function stypeGoods(){
		$result = $this->requestApi('goods.cate.lists');  
		$result = $this->requestApi('user.login',$args); 
		return Response::json($result['data']);
	}
	/**
	 * 检查登录提交信息
	 */
	public function dologin() {
		$args = Input::all();
		if(empty($args['mobile'])) return $this->error(Lang::get('seller.code.11000'), u('Public/login'), $args);
		if(empty($args['pwd'])) return $this->error(Lang::get('seller.code.11001'), u('Public/login'), $args);

		$result = $this->requestApi('user.login',$args);
        //print_r($result);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg'], u('Public/login'));
		}
		$this->setSecurityToken($result['token']);
		$this->setSeller($result['data']);
		return $this->success($result['msg'], u('Index/index'), $result['data']);
	}

	/**
	 * 生成验证码
	 */
	public function verify() 
    {
        if($this->checkVerify())
        {
            $args = Input::all();
            $result = $this->requestApi('user.'.$args['vertype'],$args); 
            return Response::json($result);
        }
	}
	
	/**
	 * 成功
	 */
	public function _success() {
		return $this->display();
	}

	/**
	 * 失败
	 */
	public function _error() {
		return $this->display();
	}

	public function forgetpwd() {
		return $this->display();
	}

	public function checkpwd() {
		$args = Input::all();
		$args['sellerId'] = 0;
		$result = $this->requestApi('user.changepwd',$args);
		return Response::json($result);
	}

	public function checkpwds() {
		return $this->display();
	}

	/**
	 * 管理员登出
	 */
	public function logout() {
		$this->requestApi('user.logout');

		Session::put('seller', null);
		$this->setSecurityToken(null);

		return Redirect::to('Public/login');
	}

	/**
     * 检索小区
     */
    public function search(){
        $args = Input::all();
        $args['isTotal'] = 1;
        $result = $this->requestApi('district.lists', $args);
        return Response::json($result);
    }

    /**
	 * 生成图形验证码
	 */
	public function imgverify() {
        $this->createVerify();
		$imgVerify = new ImgVerify();
		$imgVerify->doimg();	
		$code = $imgVerify->getCode();
		Session::set('imgVerify', $code);
		Session::save();
		exit;
	}
    public function createVerify() 
    {	
		Session::set("user_reg", md5(Request::getClientIp().$_SERVER['HTTP_USER_AGENT']));
		Session::set("user_reg_time", UTC_TIME);
		Session::save();
	}
    private function checkVerify() 
    {
		if (!Request::ajax()) 
        {
			return false;
		}
		
		$referer = Request::header('REFERER');
        
		if ($referer != u('Public/register')) 
        {
			return false;
		}

		$imgVerify = Session::get('imgVerify');
        
		if (strtolower($imgVerify) !== strtolower(Input::get('imgverify')) ) 
        {	
        	return false;
		}
        
        Session::set('imgverify', "");
        
        Session::save();

		$userRegTime 	= Session::get('user_reg_time');
        
		$userRegTimeRe 	= (int)Session::get('user_reg_time_re');

		/*if(Session::get('user_reg') != md5(Request::getClientIp().$_SERVER['HTTP_USER_AGENT']) ||
			($userRegTimeRe > Time::getTime() - 60) || $userRegTime >= Time::getTime()) {
			return false;
		}*/

		Session::put("user_reg_time_re", Time::getTime());
        
		Session::save();

		return true;
	}
}
