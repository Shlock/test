<?php 
namespace YiZan\Http\Controllers\Wap;

use View, Input, Lang, Route, Page;
/**
 * 地址
 */
class AddressController extends BaseController {

	//
	public function __construct() {
		parent::__construct();
		View::share('nav','index');
		View::share('is_show_top',false);
	}

	/**
	 *  会员地址列表
	 */
	public function index(){
		$addressList_data = $this->requestApi('user.address.lists'); 
		if($addressList_data['code'] == 0) {
			View::share('addressList_data', $addressList_data['data']);
		}

		$reservation_data = Session::get('reservation_data');
		View::share('reservation_data', $reservation_data);

		if(empty($addressList_data['data']) && empty($reservation_data['address'])){ 
			return redirect('Address/addtoaddr');
		} else { 
			return $this->display();
		}
	} 

	/**
	 * 地址选择
	 */
	public function addtoaddr(){
        $reservation_data = Session::get('reservation_data');
        if (isset($reservation_data['staff']) && (int)$reservation_data['staff'] > 0) {
            $reservation_data['staff'] = 58;
            $staff = $this->requestApi('staff.detail',array('staffId' => $reservation_data['staff']));
            View::share('map_pos',$staff['data']['district']);
            View::share('staff_id',$reservation_data['staff']);
        }
		$addressList_data = $this->requestApi('user.address.lists'); 
		if($addressList_data['code'] == 0) {
			View::share('addressList_data', $addressList_data['data']);   
		}
		return $this->display();
	}

    /**
     * 检测定位点是否在当前选定服务人员服务范围内
     */
    public function checkMapPos(){
        $args = [
            'mapPoint' => Input::get('map_point'),
            'staffId'  => (int)Input::get('staff_id')
        ];
        $result = $this->requestApi('staff.checkmappos',$args);
        die(json_encode($result));

    }
	/**
	 * 会员地址操作
	 */
	public function set(){
		$option = Input::all();   
		$json_result = array(
			'code'=>0,
			'data'=>'',
			'msg'=>''
		);
		
		if(empty($option) || empty($option['address']) || empty($option['mapPoint'])){
			return $this->error('非法操作');
		} else {
			$address = []; 
			$reservation_data = $this->getReservationData();
			$mapPoint_str = $option['mapPoint'];
			$mpstr_arr = explode(',', $mapPoint_str);
			if(count($mpstr_arr) == 2){
				$mp_arr['x'] = $mpstr_arr[0];
				$mp_arr['y'] = $mpstr_arr[1];
				$address['mapPoint'] = $mp_arr;
			} 
			$address['mapPointStr'] = $mapPoint_str;
			$address['address'] 	= $option['address'];
			$reservation_data['address'] = $address;
			$this->saveReservationData($reservation_data);
			return $this->success('设置成功', Session::get('reservation_callback_url'));
		}
	}
}
