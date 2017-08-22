<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\OrderConfig;
use View, Input, Lang, Route, Page, Form;
/**
 * 订单配置管理
 */
class OrderConfigController extends AuthController {
	public function index(){
		$args = Input::all();
		$args['groupCode'] = "order_config";
		$result = $this->requestApi('system.config.get',$args);
		if( $result['code']==0 ) {
			foreach ($result['data'] as $key => $value) {
				if( $value['code'] === 'system_order_pass' ) {
					$result['data'][$key]['val'] /= 60;
				}
			}
			//var_dump($result);
			if( $result['code'] == 0 ) {
				View::share('list', $result['data']);
			}
		}
		return $this->display();
	}


	public function save(){
		$data = Input::all();
		/*if($data['staff_deduct_type']==2) {
			if($data['staff_deduct_value']<0 || $data['staff_deduct_value']>100) {
				return $this->error(Lang::get('admin.code.27017'));
			}
		}*/
		$i = 0; 
		foreach ($data as $key => $value) {
			/*if( $key === 'system_order_confirm' ) {
				$value *= 3637440;
			}*/
			if( $key === 'system_order_pass' ) {
				$value *= 60;
			}
			$args[$i]['code'] = $key;
			$args[$i]['val'] = $value;
			$i++;
		}
		$_args['configs'] = $args;
		$result = $this->requestApi('system.config.update',$_args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('OrderConfig/index'), $result['data']);
	}

}
