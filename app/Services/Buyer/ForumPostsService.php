<?php namespace YiZan\Services\Buyer;

use YiZan\Models\ForumPlate;
use YiZan\Models\ForumPosts;
use YiZan\Models\ForumComplain;
use YiZan\Models\SystemConfig;
use YiZan\Models\UserPraise;
use YiZan\Models\User;
use YiZan\Models\UserAddress;
use YiZan\Services\ForumMessageService;
use DB, Time, Exception;

class ForumPostsService extends \YiZan\Services\ForumPostsService {

    /**
     * 列表
     */
    public static function lists($userId, $plateId, $page, $pageSize = 20, $isUser = 0){
        $lists = ForumPosts::orderBy('id', 'DESC');
        $dbPrefix = DB::getTablePrefix();
        //查询会员自己的帖子
        if($isUser && $userId > 0){
            return $lists->where('pid', 0)
                ->where('user_id', $userId)
                ->orderBy('top', 'DESC')
                ->orderBy('hot', 'DESC')
                ->orderBy('good_num', 'DESC')
                ->orderBy('rate_num', 'DESC')
                ->orderBy('create_time', 'DESC')
                ->skip(($page - 1) * $pageSize)
                ->take($pageSize)
                ->with('plate','user')
                ->get()
                ->toArray();
            //查询板块下面的帖子
        } else if($plateId > 0){
            $lists = $lists->where('plate_id', $plateId)
                ->where('is_check', 1)
				->where('is_del',0)
                ->where('status',1)
                ->where('pid', 0)
                ->leftJoin('user_praise', function($join) use($userId){
                    $join->on('user_praise.posts_id', '=', 'forum_posts.id')
                        ->where('user_praise.user_id', '=', $userId);
                })
                ->orderBy('top', 'DESC')
                ->orderBy('hot', 'DESC')
                ->orderBy('good_num', 'DESC')
                ->orderBy('rate_num', 'DESC')
                ->orderBy('create_time', 'DESC')
                ->skip(($page - 1) * $pageSize)
                ->take($pageSize)
                ->with('plate','user','address')
                ->select('forum_posts.*')
                ->addSelect(DB::raw("IFNULL({$dbPrefix}user_praise.posts_id,0) AS isPraise"))
                ->get()
                ->toArray();
            return $lists;
        } else {
            return $lists->where('pid', 0)
                ->where('is_check', 1)
				->where('is_del',0)
                ->where('status',1)
                ->leftJoin('user_praise', function($join) use($userId){
                    $join->on('user_praise.posts_id', '=', 'forum_posts.id')
                        ->where('user_praise.user_id', '=', $userId);
                })
                ->orderBy('top', 'DESC')
                ->orderBy('hot', 'DESC')
                ->orderBy('good_num', 'DESC')
                ->orderBy('rate_num', 'DESC')
                ->orderBy('create_time', 'DESC')
                ->skip(0)
                ->take($pageSize)
                ->with('plate','user','address')
                ->select('forum_posts.*')
                ->addSelect(DB::raw("IFNULL({$dbPrefix}user_praise.posts_id,0) AS isPraise"))
                ->get()
                ->toArray();
        }
    }

    /**
     * 帖子数量
     */
    public static function getPostsNum($userId){
        return ForumPosts::where('user_id', $userId)
            ->where('pid', 0)
            //->where('is_check', '=', 1)
            ->count();
    }

    /**
     * 已回复的帖子
     */
    public static function replyLists($userId, $page, $pageSize){

        $dbPrefix = DB::getTablePrefix();
        $sql = "
			SELECT
					id
				FROM
					{$dbPrefix}forum_posts
				WHERE
					id IN (
						SELECT
							pid
						FROM
							{$dbPrefix}forum_posts
						WHERE
							user_id = {$userId}
						AND pid <> 0
						GROUP BY
							pid
					)
				AND pid = 0
			UNION
				SELECT
					pid
				FROM
					{$dbPrefix}forum_posts
				WHERE
					id IN (
						SELECT
							pid
						FROM
							{$dbPrefix}forum_posts
						WHERE
							user_id = {$userId}
						AND pid <> 0
						GROUP BY
							pid
					)
				AND pid > 0;
			";

        $data = DB::select($sql);
        $postsIds = [];
        foreach ($data as $key => $item) {
            $postsIds[] = $item->id;
        }
        $lists = ForumPosts::whereIn('id', $postsIds)
            ->with('plate', 'user', 'address')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();
        return $lists;
    }

