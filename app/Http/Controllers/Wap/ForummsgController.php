<?php 
namespace YiZan\Http\Controllers\Wap;
use Illuminate\Support\Facades\Response;
use Input, View, Cache;
/**
 * 论坛消息
 */
class ForummsgController extends UserAuthController {

	public function __construct() {
		parent::__construct();
		View::share('nav','more');
	}
	/**
	 * 论坛消息
	 */
	public function index() {
        $post = Input::get();
        $list = $this->requestApi('forummessage.lists',['page' => (int)$post['page']]);
        $result = $this->requestApi('forummessage.read');
		View::share('list', $list['data']);
        if (Input::ajax()) {
            return $this->display('index_item');
        } else {
            return $this->display();
        }

	}


    /**
     * 删除消息
     */
    public function delete() {
        $result = $this->requestApi('forummessage.delete',['id' => (int)Input::get('id')]);
        return  Response::json($result);
    }


    public function readmsg() {
        $result = $this->requestApi('forummessage.read',['id' => (int)Input::get('id')]);
        return  Response::json($result);
    }

	
}