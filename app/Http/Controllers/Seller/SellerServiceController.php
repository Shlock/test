<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Goods;
use YiZan\Models\Seller;
use View, Input, Lang, Route, Page, Validator, Session, Response;
/**
 * 服务
 */
class SellerServiceController extends AuthController {
	/**
	 * 服务管理-服务列表
	 */
	public function index() {
		$args = Input::all();  
		$args['type'] = $option['type'] = Goods::SELLER_SERVICE;
		$result = $this->requestApi('goods.lists', $args); 
		if( $result['code'] == 0 ){
			View::share('list', $result['data']['list']);
		} 
        $result_cate = $this->requestApi('goods.cate.lists', $option);   
        View::share('cate', $result_cate['data']); 
		View::share('excel',http_build_query($args));
		return $this->display();
	}

	/**
	 * 服务审核
	 */
	public function audit() {
		$post = Input::all();
        
		$args['status'] = [\YiZan\Models\Base::STATUS_NOT_BY, \YiZan\Models\Base::STATUS_AUDITING];
        
        
        switch(Input::get("status"))
        {
            case "1":
                $args['status'] = [\YiZan\Models\Base::STATUS_NOT_BY];
                break;
                
            case "2":
                $args['status'] = [\YiZan\Models\Base::STATUS_AUDITING];
                break;
            
            default:
                $args['status'] = [\YiZan\Models\Base::STATUS_NOT_BY, \YiZan\Models\Base::STATUS_AUDITING];
                break;
        }
            
		!empty($post['name']) 	?  $args['name']  	= strval($post['name']) 	: null;
		!empty($post['page']) 	?  $args['page'] 	= intval($post['page']) 	: $args['page'] = 1;
		$result = $this->requestApi('goods.lists', $args);
		if($result['code']==0)
			View::share("list",$result['data']['list']);
		$_cate = $this->getcate();
		$cate = [];
		foreach ($_cate as $key => $value) {
			$cate[$value['id']] = $value;
		}
		View::share('cate', $cate);
		return $this->display();
	}

	/**
	 * 服务审核进度
	 */
	public function auditplan() {
		$args = Input::all();
		if( isset($args['id']) ) {
			View::share('data',$result['data']);
		}
		return $this->display();
	}

	/**
	 * 添加服务(取消)
	 */
	public function addgoods() {
		return $this->display();
	}

	/**
	 * 服务种类须知
	 */
	public function quickchoose(){
		$type = (int)Input::get('type');
		$args['name'] = Input::get('name'); 
		$args['page'] = (int)Input::get('page') > 1 ? (int)Input::get('page') : 1; 
		$result = $this->requestApi('system.goods.lists',$args); 
		if( $result['code'] == 0 ){
			View::share('list', $result['data']['list']);
		}
		if($type==0){
			$args['type'] = 1;
		} else {
			$args['type'] = 0;
		}
		View::share('args',$args);
		if($type > 0){
			return $this->display('textchoose');
		}
		return $this->display();	
	} 

	/**
	 * 添加新服务
	 */
	public function create() {   
        $result_cate = $this->requestApi('goods.cate.lists',['type'=>Goods::SELLER_SERVICE]);  
        View::share('cate', $result_cate['data']);
        return $this->display('edit');
	}

	/**
	 * 编辑服务
	 */
	public function edit() { 
        $result_cate = $this->requestApi('goods.cate.lists',['type'=>Goods::SELLER_SERVICE]);   
        View::share('cate', $result_cate['data']); 

		$args['goodsId'] = Input::get('id');
		$result = $this->requestApi('goods.get',$args); 
 		View::share('data', $result['data']);

        $servicestime = $this->requestApi('servicestime.lists',$args); 
        // var_dump($servicestime['data']);
        View::share('stime', $servicestime['data']);
		return $this->display();
	} 

	public function save() {
		$args = Input::all(); 

		if(isset($args['staffIds']) && !empty($args['staffIds'])){
			$args['staffIds'] = explode(',', $args['staffIds']);
		}    

		$args['type'] = Goods::SELLER_SERVICE;

		if( $args['id'] > 0 ){
			$result = $this->requestApi('goods.update',$args);
			$msg = Lang::get('seller.code.98003');
		} else {
			$result = $this->requestApi('goods.create',$args);
			$msg = Lang::get('seller.code.98001');
		} 
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success($msg, u('SellerService/index'));
	}

	public function destroy() {
		$args['id'] = Input::get('id');
		$result = $this->requestApi('goods.delete',$args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('seller.code.98005'), u('Goods/index'), $result['data']);
	}

	public function updateStatus() {
		$args = Input::all();
		$result = $this->requestApi('goods.updateStatus',$args);
		return Response::json($result);
	} 

    public function gettimes()
    {
        $args['goodsId'] = (int)Input::get('id');
        $staffstime = $this->requestApi('servicestime.lists',$args);
        
        return Response::json($staffstime);
    }

    public function showtime()
    {
        $staffstime = $this->requestApi('servicestime.edit',Input::all());
        return Response::json($staffstime);
    }
    /*
    *更新预约时间
    */
    public function updatetime(){
        $args = Input::all(); 
        $data  = $this->requestApi('servicestime.update',$args);
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'), url('Service/index'));
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), url('Service/index'), $data['data']);
    }
     /*
    *添加预约时间
    */
    public function addtime(){
        $args = Input::all(); 
        $data  = $this->requestApi('servicestime.add',$args);
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'), url('Service/index'));
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), url('Service/index'), $data['data']);
    }
     /**
     * [destroy 删除时间]
     */
    public function deldatatime(){
        $args = Input::all();
        //var_dump($args);
        $result = $this->requestApi('servicestime.delete',$args);
        return Response::json($result);
    }
}