    /**
     * 点赞的帖子
     */
    public static function praiseLists($userId, $page, $pageSize){
        $dbPrefix = DB::getTablePrefix();
        $sql = "
			SELECT
				posts_id
			FROM
				{$dbPrefix}user_praise
			WHERE
				user_id = {$userId} 
			";
        $data = DB::select($sql);
        $postsIds = [];
        foreach ($data as $key => $item) {
            $postsIds[] = $item->posts_id;
        }
        $lists = ForumPosts::whereIn('id', $postsIds)
            ->with('plate', 'user', 'address')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();
        return $lists;
    }

    /**
     * 点赞
     */
    public static function praise($userId, $postsId){
        $result =
            [
                'code'	=> 0,
                'data'	=> 0,
                'msg'	=> '操作成功'
            ];
        $forumposts = ForumPosts::where('id', $postsId)
            ->with('user')
            ->first();
        if(empty($forumposts)){
            $result['code'] = 30922;
            return $result;
        }
        //开启事务
        DB::beginTransaction();
        try {
            $praise = UserPraise::where('user_id', $userId)
                ->where('posts_id', $postsId)
                ->first();
            if($praise){
                UserPraise::where('user_id', $userId)
                    ->where('posts_id', $postsId)
                    ->delete();
                $forumposts->good_num = ($forumposts->good_num - 1) < 0 ? 0 : $forumposts->good_num - 1;
                $forumposts->save();
                $data['status'] = 0;
            } else {
                $praise = new UserPraise();
                $praise->user_id 	= $userId;
                $praise->posts_id 	= $postsId;
                $praise->save();
                $forumposts->good_num += 1;
                $forumposts->save();
                $data['status'] = 1;
            }
            $username = User::where('id', $userId)->pluck('name');
            if($data['status'] == 1 ){
                //写入论坛消息
                $content = $username . '点赞了您的帖子『' . $forumposts->title . '』。';
                $bl = ForumMessageService::create(1, '系统消息', $content, $userId, $forumposts->user_id, $forumposts->id, 0);
                if(!$bl){
                    throw new Exception("Error Processing Request", 1);
                }
            }
            $result['data'] = $data;
            DB::commit();
        } catch (Exception $e) {
            $result['code'] = 30923;
            DB::rollback();
        }

        return $result;
    }

