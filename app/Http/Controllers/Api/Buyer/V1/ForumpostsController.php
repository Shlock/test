<?php
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\ForumPostsService;
use YiZan\Services\Buyer\ForumPlateService;
use YiZan\Services\Buyer\ForumMessageService;
use Time;

/**
 * 发帖
 */
class ForumpostsController extends BaseController {


    /**
     * [index ]
     */
    public function index(){
        $result = [];
        $posts = ForumPostsService::lists(
            $this->userId,
            $this->request('plateId'),
            $this->request('page') ? (int)$this->request('page') : 1,
            $this->request('pageSize') ? (int)$this->request('pageSize') : 20,
            (int)$this->request('isUser')
        );

        foreach ($posts as $key => $value) {
            $posts[$key]['imagesArr'] = empty($value['images']) ? [] : explode(',', $value['images']);
            $posts[$key]['createTimeStr'] = Time::toDate($value['createTime']);
        }
        $result['posts'] = $posts;
        $plates = ForumPlateService::lists();
        if(count($plates) > 8){
            $data = [
                'id' 		=> 0,
                'name' 		=> '更多',
                'icon' 		=> 'http://image.jikesoft.com/images/2016/01/11/201601110938458769267.jpg',
                'sort' 		=> '100',
                'status' 	=> '1',
            ];
            $plates = array_slice($plates, 0, 7);
            $plates[] = $data;
        }

        $result['plates'] = $plates;
        $result['postsnum'] = ForumPostsService::getPostsNum($this->userId);
        $result['messagenum'] = ForumMessageService::getMessageNum($this->userId);
        $this->outputData($result);
    }

    /**
     * [lists 帖子列表]
     */
    public function lists(){
        $result = ForumPostsService::lists(
            $this->userId,
            $this->request('plateId'),
            $this->request('page') ? (int)$this->request('page') : 1,
            $this->request('pageSize') ? (int)$this->request('pageSize') : 20,
            (int)$this->request('isUser')
        );
        foreach ($result as $key => $value) {
            $result[$key]['imagesArr'] = empty($value['images']) ? [] : explode(',', $value['images']);
            $result[$key]['createTimeStr'] = Time::toDate($value['createTime']);
        }
        $this->outputData($result);
    }

    /**
     * 会员帖子
     */
    public function userlists(){
        $type = $this->request('type');
        if($type == 1){
            $result = ForumPostsService::lists(
                $this->userId,
                0,
                $this->request('page') ? (int)$this->request('page') : 1,
                $this->request('pageSize') ? (int)$this->request('pageSize') : 20,
                1
            );
        } else if($type == 2){
            $result = ForumPostsService::replyLists(
                $this->userId ,
                $this->request('page') ? (int)$this->request('page') : 1,
                $this->request('pageSize') ? (int)$this->request('pageSize') : 20
            );
        } else {
            $result = ForumPostsService::praiseLists(
                $this->userId ,
                $this->request('page') ? (int)$this->request('page') : 1,
                $this->request('pageSize') ? (int)$this->request('pageSize') : 20
            );
        }
        foreach ($result as $key => $value) {
            $result[$key]['imagesArr'] = empty($value['images']) ? [] : explode(',', $value['images']);
            $result[$key]['createTimeStr'] = Time::toDate($value['createTime']);
        }
        $this->outputData($result);
    }


    /**
     * 回复的帖子
     */
    public function replylists(){
        $result = ForumPostsService::replyLists(
            $this->userId ,
            $this->request('page') ? (int)$this->request('page') : 1,
            $this->request('pageSize') ? (int)$this->request('pageSize') : 20
        );
        $this->outputData($result);
    }

    /**
     * 点赞的帖子
     */
    public function praiselists(){
        $result = ForumPostsService::praiseLists(
            $this->userId ,
            $this->request('page') ? (int)$this->request('page') : 1,
            $this->request('pageSize') ? (int)$this->request('pageSize') : 20
        );
        $this->outputData($result);
    }

    /**
     * 我的帖子数量
     */
    public function postsnum(){
        $result = ForumPostsService::getPostsNum(
            $this->userId
        );
        $this->outputData($result);
    }

    /**
     * 点赞/取消点赞操作
     */
    public function praise(){
        $result = ForumPostsService::praise(
            $this->userId,
            $this->request('postsId')
        );
        $this->output($result);
    }

    /**
     * 帖子编辑
     */
    public function get(){
        $result = ForumPostsService::get(
            $this->userId,
            $this->request('id')
        );
        $this->outputData($result);
    }

    /**
     * 帖子详情
     */
    public function edit(){
        $result = ForumPostsService::edit(
            $this->userId,
            $this->request('id'),
            $this->request('page') ? (int)$this->request('page') : 1,
            $this->request('pageSize') ? (int)$this->request('pageSize') : 20,
            $this->request('isLandlord'),
            $this->request('sort')
        );
        $this->outputData($result);
    }

    /**
     * 添加/修改帖子
     */
    public function save(){
        $result = ForumPostsService::save(
            $this->request('id'),
            $this->userId,
            (int)$this->request('plateId'),
            $this->request('title'),
            $this->request('content'),
            $this->request('images'),
            $this->request('addressId')
        );
        $this->output($result);
    }

    /**
     * 回复
     */
    public function reply(){
        $result = ForumPostsService::reply(
            $this->request('id'),
            $this->userId,
            $this->request('title'),
            $this->request('content')
        );
        $this->output($result);
    }

    /**
     * 删除
     */
    public function delete(){
        $result = ForumPostsService::delete(
            $this->userId,
            $this->request('id')
        );
        $this->output($result);
    }

    //举报
    public function complain(){
        $result = ForumPostsService::complain(
            $this->userId,
            $this->request('id'),
            $this->request('content')
        );
        $this->output($result);
    }

    /**
     * 搜索帖子
     */
    public function search(){
        $result = ForumPostsService::searchPosts(
            $this->request('keywords'),
            $this->request('page') ? (int)$this->request('page') : 1,
            $this->request('pageSize') ? (int)$this->request('pageSize') : 20
        );
        $this->outputData($result);
    }


}