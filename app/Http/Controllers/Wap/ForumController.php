<?php 
namespace YiZan\Http\Controllers\Wap;
use Illuminate\Support\Facades\Response;
use Input, View, Cache;
/**
 * 论坛消息
 */
class ForumController extends UserAuthController {

	public function __construct() {
		parent::__construct();
		View::share('nav','forum');
	}

    //生活圈首页
    public function index() {
        $result = $this->requestApi('forumposts.index');  
        View::share('postsnum', $result['data']['postsnum']);
        View::share('messagenum', $result['data']['messagenum']);
        View::share('plates', $result['data']['plates']);
        View::share('lists', $result['data']['posts']);
        return $this->display();
    }
    /*
    * 版块列表
    */
    public function plates() {
        $plates = $this->requestApi('forumplate.lists');
        View::share('plates', $plates['data']);
        return $this->display();
    }

    //论坛帖子列表
    public function lists() {
        $args = Input::all();

        $plate = $this->requestApi('forumplate.get', ['id' => $args['plateId']]);
        $lists = $this->requestApi('forumposts.lists', $args);
        $list = $lists['data'];
        foreach ($list as $key => $value) {
            $list[$key]['images'] = is_array($list[$key]['images']) ? $list[$key]['images'] : explode(',', $list[$key]['images']);
            if ($value['top'] == 1) {
               $posts['top'][] = $list[$key];
            } else {
               $posts['nottop'][] = $list[$key];
            }
        }
        //print_r($posts); 
        View::share('plate', $plate['data']);
        View::share('list', $posts);
        View::share('args', $args);
        return $this->display();
    }

    public function detail() {
        $args = Input::all();
        if (!isset($args['sort']) || (int)$args['sort'] < 1) {
            $args['sort'] = 0;
        }
        if (!isset($args['isLandlord']) || (int)$args['isLandlord'] < 1) {
            $args['isLandlord'] = 0;
        }
        $data = $this->requestApi('forumposts.edit', $args);
        if ($data['code'] == 0) {
            $data['data']['images'] = is_array($data['data']['images']) ? $data['data']['images'] : explode(',', $data['data']['images']);
        }
        //print_r($data);

        View::share('data', $data['data']);
        View::share('args', $args);
        return $this->display();
    }

    public function replypost() {
        $args = Input::all();
        if ((int)$args['id'] < 1) {
            $args['id'] = $args['pid'];
        }

        $result = $this->requestApi('forumposts.reply', $args);
        die(json_encode($result));  
    }

    //我的帖子
    public function mylists() {
        $args = Input::all();
        if (!isset($args['type']) || (int)$args['type'] < 1) {
            $args['type'] == 0;
            $args['isUser'] = 1;
            $lists = $this->requestApi('forumposts.lists', $args);
        } elseif ((int)$args['type'] == 1) {
            $lists = $this->requestApi('forumposts.replylists');
        } else {
            $lists = $this->requestApi('forumposts.praiselists');
        }
        
        //print_r($lists);

        View::share('list', $lists['data']);
        View::share('args', $args);
        return $this->display();
    }

    public function delete() {
        $id = (int)Input::get('id');
        $result = $this->requestApi('forumposts.delete',array('id'=>$id));
        return Response::json($result); 
    }


    public function search(){
        $keywords = Input::get('keywords');
        $searchs = array();
        if (Session::get('searchs')) {
            $searchs = Session::get('searchs');
        }
        if (!empty($keywords) && !in_array($keywords, $searchs)) {
            array_push($searchs, $keywords);
            Session::set('searchs', $searchs);
            Session::save();
        }
        $history_search = Session::get('searchs');
        //var_dump($history_search);
        View::share('history_search',$history_search); 

        $option = Input::all();

        $post_data = $this->requestApi('forumposts.search',$option);
        //print_r($post_data);
        if($post_data['code'] == 0)     
            View::share('data',$post_data['data']); 

        View::share('option',$option); 
        return $this->display();

    }

    /**
     * 清除搜索历史记录
     */
    public function clearsearch(){
        $keywords = Input::get('keywords');
        if (!empty($keywords)) {
            $searchs = Session::get('searchs');
            foreach ($searchs as $key => $value) {
                if ($value == $keywords) {
                    unset($searchs[$key]);
                }
            }
            Session::set('searchs', $searchs);
        } else {
            Session::set('searchs', NULL);
        }
        Session::save();
    }

