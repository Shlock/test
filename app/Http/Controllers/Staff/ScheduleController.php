<?php 
namespace YiZan\Http\Controllers\Staff;

use View, Input, Lang, Route, Page ,Session,Time;
/**
 * 日程安排
 */
class ScheduleController extends AuthController {

	public function __construct() {
		parent::__construct();
		View::share('nav','schedule');
		View::share('is_show_top',true);
	}
	
	/**
	 * 日程列表
	 */
	public function index() {
        $args = Input::get();
        $data = $this->requestApi('order.wapschedule', $args);
        if ($data['code'] == 0) {
            View::share('daylist', $data['data']['daylist']);
            View::share('list', $data['data']['list']);
            View::share('date', $data['data']['date']);
        }
		return $this->display();
	}

    /**
     * 日程详情
     */
	public function detail() {
        $data = $this->requestApi('order.scheduledetail', ['id'=>(int)Input::get('id')]);
        if ($data['code'] == 0) {
            View::share('data', $data['data']);
        }
        return $this->display();
    }
    /*统计*/
    public function statistics() {
        $month['month'] = Input::get('month');        
        if(!$month['month'])
        {
            $month['month'] = Time::toDate(UTC_TIME, 'Ym');
            View::share('month', Time::toDate(UTC_TIME, 'Y年m月') );

        }else{
            
            View::share('month', $month['month'] );
        }
        $data = $this->requestApi('statistics.detail',$month);
            View::share('list', $data['data']);
        return $this->display();
    }
    /*按月统计*/
    public function month() {
        $list = $this->requestApi('statistics.month');
            View::share('list', $list['data']);
        return $this->display();
    }

    

}