    /**
     * 添加/修改帖子
     */
    public static function save($id, $userId, $plateId, $title, $content, $images, $addressId){
        if ($id > 0) {
            $result =
                [
                    'code'	=> 0,
                    'data'	=> null,
                    'msg'	=> '编辑帖子成功'
                ];
        } else {
            $result =
                [
                    'code'	=> 0,
                    'data'	=> null,
                    'msg'	=> '发帖成功，请等待审核'
                ];
        }

        $forumposts = ForumPosts::where('id', $id)->first();


        if($id > 0){
            $forumposts = ForumPosts::find($id);
            if(empty($forumposts)){
                $result['code'] = 30916;
                return $result;
            }
            if ($forumposts->status = 0) {
                $result['code'] = 30926;
                return $result;
            }
        } else {
            $forumposts = new ForumPosts();
            //获取发帖的系统配置
            $postscheck = SystemConfig::where('code', 'posts_check')
                ->first();
            if($postscheck->val == 1){
                $forumposts->is_check = 0;
            } else {
                $forumposts->is_check = 1;
            }
            $forumposts->status 	= 1;
        }

        if(!ForumPlate::find($plateId)){
            $result['code'] = 30920;
            return $result;
        }
        if($title == ''){
            $result['code'] = 30913;
            return $result;
        }
        if($content == ''){
            $result['code'] = 30914;
            return $result;
        }

        if($addressId > 0 && !UserAddress::find($addressId)){
            $result['code'] = 61001;
            return $result;
        }

        $keyinfo  = SystemConfig::where('code', 'key_words')
            ->first();

        $keywords = explode(',', $keyinfo->val);
        //检查关键词
        if(self::checkKeyWords($title, $keywords)){
            $result['code'] = 30924;
            return $result;
        }

        if(self::checkKeyWords($content, $keywords)){
            $result['code'] = 30925;
            return $result;
        }

        $forumposts->plate_id	= $plateId;
        $forumposts->title 		= $title;
        $forumposts->content 	= $content;
        $forumposts->user_id 	= $userId;
        if(!empty($images)) {
            $newImages = [];
            foreach ($images as $image) {
                if (!empty($image)) {
                    $image = self::moveUserImage($userId, $image);
                    //转移图片失败
                    if (!$image) {
                        $result['code'] = 30213;

                        return $result;
                    }
                    $newImages[] = $image;
                }
            }
            $forumposts->images = count($newImages) ? implode(',', $newImages) : "";

        }
        $forumposts->address_id 	= $addressId;
        $forumposts->create_time 	= UTC_TIME;
        $forumposts->create_date 	= UTC_DAY;
        try {
            $forumposts->save();
            $result['data'] = $forumposts;
        } catch (Exception $e) {
            $result['code'] = 30917;
        }
        return $result;
    }

    public static function get($userId, $id){
        $forumposts = ForumPosts::where('id',$id)
            ->where('user_id', $userId)
            ->with('plate','address')
            ->first();
        return $forumposts;
    }

    public static function edit($userId, $id, $page, $pageSize, $isLandlord, $sort = 0){
        $dbPrefix = DB::getTablePrefix();
        $forumposts = ForumPosts::where('forum_posts.id',$id)
            ->leftJoin('user_praise', function($join) use($userId){
                $join->on('user_praise.posts_id', '=', 'forum_posts.id')
                    ->where('user_praise.user_id', '=', $userId);
            })
            ->with('plate','user','address')
            ->select('forum_posts.*')
            ->addSelect(DB::raw("IFNULL({$dbPrefix}user_praise.posts_id,0) AS isPraise"))
            ->first();

        if(!$forumposts){
            return [];
        } else {
            $forumposts = $forumposts->toArray();
        }
        if($sort == 0){
            $sort = 'ASC';
        } else {
            $sort = 'DESC';
        }
        //查询子项
        if($isLandlord == 1){
            $forumposts['childs'] = ForumPosts::where('pid', $forumposts['id'])
                ->where('user_id', $forumposts['userId'])
                ->with('plate','user','address','praise','replyPosts')
                ->skip(($page - 1) * $pageSize)
                ->take($pageSize)
                ->get()
                ->toArray();
        } else {
            $forumposts['childs'] = ForumPosts::where('pid', $id)
                ->with('plate','user','address','praise','replyPosts')
                ->orderBy('create_time', $sort)
                ->skip(($page - 1) * $pageSize)
                ->take($pageSize)
                ->get()
                ->toArray();
        }
        $forumposts['imagesArr'] = !empty($forumposts['images']) ? explode(',', $forumposts['images']) : null;
        $forumposts['createTimeStr'] =  Time::toDate($forumposts['createTime']);
        $i = 1;
        foreach ($forumposts['childs'] as $key => $value) {
            $forumposts['childs'][$key]['createTimeStr'] = Time::toDate($value['createTime']);
            $forumposts['childs'][$key]['flood'] = $i++;
        }
        return $forumposts;
    }

