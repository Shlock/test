<?php 
namespace YiZan\Http\Controllers\Seller;

use YiZan\Models\Goods;
use YiZan\Models\Seller;
use YiZan\Utils\Time;
use View, Input, Lang, Route, Page, Validator, Session, Response, Redirect;
/**
 * 员工日程
 */
class StaffScheduleController extends AuthController {
    protected $staffType;
    public function __construct() {
        parent::__construct();
        $this->staffType = 2;//服务类型
        if ($this->sellerId < 1) {//如果未登录时,则退出
            Redirect::to(u('Public/login'))->send();
        }
        View::share('login_seller', $this->seller);
        //验证权限
    }
	/**
	 * 员工管理-员工列表
	 */
	public function index() {
        $args = Input::all();
        $args['type'] = $this->staffType;
        //var_dump($args);
        $result = $this->requestApi('sellerstaff.lists', $args);
        // dd($result);
        if( $result['code'] == 0 ){
            View::share('list', $result['data']['list']);
        }
        View::share('args', $args);
		return $this->display();
	}

    /**
     * [edit 人员日程]
     */
    public function edit(){
        $args['id'] = (int)Input::get('id');
        if ($args['id'] > 0) {
            $data = $this->requestApi('sellerstaff.get',$args);
            if($data['code'] == 0)
            View::share('data', $data['data']);

            $result = $this->requestApi('sellerstaff.getstaffschedule',$args);
            if($result['code'] == 0)
            View::share('schedule_date', $result['data']);
        }
        //print_r($result['data']);
        View::share('days', 7);
        return $this->display();
    }

}
