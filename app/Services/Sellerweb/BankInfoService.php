<?php 
namespace YiZan\Services\Sellerweb;
use YiZan\Models\SellerBank;
use YiZan\Models\Seller;
use YiZan\Services\Sellerweb\UserService;  
use YiZan\Models\UserVerifyCode;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use Exception, DB, Lang, Validator, App;
class BankInfoService extends \YiZan\Services\BaseService {  

	/**
	 * 获取银行卡信息
	 * @param  integer $sellerId 机构或个人编号
	 * @return array             银行卡信息
	 */
	public static function getBankInfo($id){
	//	$result = SellerBank::where('seller_id',$sellerId)->where('id',$id)->first();
        $result = Seller::where('id',$id)->with('banks')->first();
        return $result;
    }

    /**
     * 添加银行卡信息
     * @param  integer $sellerId 机构或个人编号
     * @param  integer $number  银行卡号
     * @param  integer $bank  开户银行
     * @param  integer $name  户主名
     * @return array             银行卡信息
     */
    public function addInfoList($number,$bank,$mobile,$name,$sellerId,$verifyCode){
        $result = [
            'code' => 0,
            'data' =>'',
            'msg'  =>Lang::get('api_sellerweb.success.success')
        ];
        //检测验证码
        $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_BANKINFO);
        if (!$verifyCodeId) {
            $result['code'] = 10104;
            return $result;exit;
        }
        //保存银行卡信息
        $sellerBank = new SellerBank();
        $sellerBank->seller_id   = $sellerId;
        $sellerBank->bank        = $bank;
        $sellerBank->bank_no     = $number;
        $sellerBank->name        = $name;
        $sellerBank->mobile      = $mobile;
        $sellerBank->save();
        if(!$sellerBank) {
            $result['code'] = 50604 ;//操作失败
            return $result;exit;
        }
        return $result;
    }
    /**
     * 编辑银行卡信息
     * @param  integer $sellerId 机构或个人编号
     * @param  integer $number  银行卡号
     * @param  integer $bank  开户银行
     * @param  integer $name  户主名
     * @return array             银行卡信息
     */

    public function editList($number,$bank,$mobile,$name,$sellerId,$verifyCode){
        $result = [
            'code' => 0,
            'data' =>'',
            'msg'  =>Lang::get('api_sellerweb.success.success')
        ];
        $rese = SellerBank::where('seller_id',$sellerId)->first();
        if($rese['mobile']!=$mobile){
            //检测验证码
            $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_BANKINFO);
            if (!$verifyCodeId) {
                $result['code'] = 10104;
                return $result;exit;
            }
        }
        //修改银行卡信息
         SellerBank::where('seller_id',$sellerId)->update(array(  'bank'   =>$bank,
                                                                   'bank_no'=>$number,
                                                                   'name'   =>$name,
                                                                   'mobile' =>$mobile
                ));
             return $result;
    }







	/**
	 * 银行卡信息
	 * @param  integer $sellerId 机构或个人编号
	 * @return array             银行卡信息
	 */
	public static function lists($sellerId,$page,$pageSize){
	    $list = SellerBank::where('seller_id',$sellerId);
	    $result['totalCount'] = $list->count();  
        $result['list'] = $list->skip(($page - 1) * $pageSize)
                ->take($pageSize)
                ->get()
                ->toArray();
		return $result;
	}
	/**
	 * 更新银行卡
	 * @param  integer $sellerId   机构或个人编号
	 * @param  string  $bank       银行名称
	 * @param  string  $bankNo     银行卡号
	 * @param  string  $mobile     验证手机
	 * @param  string  $verifyCode 验证码
	 * @return array               处理结果
	 */
	public static function save($sellerId,$id, $bank, $bankNo, $mobile,$name, $verifyCode){
	    $result = array(
	        'code'	=> self::SUCCESS,
	        'data'	=> null,
	        'msg'	=> ''
	    );
	    
	    $rules = array(
	        'bank'          => ['required'],
	        'bank_no'       => ['required'],
	        'mobile' 	 	=> ['required','mobile'],
	        'code' 	 		=> ['required','size:6'],
	        'name' 	 		=> ['required'],
	    );
	    
	    $validata = array(
	        'seller_id' => $sellerId,
	        'bank' 		=> $bank,
	        'bank_no'   => $bankNo,
	        'mobile'	=> $mobile,
	        'name'	    => $name,
	        'code'		=> $verifyCode
	    );
	    
	    $messages = array(
	        'bank.required'			=> 10150,	// 请输入银行
	        'bank_no.required'	    => 10151,	// 请输入银行卡号
	        'mobile.required'		=> 10101,
	        'mobile.mobile'			=> 10102,
	        'name.required' 		=> 10208,
	        'code.required' 		=> 10103,
	        'code.size' 			=> 10104,
	    );
	    
	    $validator = Validator::make($validata, $rules, $messages);
	    if ($validator->fails()) {//验证信息
	        $messages = $validator->messages();
	        $result['code'] = $messages->first();
	        return $result;
	    }
	    //检测验证码
	    $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_BANKINFO);
	    if (!$verifyCodeId) {
	        $result['code'] = 10104;
	        return $result;
	    }
	    $bankObj = new SellerBank();
	    $bankObj->seller_id 	= $sellerId;
	    $bankObj->bank 			= $bank;
	    $bankObj->bank_no 		= $bankNo;
	    $bankObj->mobile 		= $mobile;
	    $bankObj->name 		    = $name;
	    
	    DB::beginTransaction();
	    try
	    {
	        $bankObj->save();
	        DB::commit();
	    } catch (Exception $e) {
	        DB::rollback();
	        $result['code'] = 99999;
	    }
	    return $result;
	}

	/**
	 * 更新银行卡
	 * @param  integer $sellerId   机构或个人编号
	 * @param  string  $bank       银行名称
	 * @param  string  $bankNo     银行卡号
	 * @param  string  $mobile     验证手机
	 * @param  string  $verifyCode 验证码
	 * @return array               处理结果
	 */
	public static function updateBankInfo($sellerId, $bank, $bankNo, $mobile, $verifyCode){  

		$validata = array(
			'seller_id' => $sellerId,
			'bank' 		=> $bank,
			'bank_no'   => $bankNo, 
			'mobile'	=> $mobile,
			'code'		=> $verifyCode
		);

		$result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

		$rules = array( 
			'bank'          => ['required'],
			'bank_no'       => ['required'], 
		    'mobile' 	 	=> ['required','mobile'],
		    'code' 	 		=> ['required','size:6'],
		);

		$messages = array(
		    'bank.required'			=> 10150,	// 请输入银行 
            'bank_no.required'	    => 10151,	// 请输入银行卡号
		    'mobile.required'		=> 10101,
		    'mobile.mobile'			=> 10102,
		    'code.required' 		=> 10103,
		    'code.size' 			=> 10104,
        );

		$validator = Validator::make($validata, $rules, $messages);
		if ($validator->fails()) {//验证信息
	    	$messages = $validator->messages();
	    	$result['code'] = $messages->first();
	    	return $result;
	    }

	    //检测验证码
	    $verifyCodeId = UserService::checkVerifyCode($verifyCode, $mobile, UserVerifyCode::TYPE_BANKINFO);
	    if (!$verifyCodeId) {
	    	$result['code'] = 10104;
	    	return $result;
	    }

	    $bankinfo = self::getBankInfo($sellerId);
		
	    //如果未添加过银行卡
	    if(empty($bankinfo)){
	    	$bankObj = new SellerBank();
	    	$bankObj->seller_id 	= $sellerId;
	    	$bankObj->bank 			= $bank;
	    	$bankObj->bank_no 		= $bankNo;
	    
		    $bankObj->save();
	    } else {
	    	
	    	SellerBank::where('seller_id',$sellerId)->update(['bank'=>$bank, 'bank_no'=>$bankNo]);
		}
    	UserVerifyCode::destroy($verifyCodeId);
        return $result; 
	}
	/**
	 * 删除订单
	 * @param  [type] $userId  [description]
	 * @param  [type] $orderId [description]
	 * @return [type]          [description]
	 */
	public static function delete($sellerId, $id) {	   
	    $delete = SellerBank::where('id', $id)->where('seller_id', $sellerId)->delete();
	    return $delete;
	}
}