    //发帖、编辑
    public function addbbs() {
        $args = Input::all();
        $option = Session::get('bbs_info');
        if (isset($args['addressId']) || (int)$args['addressId'] > 0) {
            $address = $this->requestApi('user.address.get', ['id' => $args['addressId']]);
            $option['addressId'] = $args['addressId'];
            Session::put('bbs_info', $option);
            Session::save();
            View::share('address', $address['data']);
        }
        if ((int)$args['postId'] > 0) {
            $data = $this->requestApi('forumposts.get', ['id' => $args['postId']]);
            $data['data']['images'] = is_array($data['data']['images']) ? $data['data']['images'] : explode(',', $data['data']['images']);
            View::share('data', $data['data']);
        }
        //print_r($data);
        $plate = $this->requestApi('forumplate.get', ['id' => $args['plateId']]);
        //print_r($option);
        View::share('option', $option);
        View::share('plate', $plate['data']);
        View::share('args', $args);
        return $this->display();
    }

    //保存帖子
    public function savebbs() {
        $data = Session::get('bbs_info');
        $data['id'] = (int)$data['postId'];
        // var_dump($data);
        // exit;
        $result = $this->requestApi('forumposts.save',$data);
        if ($result['code'] == 0) {
            Session::put('bbs_info', NULL);
            Session::save();
        }
        die(json_encode($result));
    }

    public function savebbsData() {
        $data = Input::all();
        Session::put('bbs_info', $data);
        Session::save();
        return $this->success('成功');
    }

	/**
     * 我的地址
     */
    public function address() {
        $list = $this->requestApi('user.address.lists',Input::all());
        View::share('list',$list['data']);
        View::share('title',"- 联系地址");
        View::share('nav_back_url', u('Forum/addbbs',Input::all()));
        if(Input::ajax()){
            return $this->display('address_item');
        }else{
            return $this->display();
        }   
    }

    /**
     * 地址详情
     */
    public function addressdetail() {
        $init_data = $this->requestApi('app.init');
        View::share('cityData', $init_data['data']['province']);
        View::share('cityJson', json_encode($init_data['data']['province']));
        if ((int)Input::get('id') > 0) {
            $data = $this->requestApi('user.address.get', ['id' => (int)Input::get('id')]);
            View::share('data', $data['data']);
            View::share('title',"- 编辑地址");
        }else{
            View::share('title',"- 添加地址");
        }
        return $this->display();    
    }
    /**
     * 新增地址
     */
    public function addresssdetail() {
        $init_data = $this->requestApi('app.init');
        View::share('cityData', $init_data['data']['province']);
        View::share('cityJson', json_encode($init_data['data']['province']));
        if ((int)Input::get('id') > 0) {
            $data = $this->requestApi('user.address.get', ['addressId' => (int)Input::get('id')]);
            View::share('data', $data['data']);
            View::share('title',"- 编辑地址");
        }else{
            View::share('title',"- 添加地址");
        }
        return $this->display();    
    }

    /**
     * 获取城市数据
     */
    public function getcity() {
        $data = $this->requestApi('app.init');
        die(json_encode($data['data']['province']));
    }

    /**
     * 常用地址操作
     */
    public function saveaddress() {
        $data = Input::all();
        $result = $this->requestApi('user.address.create',$data);
        if ($result['code'] == 0) {
            $result['msg'] = '添加联系地址成功！';
        }
        die(json_encode($result));
    }
    /**
     * 删除地址
     */
    public function deladdress() {
        $id = (int)Input::get('id');
        $result = $this->requestApi('user.address.delete',array('id'=>$id));
        die(json_encode($result));
            
    }

    /**
     * 设置默认地址
     */
    public function setdefault() {
        $id = (int)Input::get('id');
        $result = $this->requestApi('user.address.setdefault',array('id'=>$id));
        die(json_encode($result));  
    }

	
    /**
     * 获取发帖时间
     */
    public function getPostTime($createTime) {
        $current_time = UTC_TIME;
        $time = ($current_time - $createTime)/60;
        if ($time > 60 ) {
            $postTime = ceil($time/60) . '小时前';
        } else {
            $postTime = ceil($time) . '分钟前';
        }
        return $postTime; 
    }

    //点赞
    public function updateLike() {
        $id = (int)Input::get('id');
        $result = $this->requestApi('forumposts.praise',array('postsId'=>$id));
        die(json_encode($result));  
    }

    //举报
    public function complain() {
        $id = Input::get('id');
        View::share('id', $id);
        return $this->display();    
    }

    public function addcomplain() {
        $args = Input::all();

        $result = $this->requestApi('forumposts.complain',$args);
        die(json_encode($result));  
    }


}