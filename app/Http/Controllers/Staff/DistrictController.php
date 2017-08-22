<?php 
namespace YiZan\Http\Controllers\Staff;

use View, Input, Lang, Route, Page ,Session;
/**
 * 首页
 */
class DistrictController extends BaseController {


	public function __construct() {
		parent::__construct();
		View::share('nav','index');
		View::share('is_show_top',false);
	}
	

	public function index() {
        $data = $this->requestApi('district.detail');
        View::share('data', $data['data']);
		return $this->display();
	}

    /**
     * 服务商圈设置
     */
    public function district() {
        $list = $this->requestApi('district.lists');
        View::share('list', $list['data']);
        return $this->display();
    }

    /**
     * 设置服务商圈
     */
    public function setdistrict() {
        $ids = explode(',',Input::get('ids'));
        $result = $this->requestApi('district.create',array('districtIds'=>$ids));
        die(json_encode($result));
    }
    /**
     * 服务范围设置
     */
    public function range() {
        $data = $this->requestApi('district.detail');
        View::share('data', $data['data']);
        return $this->display();
    }


    /**
     * 设置服务范围
     */
    public function setrange() {
        $mapPos = Input::get('mapPos');
        $result = $this->requestApi('district.range',array('mapPos'=>$mapPos));
        die(json_encode($result));
    }

}
