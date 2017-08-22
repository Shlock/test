<?php 
namespace YiZan\Http\Controllers\Wap;
use Illuminate\Support\Facades\Response;
use Input, View, Cache, Session;
/**
 * 物业报修
 */
class RepairController extends UserAuthController {

	public function __construct() {
		parent::__construct();
		View::share('nav','forum');
	}

    public function index() {
    	$args = Input::all();
    	$list = $this->requestApi('property.repairlists', $args);
    	//print_r($list);
    	if ($list['code'] == 0) {
    		View::share('list', $list['data']);
    	}
    	View::share('args', $args);
        return $this->display();
    }
    
    public function detail() {
    	$args = Input::all();
    	$data = $this->requestApi('property.repairget', $args);
    	//print_r($data);
    	if ($data['code'] == 0) {
    		View::share('data', $data['data']);
    	}
        return $this->display();
    }

    public function repair() {
    	$args = Input::all();
    	$data = $this->requestApi('district.get', ['districtId'=>$args['districtId']]);
    	View::share('data', $data['data']);

    	$list = $this->requestApi('property.typelists');
    	//print_r($list);
    	if ($list['code'] == 0) {
    		View::share('list', $list['data']);
    	}
    	View::share('args', $args);
        return $this->display();
    }

    public function save() {
	    $args = Input::all();
	   
	    $result = $this->requestApi('property.createrepair',$args);
	    return Response::json($result);
	}

}