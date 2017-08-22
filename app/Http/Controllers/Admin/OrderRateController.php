<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\OrderRate;
use YiZan\Utils\String;
use View, Input, Lang, Route, Page;
/**
 * 服务评价管理
 */
class OrderRateController extends AuthController {
	/**
	 * 评价管理-评价列表
	 */
	public function index() {
		$post = Input::all();
		!empty($post['userMobile']) ? $args['userMobile'] = strval($post['userMobile']) : null;
		!empty($post['goodsName']) ? $args['goodsName'] = strval($post['goodsName']) : null;
		!empty($post['sellerMobile']) ? $args['sellerMobile'] = strval($post['sellerMobile']) : null;
		!empty($post['staffMobile']) ? $args['staffMobile'] = strval($post['staffMobile']) : null;
		!empty($post['orderSn']) ? $args['orderSn'] = strval($post['orderSn']) : null;
		!empty($post['beginTime']) ? $args['beginTime'] = strval($post['beginTime']) : null;
		!empty($post['endTime']) ? $args['endTime'] = strval($post['endTime']) : null;
		!empty($post['result']) ? $args['result'] = strval($post['result']) : null;
		!empty($post['replyStatus']) ? $args['replyStatus'] = intval($post['replyStatus']) : null;
		!empty($post['page']) ? $args['page'] = intval($post['page']) : $args['page'] = 1;
		$result = $this->requestApi('order.rate.lists',$args); 
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']);
		}
		return $this->display();
	}

	/**
	 * 编辑会员评价/商家回复
	 */
	public function detail(){
		$args = Input::all();
		$result = $this->requestApi('order.rate.get', $args);
		View::share('data', $result['data']);
		// print_r($result['data']);exit;
		return $this->display();
	}

	/**
	 * 保存管理员编辑的会员评价/商家回复
	 */
	public function saveRate(){
		$args = Input::all(); 
		$result = $this->requestApi('order.rate.save', $args);
		if($result['code'] == 0){
			return $this->success($result['msg'], u('OrderRate/index'));
		} 
		return $this->error($result['msg']);
	}

	/**
	 * 评价管理-评价回复
	 */
	public function rateReply() {
		$args = Input::all();
		if(empty($args['id'])) return $this->error(Lang::get("admin.noId"));
		if(empty($args['content'])) return $this->error(Lang::get("admin.code.23015"));
		$result = $this->requestApi('order.rate.reply',$args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get("admin.code.23016"), u('OrderRate/index'), $result['data']);
	}

	public function replypage() {
		$this->display();
	}

	/**
	 * 评价管理-删除评价
	 */
	public function destroy() {
		$args = Input::all();
		if( !empty( $args['id'] ) ) {
			$result = $this->requestApi('order.rate.delete',$args); 
		}

		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98005'), u('OrderRate/index'), $result['data']);
	}
}
