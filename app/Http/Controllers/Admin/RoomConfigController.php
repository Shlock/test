<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Utils\Time;
use View, Input, Response, Lang;
/**
 * 送餐配置
 */
class RoomConfigController extends AuthController {
	/**
	 * 送餐配置
	 */
	public function index() {
		$args['groupCode'] = "run";
		$result = $this->requestApi('system.config.get',$args);

		if( $result['code'] == 0 ) {
			foreach ($result['data'] as $key => $value) {
				$data[$value['code']] = $value['val'];
			}
			//拆分时间
			$run_subscribe_send_endtime = explode(':', $data['run_subscribe_send_endtime']);
			$run_subscribe_lunch_endtime = explode(':', $data['run_subscribe_lunch_endtime']);
			$run_subscribe_send_begintime = explode(':', $data['run_subscribe_send_begintime']);
			$run_subscribe_lunch_begintime = explode(':', $data['run_subscribe_lunch_begintime']);
	        $data['run_subscribe_send_endtime_hour'] = $run_subscribe_send_endtime[0];
	        $data['run_subscribe_send_endtime_minute'] = $run_subscribe_send_endtime[1];
	        $data['run_subscribe_lunch_endtime_hour'] = $run_subscribe_lunch_endtime[0];
	        $data['run_subscribe_lunch_endtime_minute'] = $run_subscribe_lunch_endtime[1];
	        $data['run_subscribe_send_begintime_hour'] = $run_subscribe_send_begintime[0];
	        $data['run_subscribe_send_begintime_minute'] = $run_subscribe_send_begintime[1];
	        $data['run_subscribe_lunch_begintime_hour'] = $run_subscribe_lunch_begintime[0];
	        $data['run_subscribe_lunch_begintime_minute'] = $run_subscribe_lunch_begintime[1];
	        View::share('data', $data);
		}
		//获取时分秒
        $time = Time::getHouerMinuteSec(true, true, false);
        View::share('time', $time);
		return $this->display();
	}

	public function save() {
		$data = Input::all();
		
		$i = 0;
		$args = [];
		$data['run_subscribe_send_endtime'] = $data['run_subscribe_send_endtime_hour'].':'.$data['run_subscribe_send_endtime_minute'];
		$data['run_subscribe_lunch_endtime'] = $data['run_subscribe_lunch_endtime_hour'].':'.$data['run_subscribe_lunch_endtime_minute'];
		$data['run_subscribe_send_begintime'] = $data['run_subscribe_send_begintime_hour'].':'.$data['run_subscribe_send_begintime_minute'];
		$data['run_subscribe_lunch_begintime'] = $data['run_subscribe_lunch_begintime_hour'].':'.$data['run_subscribe_lunch_begintime_minute'];

		if(intval(str_replace(":", "", $data['run_subscribe_lunch_endtime'])) - intval(str_replace(":", "", $data['run_subscribe_lunch_begintime'])) < 1) {
			return $this->error(Lang::get('admin.system.10207'));
			die;
		}
		if(intval(str_replace(":", "", $data['run_subscribe_send_endtime'])) - intval(str_replace(":", "", $data['run_subscribe_send_begintime'])) < 1) {
			return $this->error(Lang::get('admin.system.10208'));
			die;
		}

		foreach ($data as $key => $value) {
			$args[$i]['code'] = $key;
			$args[$i]['val'] = $value;
			$i++;
		}
		$_args['configs'] = $args;
		$result = $this->requestApi('system.config.update',$_args);
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('RoomConfig/index'), $result['data']);
	}
}
