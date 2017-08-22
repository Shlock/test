<?php 
namespace YiZan\Http\Controllers\Seller;
use View, Input, Lang, Route, Page, Validator, Response;
/**
 * 银行卡
 */
class BankController extends AuthController {

	public function index() {
          $sellers  = session::get('seller');
	      $bankinfo = $this->requestApi('bankinfo.get',array('id'=>$sellers['id']));
          if(empty($bankinfo['data']['banks'][0]['sellerId'])){
               return redirect('Bank/noInfo');exit;
           }
           View::share('list',$bankinfo['data']);
		   return $this->display();
	}

    //获取验证码
    public function getVerify(){
        $args = Input::all();
        $result = $this->requestApi('bankinfo.bankinfoverify',$args);
        return Response::json($result);
    }

    //添加银行信息
    public function addInfo(){
        return $this->display();
    }

    //执行添加银行信息
    public function doAddInfo(){
        $res = Input::all();
        $sellers = session::get('seller');
        $res['sellerId']=$sellers['id'];
        $result = $this->requestApi('bankinfo.addinfo',$res);
        if($result['code']==0){
            return $this->success('操作成功',u('bank/index'));exit;
        }
        return Response::json($result);

    }
    //验证验证码
    public function checkVerify(){
        $res = Input::all();
        $result = $this->requestApi('bankinfo.checkVerify',$res);
        return Response::json($result);
    }
    //银行卡编辑页面
    public function edit(){
        $sellers  = session::get('seller');
        $bankinfo = $this->requestApi('bankinfo.get',array('id'=>$sellers['id']));
        View::share('data',$bankinfo['data']['banks'][0]);
        return $this->display();
    }
   //银行卡信息编辑提交
    public function editBank(){
        $rese = Input::all();
        $sellers  = session::get('seller');
        $rese['sellerId'] = $sellers['id'];
        $result = $this->requestApi('bankinfo.edit',$rese);
        if($result['code']==0) {
            return $this->success('保存成功', u('bank/index'));exit;
        }
        return Response::json($result);

    }

    //无银行卡页面
    public function noInfo(){
        return $this->display();
    }






	/**
	 * 提现申请
	 */
	public function ajaxwithdraw() {
		$args = Input::all();
		$result = $this->requestApi('useraccount.withdraw',$args);
		return Response::json($result);
	}

	/**
	 * 添加银行卡
	 */
	public function create() {
		View::share('title','添加银行卡');
		return $this->display('edit');
	}
	/**
	 * 保存银行卡
	 */
	public function save() {
	    $args = Input::all();
		$result = $this->requestApi('bankinfo.save', $args); 
		if ($result['code'] == 0){
	       return $this->success($result['msg'], u('Bank/index'));		    
		}else{
		    return $this->error($result['msg'], u('Bank/index'));
		}
	}
	/**
	 * 更改银行卡
	 */
	public function changebankcard() { 
		$result = $this->requestApi('bankinfo.get'); 
		if($result['code']==0){
			View::share('data',$result['data']);		 	
		} 
		return $this->display();
	}
	
	public function updabankcard() { 
		$args = Input::all();
		$result = $this->requestApi('bankinfo.update',$args); 
		return Response::json($result);
	}
	/*验证码*/
	public function bankverify() { 
		$args = Input::all();
		$result = $this->requestApi('bankinfo.bankinfoverify',$args); 
		return Response::json($result);
	}
	/*验证码*/
	public function userverify() { 
		$args = Input::all();
		$result = $this->requestApi('useraccount.withdrawverify',$args); 
		return Response::json($result);
	}
	/**
	 * 删除订单
	 */
	public function destroy() {
	    $args = Input::all();
	    if( !empty( $args['id'] ) ) {
	        $result = $this->requestApi('bankinfo.destroy',$args);
	    }
	    if( $result['code'] > 0 ) {
	        return $this->error($result['msg']);
	    }
	    return $this->success(Lang::get('admin.code.98005'), u('Bank/index'), $result['data']);
	}
}
