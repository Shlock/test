<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\BankInfoService; 
use YiZan\Services\Sellerweb\UserService;  
use YiZan\Models\UserVerifyCode;
use Lang, Validator;

class BankinfoController extends BaseController {
	
	/**
	 * 银行卡信息
	 */
	public function get(){  
		$result = BankInfoService::getBankInfo($this->request('id'));
		$this->outputData($result);
	}

    //添加银行卡信息
    public function addInfo(){
           $number = $this->request('number');
           $bank   = $this->request('bank');
           $mobile = $this->request('mobile');
           $name   = $this->request('name');

        $validator = Validator::make(
                      [ 'number'=>$number,'bank'=>$bank,'name'=>$name,'mobile'=>$mobile],
                      [  'number' =>'required',
                          'bank'  =>'required',
                          'name'  =>'required',
                          'mobile'=>'required | regex:/^1[0-9]{10}$/'

                      ],
                      [   'number.required' =>10151,
                          'number.regex'    =>10156,
                          'bank.required'   =>10150,
                          'name.required'   =>10157,
                          'mobile.required' =>10101,
                          'mobile.regex'    =>10102
                      ]
        );
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            $this->output($result);
        }
          $result = BankInfoService::addInfoList($number,$bank,$mobile,$name,$this->request('sellerId'),$this->request('verifyCode'));
          $this->output($result);
    }


    public function edit(){
        $number = $this->request('bankNo');
        $bank   = $this->request('bank');
        $mobile = $this->request('mobile');
        $name   = $this->request('name');

        $validator = Validator::make(
            [ 'number'=>$number,'bank'=>$bank,'name'=>$name,'mobile'=>$mobile],
            [  'number' =>'required|regex:/^[0-9]{19}$/',
                'bank'  =>'required',
                'name'  =>'required',
                'mobile'=>'required | regex:/^1[0-9]{10}$/'

            ],
            [   'number.required' =>10151,
                'number.regex'    =>10156,
                'bank.required'   =>10150,
                'name.required'   =>10157,
                'mobile.required' =>10101,
                'mobile.regex'    =>10102
            ]
        );
        if ($validator->fails()){
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            $this->output($result);
        }
        $result = BankInfoService::editList($number,$bank,$mobile,$name,$this->request('sellerId'),$this->request('verifyCode'));
        $this->output($result);
    }






    /**
     * 验证验证码
     */
    public function checkVerify(){
        $result = ['code'=>0,
                   'msg' =>'操作成功'
        ];
        $verifyCodeId = UserService::checkVerifyCode($this->request('verifyCode'),$this->request('mobile'));
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            $result['msg']  = '验证码错误';
            return $result;exit;
        }
        $this->output($result);
    }
	/**
	 * 银行卡信息
	 */
	public function lists(){
	    $result = BankInfoService::lists(
	        $this->sellerId,
            max((int)$this->request('page'), 1), 
            max((int)$this->request('pageSize'), 20)
	    );
	    $this->outputData($result);
	}

	/**
	 * 修改银行卡信息手机号码验证
	 */
	public function bankinfoverify() 
    {
		$result = UserService::sendVerifyCode($this->request('mobile'), UserVerifyCode::TYPE_BANKINFO);
		$this->output($result);
	}
	
	/**
	 * 更新银行卡
	 */
	public function update(){   
		$result = BankInfoService::updateBankInfo($this->sellerId,$this->request('bank'),$this->request('bankNo'),$this->request('mobile'),$this->request('verifyCode'));  
        $this->output($result);
	} 
	/**
	 * 更新银行卡
	 */
	public function save(){
	    $result = BankInfoService::save($this->sellerId,$this->request('id'),$this->request('bank'),$this->request('bankNo'),$this->request('mobile'),$this->request('name'),$this->request('verifyCode'));
	    $this->output($result);
	}
	/**
	* 删除银行卡
	*/
	public function destroy(){
	    $result = BankInfoService::delete($this->sellerId,$this->request('id'));
	    $this->output($result);
	}
}