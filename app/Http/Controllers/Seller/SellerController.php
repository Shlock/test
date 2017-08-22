<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Seller;
use View, Input, Lang, Route, Page, Validator, Response;
/**
 * 服务人员
 */
class SellerController extends AuthController {

	public function index() {   	  
		$data = $this->requestApi('user.get');    
		//print_r($data['data']); 
		foreach ($data['data']['deliveryTimes'] as $key => $value) {
			$time[] = $value['stime'] . '-' . $value['etime'];
			$deliveryTime = implode(',', $time);
		}
		$data['data']['deliveryTime'] = $deliveryTime;
		if($data['code'] == 0){
			View::share('list', $data['data']); 			
		} 
		$seller_data = $this->requestApi('user.lists');
		if($seller_data['code'] == 0){
			View::share('list_data', $seller_data['data']); 			
		}
	    $hours = $this->requestApi('schedule.staffLists');
	    if($hours['code'] == 0){
	    	View::share('hours', $hours['data']);
	    }
	    //print_r($data);
		return $this->display();
	}

	/**
	 * 修改手机号
	 */
	public function changetel() {  
		$data = $this->requestApi('user.get');     
		if($data['code'] == 0){
			View::share('data', $data['data']); 			
		} 	
		return $this->display();
	}

	/*
	*	基本信息获取页面
	*
	*/
	public function basic() {  
		$data = $this->requestApi('user.get');   
		//$data['data']['deliveryTime'] = explode('-', $data['data']['deliveryTime']);  
		if($data['code'] == 0){
			View::share('data', $data['data']); 			
		} 
		
		$cateIds = $this->requestApi('sellerstaff.cateall');
        if($cateIds['code'] == 0)
            View::share('cateIds',$cateIds['data']);	
		if($data['code'] == 0){
            $_cateIds = array_map('array_shift', $data['data']['sellerCate']); 
            View::share('_cateIds',$_cateIds);
        }
        $staffstime = $this->requestApi('staffstime.lists',$args);
        //var_dump($staffstime);
        if($staffstime['code'] == 0)
            View::share('stime', $staffstime['data']);

		return $this->display();
	}
	/*
	*	基本信息修改
	*
	*/
	public function updatebasic() {
		$args = Input::all();
		$detime['stimes'] =  $args['_stime'];
        $detime['etimes'] = $args['_etime'];
        if (count($detime['stimes']) != count($detime['etimes'])) {
           return $this->error('配送时间没填写完整');
        }
        if (count($detime['stimes']) > 3 || count($detime['etimes']) > 3) {
            return $this->error('配送时间段最多可设置三个');
        }
        $args['deliveryTime'] = json_encode($detime); 
        // var_dump($args);
        // exit;
		$data = $this->requestApi('user.update',$args);    
		if($data['code'] == 0){
			return $this->success($data['msg'] ? $data['msg'] :"更新成功" ,u("Seller/basic"));
		}else{ 
			return $this->error($data['msg'] ? $data['msg'] :"更新失败",u("Seller/basic"));
		}
	}

	/*
	*	纸质认证获取页面
	*
	*/
	public function qualification() {  
		$data = $this->requestApi('user.get');     
		if($data['code'] == 0){
			if ($data['data']['isAuthenticate'] == 1) {
				return $this->error("已认证的身份信息不能修改",u("Seller/index"));
			}
			View::share('data', $data['data']); 			
		} 	
		return $this->display();
	}

	/*
	*	纸质认证修改
	*
	*/
	public function updaqualification() {
		$args = Input::all(); 
		$args['sellerId'] = $args['id'];
		$data = $this->requestApi('user.certificate',$args); 
		/*返回处理*/
		if($data['code'] == 0){
			return $this->success($data['msg'] ? $data['msg'] :"更新成功" ,u("Seller/index"));
		}else{ 
			return $this->error($data['msg'] ? $data['msg'] :"更新失败",u("Seller/qualification"));
		}
	}

	/*
	*	预约时间页面
	*
	*/
	public function subscribe() { 
		return $this->display();
	} 
	public function gettimes()
    {
        $args['id'] = (int)Input::get('id');
        $staffstime = $this->requestApi('staffstime.lists',$args);
        
        return Response::json($staffstime);
    }
    public function showtime()
    {
        $staffstime = $this->requestApi('staffstime.edit',Input::all());
        return Response::json($staffstime);
    }

    /*
    *更新预约时间
    */
    public function updatetime(){
        $args = Input::all();
        $data  = $this->requestApi('staffstime.update',$args);
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'), url('Seller/basic'));
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), url('Seller/basic'), $data['data']);
    }
     /*
    *添加预约时间
    */
    public function addtime(){
        $args = Input::all();
        // var_dump($args);
        // exit;
        $data  = $this->requestApi('staffstime.add',$args);
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'), url('Seller/basic'));
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), url('Seller/basic'), $data['data']);
    }
     /**
     * [destroy 删除时间]
     */
    public function deldatatime(){
        $args = Input::all();
        //var_dump($args);
        $result = $this->requestApi('staffstime.delete',$args);
        return Response::json($result);
    }

	/*
	*获取
	*/
	public function schedule(){ 
		$args = Input::all();
		$hours = $this->requestApi('user.lists',$args);   
		return Response::json($hours);
	}
	/*
	*	其他设置
	*
	*/
	public function rest() {
		$data = $this->requestApi('user.get');   
		
		if($data['code'] == 0){
			View::share('data', $data['data']); 			
		}    
		return $this->display();
	}
	/*
	*	其他设置修改
	*
	*/
	public function updaterest() {
		$args = Input::all(); 
		$data = $this->requestApi('user.moreset',$args);     
		/*返回处理*/
		if($data['code'] == 0){
			return $this->success($data['msg'] ? $data['msg'] :"更新成功" ,u("Seller/rest"));
		}else{ 
			return $this->error($data['msg'] ? $data['msg'] :"更新失败",u("Seller/rest"));
		}

	}

	/**
	 * 修改密码
	 */
	public function changepwd() {
		$data = $this->requestApi('user.get');     
		if($data['code'] == 0){
			View::share('data', $data['data']); 			
		} 	
		return $this->display();
	}
	/**
	 * 修改密码
	 */
	public function updatepwd() {
		$args = Input::all(); 	
		$args['sellerId'] = $args['id'];
		$args['type'] = "change";
		$data = $this->requestApi('user.changepwd',$args);  
		if($data['code']==0){
			$args = [];
			$this->requestApi('user.logout',$args);
			Session::put('seller_token', null);
			Session::put('seller', null);
			return $this->success($data['msg']?$data['msg']:"修改密码成功", u('Seller/changepwd'), $data['data']);
		}else{ 
			return $this->error($data['msg']?$data['msg']:"修改密码失败", u('Seller/changepwd'));
		}
	}
	/**
	 * 修改手机号
	 */
	public function updatetel() {
		$args = Input::all(); 	 
		$args['sellerId']  = $args['id'];
		$data = $this->requestApi('user.changetel',$args);   
		if($data['code']==0){
			return $this->success($data['msg'] ? $data['msg'] :"更换成功", u('Seller/changetel'));
		}else{ 
			return $this->error($data['msg'] ? $data['msg'] :"更换失败", u('Seller/changetel'));
		}
	} 

	/**
	 * 获取员工信息
	 */
	public function search(){ 
		/*获取会员接口*/		
		$list = $this->requestApi('user.search', Input::all()); 
		return Response::json($list['data']);
	}
}
