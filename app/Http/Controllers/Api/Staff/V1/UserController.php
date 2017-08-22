<?php 
namespace YiZan\Http\Controllers\Api\Staff;

use YiZan\Services\Staff\UserService;
use YiZan\Services\SellerService;
use YiZan\Services\UserAddressService;
use YiZan\Services\UserMobileService;
use YiZan\Services\SellerStaffService;
use YiZan\Services\Staff\ReadMessageService;
use Lang, Validator;

class UserController extends BaseController {
	/**
	 * 手机号码验证
	 */
	public function mobileverify() 
    {
		$result = UserService::sendVerifyCode($this->request('mobile'), 'reg');
		$this->output($result);
	}

	/**
	 * 会员登录
	 */
	public function login() 
    {
        if($this->request('verifyCode') == true)
        {
            $this->verifylogin();
            return;
        }
        
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
      
        //未找到会员时
		if (!$user) 
        {
			$result['code'] = 10108;
	    	$this->output($result);
		}
	/*
        if ($user->status != 1)
        {
            $result['code'] = 10125;
            $this->output($result);
        }

	*/
	$pwd = md5(md5($pwd) . $user->crypt);
    
        //登录密码错误
		if ($user->pwd != $pwd) {
			$result['code'] = 10109;
	    	$this->output($result);
		}
		
		$this->createToken($user->id, $user->pwd);
        $staff = SellerStaffService::getByUserId($user->id);
       	$seller = SellerService::getById(0, $user->id);
       	
        //找不到卖家信息,则返回
        if (!$staff && !$seller) 
        {
            $result['code'] = 10108;
	    	$this->output($result);
        }
		
        //找员工信息,状态锁定
        /*if ($staff && $staff->status != 1)
        {
            $result['code'] = 10125;
            $this->output($result);
        }*/

        //找到卖家信息,且还未认证
        if ($seller && $seller->is_check != 1)
        {
            $result['code'] = 10123;
	    	$this->output($result);
        }

        //找到卖家信息,状态锁定
        /*if ($seller && $seller->status != 1)
        {
            $result['code'] = 10125;
            $this->output($result);
        }*/

        $role = 0;
        
        if ($seller) {
			$role = 1; // 只是商家，不是人员
			$sellerId = $seller->id;
			$staffId = 0;
			$mobile = $seller->mobile;
			$name = $seller->name;
			$avatar = $seller->logo;
		}
        if ($staff) {
        	switch ($staff->type) {
				case 0:
					$role |= 7; //既是个人商家又是人员
					break;
				case 1:
					$role |= 2; //配送人员
					break;
				case 2:
					$role |= 4; // 服务人员
					break;
				case 3:
					$role |= 6; //同时是配送又是服务人员
					break;
			}
			$staffId = $staff->id;
			$sellerId = $staff->seller_id;
			$mobile = $staff->mobile;
			$name = $staff->name;
			$avatar = $staff->avatar;
        }
		
        $user = 
            [
                "id"=> $user->id,
                "staffId" => $staffId,
                "sellerId"=> $sellerId,
                "mobile"=>$mobile,
                "name"=> $name,
                "avatar"=>$avatar,
               	"role" => $role,
               	'bg' => asset('wap/community/client/images/top-bg.png'),
            ];
   
		$result['data'] = $user;
	    $result['token'] = $this->token;
	    $result['userId'] = $user['id'];
		file_put_contents('/mnt/test/sq/storage/logs/test.log',print_r($result,true));
		$this->output($result);
	}
    /**
     * 会员登录
     */
	public function verifylogin()
    {
		$result = array(
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.user_login')
		);

		$rules = array(
		    'mobile' => ['required','regex:/^1[0-9]{10}$/']
		);

		$messages = array(
		    'mobile.required'	=> '10101',
		    'mobile.regex'		=> '10102'
		);

		$mobile = $this->request('mobile');
        
        $verifyCode = $this->request('verifyCode');

		$validator = Validator::make([
				'mobile' => $mobile
			], $rules, $messages);
        
		if ($validator->fails()) {//验证信息
	    	$messages = $validator->messages();
	    	$result['code'] = $messages->first();
	    	$this->output($result);
	    }

        if(UserService::checkVerifyCode($verifyCode, $mobile) == false)
        {
            $result['code'] = 10104; // 验证码不正确
            
	    	$this->output($result);
        }
        
		$user = UserService::getByMobile($mobile);
        
        //未找到会员时
		if (!$user) 
        {
			$result['code'] = 10108;
	    	$this->output($result);
		}

		$this->createToken($user->id, $user->pwd);
        $staff = SellerStaffService::getByUserId($user->id);
        $seller = SellerService::getById(0, $user->id);
        
        //找不到卖家信息,则返回
        if (!$staff && !$seller) 
        {
            $result['code'] = 10108;
	    	$this->output($result);
        }

        if ($seller && $seller->is_check != 1)
        {
            $result['code'] = 10123;
	    	$this->output($result);
        }
        $role = 0;

        if ($seller) {
			$role = 1; // 只是商家，不是人员
			$sellerId = $seller->id;
			$staffId = 0;
			$mobile = $seller->mobile;
			$name = $seller->name;
			$avatar = $user->avatar;
		}
        if ($staff) {
        	switch ($staff->type) {
				case 0:
					$role |= 7; //既是商家又是人员
					break;
				case 1:
					$role |= 2; //配送人员
					break;
				case 2:
					$role |= 4; // 服务人员
					break;
				case 3:
					$role |= 6; //同时是配送又是服务人员
					break;
			}
			$staffId = $staff->id;
			$sellerId = $staff->seller_id;
			$mobile = $staff->mobile;
			$name = $staff->name;
			$avatar = $staff->avatar;
        }

        $user = 
            [
                "id"=>$user->id,
                "staffId" => $staffId,
                "sellerId"=> $sellerId,
                "mobile"=>$mobile,
                "name"=> $name,
                "avatar"=>$avatar,
               	"role" => $role,
               	'bg' => asset('wap/community/client/images/top-bg.png'),
            ];
		$result['data'] = $user;
	    $result['token'] = $this->token;
	    $result['userId'] = $user['id'];
		$this->output($result);
	}

