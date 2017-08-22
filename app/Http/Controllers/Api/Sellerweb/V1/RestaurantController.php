<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;
use YiZan\Services\Sellerweb\RestaurantService;
use YiZan\Services\Sellerweb\UserService;
use Lang,Validator;
/**
 * 餐厅
 */
class RestaurantController extends BaseController {
	/**
     * 餐厅列表
     */
    public function lists(){
        $data = RestaurantService::lists(
            $this->sellerId,
            $this->request('name'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        $this->outputData($data);
    }

	/**
	 * 餐厅审核列表
	 * @return [type] [description]
	 */
	public function auditLists() {
		$data = RestaurantService::auditLists(
            $this->sellerId,
            $this->request('name'),
            $this->request('disposeStatus'),
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
        );
        $this->outputData($data);
	}

	/**
	 *
     * 查看餐厅信息
     */
    public function lookat() {
        $data = RestaurantService::lookat(
            $this->request('id')
        );
        $this->output($data);
    }

    /**
     * 保存编辑餐厅信息(审核保存)
     */
    public function save() {
    	$data = RestaurantService::save(
            $this->request('id'),
            $this->request('name'),
            $this->request('logo'),
            $this->request('contacts'),
            $this->request('tel'),
            $this->request('mobile'),
            $this->request('beginTime'),
            $this->request('endTime'),
            $this->request('licenseImg'),
            $this->request('license'),
            $this->request('expired'),
            $this->request('address')
        );
        $this->output($data);
    }

    /**
     * 添加餐厅信息(添加保存)
     */
    public function add() {
    	$data = RestaurantService::add(
    		$this->sellerId,
            $this->request('id'),
            $this->request('name'),
            $this->request('logo'),
            $this->request('contacts'),
            $this->request('tel'),
            $this->request('mobile'),
            $this->request('password'),
            $this->request('beginTime'),
            $this->request('endTime'),
            $this->request('licenseImg'),
            $this->request('license'),
            $this->request('expired'),
            $this->request('source'),
            $this->request('address')
        );
        $this->output($data);
    }

    /**
     * 删除餐厅
     */
    public function delete() {
    	$data = RestaurantService::delete(
    		$this->sellerId,
            $this->request('id')
        );
        $this->output($data);
    }

	/**
	 *餐厅登录
	 */
	public function login() 
    {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.user_login')
		);

		$rules = array(
		    'mobile' => ['required','regex:/^1[0-9]{10}$/'],
		    'pwd' 	 => ['required','min:6','max:20']
		);

		$messages = array(
		    'mobile.required'	=> '10101',
		    'mobile.regex'		=> '10102',
		    'pwd.required' 		=> '10105',
		    'pwd.min' 			=> '10106',
		    'pwd.max' 			=> '10106',
		);

		$mobile = $this->request('mobile');
		$pwd 	= strval($this->request('pwd'));

		$validator = Validator::make([
				'mobile' => $mobile,
				'pwd' 	 => $pwd
			], $rules, $messages);
        
        //验证信息
		if ($validator->fails())
        {
	    	$messages = $validator->messages();
	    	$result['code'] = $messages->first();
	    	$this->output($result);
	    }

		$user = UserService::getByMobile($mobile);
        //未找到餐厅时
		if (!$user) 
        {
			$result['code'] = 10123;
	    	$this->output($result);
		}

		$pwd = md5(md5($pwd) . $user->crypt);
    
        //登录密码错误
		if ($user->pwd != $pwd) {
			$result['code'] = 10124;
	    	$this->output($result);
		}
		$this->createToken($user->id, $user->pwd);
		
		$restaurant = RestaurantService::getById($user->id);
		
        //未找到餐厅
		if (!$restaurant)
        {
			$result['code'] = 11123;
	    	$this->output($result);
		} 
		
		$restaurant = $restaurant->toArray();
		
		$user = $user->toArray();
		
		UserService::updateLoginInfo($user['id']);
		
		$result['data'] = $restaurant;
	    $result['token'] = $this->token;
	   
		$this->output($result);
	}
}