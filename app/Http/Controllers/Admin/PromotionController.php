<?php
namespace YiZan\Http\Controllers\Admin;

use YiZan\Http\Requests\Admin\PromotionPostRequest;

use View, Input, Form, Lang, Response;
/**
 * 优惠券管理
 */
class PromotionController extends AuthController {
	/**
	 * 优惠券列表
	 */
	public function index() {
		$result = $this->requestApi('promotion.lists', Input::all());
		if($result['code']==0) {
			View::share('list', $result['data']['list']);
		}
		return $this->display();
	}

	/**
	 *编辑优惠券
	 */
	public function edit(){
		$result = $this->requestApi('promotion.get', Input::all());
		if($result['code'] > 0) {
			return $this->error($result['msg']);
		}
		View::share('data', $result['data']);

        $cateIds = $this->requestApi('seller.cate.catesall');
        //print_r($cateIds);exit;
        $isSelected = [];
        foreach($result['data']['sellerCates'] as $val) {
            $isSelected[] = $val['cates']['id'];
        }
        if($cateIds['code'] == 0)
            foreach($cateIds['data'] as $k=>$v) {
                if (count($v['childs']) > 0) {
                    $cateIds['data'][$k]['isHasChild'] = true;
                }
                if (in_array($v['id'],$isSelected)) {
                   unset($cateIds['data'][$k]);
                }
                foreach ($v['childs'] as $key=>$val) {
                    if (in_array($val['id'],$isSelected)) {
                        unset($cateIds['data'][$k]['childs'][$key]);
                    }
                }
            }
        View::share('isSelected',$isSelected);
        View::share('cateIds',$cateIds['data']);
		return $this->display();
	}

	/**
	 *创建优惠券
	 */
	public function create(){
        $cateIds = $this->requestApi('seller.cate.catesall');
        if($cateIds['code'] == 0)
            View::share('cateIds',$cateIds['data']);
		return $this->display("edit");
	}

	/**
	 *添加/编辑优惠券
	 *PromotionPostRequest $request
	 */
	public function save(){
        $args = Input::get();
		$result = $this->requestApi("promotion.save",$args);
		if($result['code'] > 0) {
			return $this->error($result['msg'], u('Promotion/index'));
		}
		return $this->success($result['msg']);
	}

    /**
     * 删除优惠券
     */
    public function destroy() {
        $args = Input::get();
        $result = $this->requestApi("promotion.delete",$args);
        if($result['code'] > 0) {
            return $this->error($result['msg']);
        }
        return $this->success($result['msg']);
    }

    /**
     * 搜索商家
     */
    public function searchSeller() {
        $result = $this->requestApi('seller.search',Input::get());
        return Response::json($result);
    }

    /**
     * 发放优惠券
     */
    public function sendsn() {
        return $this->display();
    }

    /**
     * 发放
     */
    public function send() {
        $result = $this->requestApi('promotion.send',Input::get());
        if($result['code'] > 0) {
            return $this->error($result['msg']);
        }
        return $this->success($result['msg'], u('PromotionSn/index'));
    }
	
	    /**
     * 搜索会员
     * 根据会员Id 获取会员信息
     */
    public function searchUser() {
        /*获取会员接口*/
        $list = $this->requestApi('user.search', Input::all());
        return Response::json($list['data']);
    }

}
