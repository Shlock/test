<?php 
namespace YiZan\Http\Controllers\Wap;

use View, Input;

/**
 * 评价
 */
class AssessController extends BaseController {

	public function __construct() {
		parent::__construct();
		View::share('nav','index');
	}  

	/**
	 *  评价列表
	 */
	public function index(){
		$option = Input::all();
		if(!isset($option['type'])){
			$option['type'] = 0;
		} 
		$assece_data = $this->requestApi('rate.staff.lists',$option); 
		if ($assece_data['code'] == 0) {
			View::share('data',$assece_data['data']);  
		}

		if (Input::ajax()) {
			return $this->display('assess_item');
		} else {
			if ($option['staffId'] > 0) {
				$seller_data = $this->requestApi('staff.detail',$option); 
				if ($seller_data['code'] == 0) {
					View::share('count_data',$seller_data['data']['extend']);
				}
			} else {
				$goods_data = $this->requestApi('goods.detail',$option);
				if ($goods_data['code'] == 0) {
					View::share('count_data',$goods_data['data']['extend']);
				}
			}
			View::share('option',$option);
			return $this->display();
		}
	} 
}
