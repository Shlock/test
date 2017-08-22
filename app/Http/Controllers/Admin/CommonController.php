<?php 
namespace YiZan\Http\Controllers\Admin; 
 
use Illuminate\Routing\Controller;
use View, Input, Form; 

/**
 * 服务人员提现
 */
class CommonController extends Controller {
	public function __construct() {
		echo "parent";
		$this->init();
	}

	protected function init(){
		echo "pinit";
	}

}
