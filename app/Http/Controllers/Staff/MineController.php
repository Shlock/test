<?php 
namespace YiZan\Http\Controllers\Staff;

use View, Input, Lang, Route, Page;
/**
 * 我的
 */
class MineController extends AuthController {

	public function __construct() {
		parent::__construct();
		View::share('nav','mine');
		View::share('is_show_top',true);
	}
	
	/**
	 * 首页信息 
	 */
	public function index() {
		//获取是否有未读消息
		$result = $this->requestApi('user.msgStatus');
		if($result['code'] == 0)
			View::share('hasNewMsg',$result['data']['hasNewMessage']);
        View::share('title','个人中心');
		View::share('staff',$this->staff);
		return $this->display();
	} 
	
	/**
	 * 员工详细
	 */
	public function info() {
		View::share('staff',$this->staff);
		return $this->display();
	}

	/**
	 * 更改用户信息
	 */
	public function updateinfo() {
		$name = trim(Input::get('name'));
		$avatar = trim(Input::get('avatar'));
		$result = $this->requestApi('staff.update',array('name'=>$name,'avatar'=>$avatar));
		if ($result['code'] == 0){
			$this->setStaff($result['data']);
		}
		die(json_encode($result));
	}

	/**
	 * 我的消息
	 */
	public function message() {
        $args = Input::get();
		$list = $this->requestApi('msg.lists',$args);
		if($list['code'] == 0){
			View::share('list',$list['data']);
		}
        View::share('nav', 'msg');
        View::share('args',$args);
        if(Input::ajax()){
            return $this->display('msg_item');
        }else{
            return $this->display();
        }
	}

	/**
	 * 阅读单条消息
	 */
	public function msgshow() {
		$args = Input::all();
		if( !is_array($args['id']) ) {
			$args['id'] = (int)$args['id'];
		}
        $this->requestApi('msg.read',$args);
        $list = $this->requestApi('msg.getdata',$args);
        View::share('nav', 'msg');
		View::share('data',$list);
		return $this->display(); 
	}

	/**
	 * 批量阅读消息
	 */
	public function readMsg() {
		$args = Input::all();
		if( !is_array($args['id']) ) {
			$args['id'] = (int)$args['id'];
		}
		$result = $this->requestApi('msg.read',$args);
		die(json_encode($result));
	}

	/**
	 * 批量删除消息
	 */
	public function deleteMsg() {
		$args = Input::all();
		if( !is_array($args['id']) ) {
			$args['id'] = (int)$args['id'];
		}
		$result = $this->requestApi('msg.delete',$args);
		die(json_encode($result));
	}

	/**
	 * 意见反馈
	 */
	public function feedback() {
        View::share('title', '意见反馈');
        View::share('nav_back_url', u('Mine/index'));
		return $this->display();
	}

	/**
	 * 增加意见反馈
	 */
	public function addfeedback() {
		$content = strip_tags(Input::get('content'));
		$result = $this->requestApi('feedback.create',['content'=>$content,'deviceType'=>'wap']);
		die(json_encode($result));
	}


    /**
     * [reg 修改密码]
     */
    public function repwd() {
        View::share('staff', $this->staff);
        View::share('title', '修改密码');
        View::share('nav_back_url', u('Mine/index'));
        return $this->display();
    }

    /**
     * [doreg 执行修改密码]
     */
    public function dorepwd() {
        $data = Input::all();
        $data['type'] = 'repwd';
        $result = $this->requestApi('user.repwd',$data);
        if($result['code'] == 0){
            Session::set('staff','');
            $this->setSecurityToken(null);
        }
        die(json_encode($result));
    }
}
