<?php 
namespace YiZan\Http\Controllers\Admin;  

use View, Input, Form,Lang; 
/**
*广告管理
*/
class WapModuleController extends UserAppAdvController {
	protected $type;
 	public function __construct() {
		parent::__construct();
		$this->WapModuletype = 'WapModule';
	}

	/**
	 * 广告 列表
	*/
	public function index() { 
        $args = Input::All();
        
        $args["code"] = "BUYER_INDEX_MENU";
        
		$result = $this->requestApi('adv.lists',$args); 
		if( $result['code'] == 0) 
			View::share('list', $result['data']['list']);
		return $this->display();
	}
}