	/**
	 * 会员退出
	 */
	public function logout() {
		$this->outputCode(0, Lang::get('api.success.user_logout'));
	}

	/**
	 * 修改密码
	 */
	public function repwd() 
    {
		$result = UserService::createUser(
				$this->request('mobile'), 
				$this->request('verifyCode'),
				$this->request('pwd'),
				$this->request('type', 'repass')
			);
        if ($result['code'] == 0) {
            $user = $result['data'];
            $this->createToken($user->id, $user->pwd);
            $staff = SellerStaffService::getByUserId($user->id);
            $seller = SellerService::getById(0, $user->id);
            $role = 0;
            if ($seller) {
                $role = 1; // 只是商家，不是人员
                $staffId = 0;
            }

            if ($staff) {
                switch ($staff->type) {
                    case 0:
                        $role |= 7; //既是个人商家又是人员
                        break;
                    case 1:
                        $role |= 2; //配送人员
                        break;
                    case 2:
                        $role |= 4; // 服务人员
                        break;
                    case 3:
                        $role |= 6; //同时是配送又是服务人员
                        break;
                }
                $staffId = $staff->id;
                $sellerId = $staff->seller_id;
                $mobile = $staff->mobile;
                $name = $staff->name;
                $avatar = $staff->avatar;
            }

            if ($seller) {
                $sellerId = $seller->id;
                $mobile = $seller->mobile;
                $name = $seller->name;
                $avatar = $seller->logo;
            }

            $user =
                [
                    "id"=> $user->id,
                    "staffId" => $staffId,
                    "sellerId"=> $sellerId,
                    "mobile"=>$mobile,
                    "name"=> $name,
                    "avatar"=>$avatar,
                    "role" => $role,
                    'bg' => asset('wap/community/client/images/top-bg.png'),
                ];

            $result['data'] = $user;
            $result['token'] = $this->token;
            $result['userId'] = $user['id'];

            $this->output($result);
        }


		$this->output($result);
	}
    /**
     * 卖家状态
     */
    public function msgStatus()
    {
        $reslut = ReadMessageService::hasNewMessage($this->staffId);
        
        $info = array(
			'code' 	=> 0,
			'data' 	=> ["hasNewMessage"=>$reslut],
			'msg'	=> ""
		);
        
		$this->output($info);
    }

    /**
     * 佣金账单查询
     */
    public function commission(){
        $data = UserService::commission(
           	$this->staffId,
            max($this->request('page'), 1)
        );
        $this->outputData($data);
    }

    /**
     * 申请提现
     */
    public function withdraw(){
        $data = UserService::applyAccount(
           	$this->sellerId,
            (float)$this->request('amount')
        );
        $this->output($data);
    }
    
    /**
     * 银行账户
     */
    public function bankinfo(){
        $data = UserService::getSellerBank(
           	$this->sellerId
        );
        $this->output($data);
    }
}