    /**
     * 回复帖子
     */
    public static function reply($id, $userId, $title, $content){
        $result =
            [
                'code'	=> 0,
                'data'	=> null,
                'msg'	=> '回复成功'
            ];

        $forumposts = $replyposts = ForumPosts::where('id', $id)
            ->with('user')
            ->first();
        if($forumposts->pid > 0){
            $forumposts = ForumPosts::where('id', $forumposts->pid)
                ->with('user')
                ->first();
        }

        if(empty($forumposts) || empty($replyposts)){
            $result['code'] = 30916;
            return $result;
        }
        if($content == ''){
            $result['code'] = 30919;
            return $result;
        }
        if ($forumposts->status == 0) {
            $result['code'] = 30926;
            return $result;
        }
        if ($forumposts->is_check != 1) {
            $result['code'] = 30926;
            return $result;
        }

        $keyinfo  = SystemConfig::where('code', 'key_words')
            ->first();
        $keywords = str_replace(',', '，', $keyinfo->val);

        $keywords = explode('，', $keywords);
        //检查关键词
        if(self::checkKeyWords($content, $keywords)){
            $result['code'] = 30925;
            return $result;
        }

        $reply = new ForumPosts();
        $reply->pid 		    = $forumposts->id;
        $reply->reply_id 	    = $replyposts->id;
        $reply->plate_id	    = $forumposts->plate_id;
        $reply->user_id 	    = $userId;
        $reply->title 		    = $title;
        $reply->content 	    = $content;
        if($forumposts->id != $replyposts->id){
            $reply->reply_content   = '引用"'.$replyposts->user['name'].'"的回复：';
        }
        $reply->create_time     = UTC_TIME;
        $reply->create_date     = UTC_DAY;
        //开启事务
        DB::beginTransaction();
        try {
            $reply->save();
            $username = User::where('id', $userId)->pluck('name');
            //写入论坛消息
            $content = $username . '回复了您的帖子『' . $forumposts->title . '』。';
            $bl = ForumMessageService::create(1, '系统消息', $content, $userId, $forumposts->user_id, $forumposts->id, 0);
            if(!$bl){
                throw new Exception("Error Processing Request", 1);
            }
            //如果回复成功 帖子的评价数量加1
            $forumposts->rate_num += 1;
            $forumposts->save();
            DB::commit();
        } catch (Exception $e) {
            $result['code'] = 30918;
            print_r($e->getMessage());
            DB::rollback();
        }
        return $result;
    }

    /**
     * 删除帖子
     */
    public static function delete($userId, $id){
        $result =
            [
                'code'	=> 0,
                'data'	=> null,
                'msg'	=> '删除成功'
            ];

        try{
            ForumPosts::where('user_id', $userId)
                ->where('id', $id)
                ->delete();
        } catch(Exception $e) {
            $result['code'] = 30921;
        }
        return $result;
    }

    /**
     * 敏感词替换
     */
    public static function replaceKeyWords($content, $keywords){
        foreach ($keywords as $word) {
            $content = str_replace($word, '*', $content);
        }
        return $content;
    }

    /**
     * 敏感词查找
     */
    public static function checkKeyWords($content, $keywords){
        foreach ($keywords as $word) {
            $location = strpos($content, trim($word));
            if(is_int($location)){
                return true;
            }
        }
        return false;
    }

    /**
     * 举报帖子
     */
    public static function complain($userId, $id, $content){
        $result =
            [
                'code'	=> 0,
                'data'	=> null,
                'msg'	=> '举报成功'
            ];
        $forumposts = ForumPosts::find($id);
        if(empty($forumposts)){
            $result['code'] = 30916;
            return $result;
        }
        if($content == ''){
            $result['code'] = 30919;
            return $result;
        }
        $complain = new ForumComplain();
        $complain->post_id		= $id;
        $complain->user_id 	= $userId;
        $complain->content 	= $content;
        $complain->create_time = UTC_TIME;
        try {
            $complain->save();
        } catch (Exception $e) {
            $result['code'] = 30919;
        }
        return $result;
    }

    /**
     * 搜索
     */
    public static function searchPosts($keywords, $page, $pageSize){
        $result = [];
        if (!empty($keywords) ) {
            $result = ForumPosts::where('title', 'like', '%' . $keywords . '%')
                ->where('pid', 0)
                ->skip(($page - 1) * $pageSize)
                ->take($pageSize)
                ->with('user','plate')
                ->get()
                ->toArray();
        }
        return $result;
    }

}
