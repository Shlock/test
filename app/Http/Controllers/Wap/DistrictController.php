<?php 
namespace YiZan\Http\Controllers\Wap;
use Illuminate\Support\Facades\Response;
use Input, View, Cache, Session;
/**
 * 我的小区
 */
class DistrictController extends UserAuthController {

	public function __construct() {
		parent::__construct();
		View::share('nav','mine');
	}

    public function index() {
    	$list = $this->requestApi('district.lists');
    	//print_r($list);
    	if ($list['code'] == 0) {
    		View::share('list', $list['data']);
    	}
    	View::share('args', $args);
        return $this->display();
    }
    
    public function detail() {
    	$args = Input::all();
    	$data = $this->requestApi('district.get', $args);
    	//print_r($data);
    	if ($data['code'] == 0) {
    		View::share('data', $data['data']);
    	}
        return $this->display();
        // if (isset($args['isUser'])) {
        //     return $this->display('details');
        // } else {
        //     return $this->display();
        // }
        
    }


    public function add() {
        $args = Input::all();
        
        if (!isset($args['keywords']) && empty($args['keywords'])) {
            $list = $this->requestApi('district.getnearestlist', $args);
        } else {
            $list = $this->requestApi('district.searchvillages', ['keywords'=>$args['keywords']]);
        }
        //print_r($list);
        if ($list['code'] == 0) {
            View::share('list', $list['data']);
        }
        View::share('args', $args);
        return $this->display();
    }

    public function delete() {
	    $args = Input::all();
	   
	    $result = $this->requestApi('district.delete',$args);
	    return Response::json($result);
	}

    public function save() {
        $args = Input::all();
       
        $result = $this->requestApi('district.create',$args);
        return Response::json($result);
    }

    /*
    * 小区身份验证
    */
    public function userapply() {
        $args = Input::all();
        $data = $this->requestApi('district.get', $args);
        //print_r($data);
        if ($data['code'] == 0) {
            View::share('data', $data['data']);
        }
        $list = $this->requestApi('district.getbuildinglist', ['villagesid'=> $args['districtId']]);
        //print_r($list);


        View::share('list', $list['data']);
        View::share('usertel', $this->user['mobile']);
        return $this->display();
    }

    public function searchrooms() {
        $args = Input::all();
       
        $result = $this->requestApi('district.getroomlist',$args);
        return Response::json($result);
    }

    public function villagesauth() {
        $args = Input::all();
        //var_dump($args);

        $result = $this->requestApi('user.villagesauth',$args);
        return Response::json($result);
    }

}