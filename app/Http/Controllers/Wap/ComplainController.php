<?php 
namespace YiZan\Http\Controllers\Wap;
use Input, View, Response;
/**
 * 优惠券控制器
 */
class ComplainController extends UserAuthController {

	public function __construct() {
		parent::__construct();
	}
	/**
	 * 服务举报
	 */
	public function goods() {
		$id = (int)Input::get('id');
		if ($id < 1) {
			return $this->error('请选择要举报的服务');
		}
		View::share('seo_title','服务举报');
		return $this->display();
	}

	/**
	 * [dogoods 执行服务举报]
	 */
	public function dogoods() {
		$id = (int)Input::get('id');
		$content = strip_tags(Input::get('content'));
		$args = array(
			'goodsId' => $id,
			'content' => $content
		);
		$result = $this->requestApi('goodscomplain.create',$args);
		return Response::json($result);
	}

	/**
	 * 服务机构举报
	 */
	public function seller() {
		$id = (int)Input::get('id');
		if ($id < 1) {
			return $this->error('请选择要举报的服务机构');
		}
		View::share('seo_title','服务机构举报');
		return $this->display();
	}

	/**
	 * [doseller 执行服务机构举报]
	 */
	public function doseller() {
		$id = (int)Input::get('id');
		$content = strip_tags(Input::get('content'));
		$args = array(
			'sellerId' => $id,
			'content' => $content
		);
		$result = $this->requestApi('sellercomplain.create',$args);
		return Response::json($result);
	}

    /**
     * 服务人员举报
     */
    public function staff() {
        $id = (int)Input::get('id');
        if ($id < 1) {
            return $this->error('请选择要举报的服务人员');
        }
        View::share('seo_title','服务人员举报');
        return $this->display();
    }

    /**
     * [doseller 执行服务人员举报]
     */
    public function dostaff() {
        $id = (int)Input::get('id');
        $content = strip_tags(Input::get('content'));
        $args = array(
            'staffId' => $id,
            'content' => $content
        );
        $result = $this->requestApi('staffcomplain.create',$args);
        return Response::json($result);
    }

    /**
     * 订单举报
     */
    public function order() {
        $id = (int)Input::get('id');
        if ($id < 1) {
            return $this->error('请选择要举报的订单');
        }
        $result = $this->requestApi('order.detail',array('orderId' => $id));
        if ($result['code'] == 0) {
        	View::share('data',$result['data']);
        }
        View::share('seo_title','订单举报');
        return $this->display();
    }
	
	/**
     * [doorder 执行订单举报]
     */
    public function doorder() {
        $option = Input::all();
        $result = $this->requestApi("Ordercomplain.create",$option);
		die(json_encode($result));
    }
}