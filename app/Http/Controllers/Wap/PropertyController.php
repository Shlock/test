<?php 
namespace YiZan\Http\Controllers\Wap;
use Illuminate\Support\Facades\Response;
use Input, View, Cache, Session;
/**
 * 物业
 */
class PropertyController extends UserAuthController {

	public function __construct() {
		parent::__construct();
		View::share('nav','forum');
	}

    public function index() {
    	$args = Input::all();
    	$data = $this->requestApi('district.getdistrict', $args);
    	//print_r($data);
    	
    	View::share('data', $data['data']);
    	View::share('args', $args);
        View::share('user', $this->user);
        return $this->display();
    }
    
    /**
     * 业主详细
     */
    public function detail() {
        $args = Input::all();
        $data = $this->requestApi('district.getdistrict', $args);
        //print_r($data);
        
        View::share('data', $data['data']);
        View::share('args', $args);
        return $this->display();
    }
    
    /**
     * 社区公告
     */
    public function article() {
        $args = Input::all();
        $data = $this->requestApi('district.getdistrict', $args);
        //print_r($data);
        $list = $this->requestApi('article.lists', ['sellerId'=>$data['data']['sellerId']]);
       // print_r($list);
        View::share('list', $list['data']);
        View::share('args', $args);
        return $this->display();
    }
    
    public function articledetail() {
        $data = $this->requestApi('article.get', ['id'=>Input::get('id')]);
        //print_r($data);
        View::share('data', $data['data']);

        $result = $this->requestApi('article.read', ['id'=>Input::get('id')]);
        return $this->display();
    }

    public function brief() {
        $args = Input::all();
        $data = $this->requestApi('property.detail', $args);
       // print_r($data);
        
        View::share('data', $data['data']);
        return $this->display();
    }

    public function applyaccess() {
        $args = Input::all();
       
        $result = $this->requestApi('user.applyaccess',$args);
        return Response::json($result);
    }

}