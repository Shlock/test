<?php 
namespace YiZan\Http\Controllers\Admin; 

use YiZan\Http\Requests\Admin\ActivityCreatePostRequest;
use Input, View, Lang;

/**
 * 优惠活动管理
 */
class ActivityTicketController extends AuthController {
	/**
	 * 活动列表
	*/
	public function index() {
		$result = $this->requestApi('activity.lists', Input::all());
		if( $result['code'] == 0 )
			View::share('list', $result['data']['list']);
		return $this->display();
	}

	/**
	 *编辑促销活动
	 */
	public function edit(){
		$result = $this->requestApi('activity.get', Input::all());
		if($result['code'] > 0) {
			return $this->error($result['msg']);
		}

		$ticket = [];
		$ticketName = [
			'offset'=>'抵用券',
			'discount'=>'洗车券',
			'money'=>'优惠券',
		];
		$lists = $this->requestApi('Promotion.lists');
		$ticketall = $lists['data']['list'];
		foreach ($ticketall as $key => $value) {
			if($value['conditionType']=='all'){
				$conditionType = '全车洗';
			}
			elseif($value['conditionType']=='body'){
				$conditionType = '洗车身';
			}
			else{
				$conditionType = '';
			}
			@$ticketall[$key]['ticketName'] = '￥'.$value['data'].' '.$conditionType.$ticketName[$value['type']];
			if(in_array($value['type'], ['offset','discount'])){
				$ticket[] = $ticketall[$key];
			}

		}

		View::share('data', $result['data']); 
		View::share('ticketall',$ticketall);
		View::share('ticket',$ticket);
		return $this->display();
	}

	/**
	 *创建促销活动
	 */
	public function create(){
		$ticket = [];
		$ticketName = [
			'offset'=>'抵用券',
			'discount'=>'洗车券',
			'money'=>'优惠券',
		];
		$result = $this->requestApi('Promotion.lists');
		$ticketall = $result['data']['list'];
		foreach ($ticketall as $key => $value) {
			if($value['conditionType']=='all'){
				$conditionType = '全车洗';
			}
			elseif($value['conditionType']=='body'){
				$conditionType = '洗车身';
			}
			else{
				$conditionType = '';
			}
			@$ticketall[$key]['ticketName'] = '￥'.$value['data'].' '.$conditionType.$ticketName[$value['type']];
			if(in_array($value['type'], ['offset','discount'])){
				$ticket[] = $ticketall[$key];
			}

		}
		View::share('ticketall',$ticketall);
		View::share('ticket',$ticket);
		return $this->display("edit");
	}
	
	/**
	 *保存活动
	 */
	public function saveActivity(ActivityCreatePostRequest $request){
		$args = Input::all();
		$args['status'] = 1;
		$args['sellerId'] = 0;
		$result = $this->requestApi("activity.saveActivity",$args);

		if($result['code'] > 0) {
			return $this->error($result['msg']);
		}
		return $this->success($result['msg'], u('ActivityTicket/index'));
	} 

	/**
	 * 删除活动
	*/
	public function destroy() {
		$data = $this->requestApi('Activity.delete', Input::all());
		if( $data['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success();
	}

	

	
}
