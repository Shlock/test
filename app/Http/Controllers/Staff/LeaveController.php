<?php 
namespace YiZan\Http\Controllers\Staff;
use YiZan\Utils\Time;
use View, Input, Lang, Route, Page ,Session;
/**
 * 请假
 */
class LeaveController extends AuthController {

	public function __construct() {
		parent::__construct();
		View::share('nav','schedule');
		View::share('is_show_top',true);
	}
	
	/**
	 * 请假列表
	 */
	public function index() {
        $args = Input::all();
        $data = $this->requestApi('staffleave.lists', $args);
        if ($data['code'] == 0) {
            View::share('list', $data['data']);
        }
        View::share('args',$args);
        if(Input::ajax()){
            return $this->display('item');
        }else{
            return $this->display();
        }
	}

    /**
     * 请假详情
     */
	public function detail() {
        $data = $this->requestApi('staffleave.detail', ['id'=>(int)Input::get('id')]);
        $config = $this->requestApi('app.init');
        if ($data['code'] == 0) {
            View::share('data', $data['data']);
        }
        View::share('config', $config['data']);
        return $this->display();
    }

    /**
     * 删除请假记录
     */
    public function delete() {
        $result = $this->requestApi('staffleave.delete', ['ids'=>[(int)Input::get('id')]]);
        die(json_encode($result));
    }

    /**
     * 请假
     */
    public function add() {
        return $this->display();
    }

    /**
     * 创建请假
     */
    public function create(){
        $args = Input::get();
        $args['beginTime'] = Time::toTime($args['beginTime']);
        $args['endTime'] = Time::toTime($args['endTime']);
        $result = $this->requestApi('staffleave.create', $args);
        die(json_encode($result));
    }
}
