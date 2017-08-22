<?php namespace YiZan\Http\Controllers\Staff;

use YiZan\Http\Controllers\YiZanViewController;
use View, Input;

abstract class BaseController extends YiZanViewController {
	/**
	 * API调用类型
	 * @var string
	 */
	protected $apiType = 'staff';

	/**
	 * 调用模板
	 * @var string
	 */
	protected $tpl = 'staff.run';

	/**
	 * 员工信息
	 * @var array
	 */
	protected $staff;

	/**
	 * 员工编号
	 * @var int
	 */
	protected $staffId = 0;

    protected $getToken = '';


	/**
	 * 初始化信息
	 */
	public function __construct() {
		parent::__construct();
		//设置员工
		$this->setStaff(Session::get('staff'));
        if (Input::get('token') != '') {
            $this->getToken = Input::get('token');
        }
        // $this->staffId = Input::get('staffId');
	}

	/**
	 * 设置员工
	 * @param array $staff 员工信息
	 */
	protected function setStaff($staff) {
		if (!empty($staff)) {
			$this->staff 	= $staff;
			$this->staffId 	= $staff['id'];
		}
		Session::set('staff', $staff);
		Session::save();
		
	} 

	/**
	 * 调用API
	 * @param  string 	$method 接口名称
	 * @param  array  	$args   参数
	 * @param  array  	$data   提交数据
	 * @return array          	API返回数据
	 */
	protected function requestApi($method, $args = [], $data = []){
		$data['staffId'] = $this->staffId;
		return parent::requestApi($method, $args, $data);
	}
}
