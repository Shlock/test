<?php namespace YiZan\Services\System;


use YiZan\Models\ForumPosts;
use YiZan\Services\ForumMessageService;
use YiZan\Utils\Time;
use DB;
class ForumPostsService extends \YiZan\Services\ForumPostsService {

    public static function getLists($username, $title, $plateId, $status, $beginTime, $endTime, $hot, $top, $sort, $page, $pageSize){
        $list = ForumPosts::orderBy('forum_posts.id', 'DESC')
            ->where('pid', 0)
            ->where('is_check', 1);
        if($username == true){
            $list->join('user', function($join) use($username) {
                $join->on('user.id', '=', 'forum_posts.user_id')
                    ->where('user.name', 'like', '%'.$username.'%');
            });
        }

        if($plateId == true){
            $list->where('plate_id', $plateId);
        }

        if($status > 0){
            $list->where('status', $status - 1);
        }

        if($beginTime > 0) {
            $list->where('create_time', '>=', Time::toTime($beginTime));
        }

        if($endTime > 0) {
            $list->where('create_time', '<=', Time::toTime($endTime));
        }

        if($title){
            $list->where('title', $title);
        }

        if($top){
            $list->where('top', $top);
        }

        if($hot){
            $list->where('hot', $hot);
        }

        $totalCount = $list->count();
        // DB::connection()->enableQueryLog();
        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('user','plate')
            ->get()
            ->toArray();
        // print_r(DB::getQueryLog());exit;
        return ["list"=>$list, "totalCount"=>$totalCount];
    }

    public static function auditLists($type, $page, $pageSize){
        $list = ForumPosts::orderBy('forum_posts.id', 'DESC')
            ->where('pid', 0);

        if($type == 1){
            $list->where('is_check', 0);
        } else  if($type == 2) {
            $list->where('is_check', -1);
        } else {
            $list->whereIn('is_check',[1,0]);
        }

        $totalCount = $list->count();

        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('user','plate')
            ->get()
            ->toArray();

        return ["list"=>$list, "totalCount"=>$totalCount];
    }

    public static function save($id, $title, $content, $images, $mobile, $top, $host, $status = 1){
        $result =
            [
                'code'	=> 0,
                'data'	=> null,
                'msg'	=> '修改成功'
            ];
        $forumposts = ForumPosts::find($id);
        if(empty($forumposts)){
            $result['code'] = 30916;
            return $result;
        }
        if ($forumposts->pid == 0) {
            if($title == ''){
                $result['code'] = 30913;
                return $result;
            }
        }

        if($content == ''){
            $result['code'] = 30914;
            return $result;
        }
        $forumposts->title 		= $title;
        $forumposts->content 	= $content;
        if($images == '' && $forumposts->pid == 0){
            $result['code'] = 30213;
            return $result;
        }  else if($images != ''){
            $newImages = [];
            foreach ($images as $image) {
                if (!empty($image)) {
                    $image = self::moveUserImage($forumposts->user_id, $image);
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
        if($top == true){
            $forumposts->top 		= $top;
        }
        if($hot == true){
            $forumposts->hot 		= $hot;
        }
        if(is_numeric($status)){
            $forumposts->status 	= $status;
        }
        try {
            $forumposts->save();
            $result['data'] = ForumPosts::find($id);
        } catch (Exception $e) {
            $result['code'] = 30917;
        }
        return $result;
    }

    public static function get($id, $type, $page, $pageSize){
        if($type == 0){
            return ForumPosts::find($id);
        } else {
            $forumposts = ForumPosts::where('id', $id)
                ->with('user','plate')
                ->first();
            $list = ForumPosts::where('pid', $forumposts->id);
            $totalCount = $list->count();
            $list = $list->skip(($page - 1) * $pageSize)
                ->take($pageSize)
                ->with('plate','user','posts')
                ->get()
                ->toArray();
            $forumposts['list'] = $list;
            $forumposts['totalCount'] = $totalCount;
            return $forumposts;
        }
    }

    public static function delete($id){
        $result =
            [
                'code'	=> 0,
                'data'	=> null,
                'msg'	=> '删除成功'
            ];
        ForumPosts::where('id', $id)
            ->delete();
        return $result;
    }

    /**
     * 更改状态
     * @param int $id;  服务编号
     * @return [type] [description]
     */
    public static function updateStatus($id, $status, $field){
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];

        if ($id < 1) {
            $result['code'] = 30214;
            return $result;
        }
        if($field == 'is_check' && $status == 1){
            $info = '已通过审核';
        } else if($field == 'is_check' && $status == -1){
            $info = '已被拒绝';
        } else if($field == 'status' && $status == 1){
            $info = '已经开启';
        } else {
            $info = '已经关闭';
        }
        //$status = $status > 0 ? 1 : -1;
        // DB::connection()->enableQueryLog();
        if(is_array($id)){
            ForumPosts::whereIn('id',$id)->update([$field => $status]);
            foreach ($id as $value) {
                $forumposts = ForumPosts::where('id', $value)
                    ->first();
                //写入论坛消息
                $content = '您的帖子『' . $forumposts->title . '』。' . $info;
                $bl = ForumMessageService::create(1, '系统消息', $content, $forumposts->user_id, $forumposts->user_id, $forumposts->id, 0);
                if(!$bl){
                    throw new Exception("Error Processing Request", 1);
                }
            }
        } else {
            ForumPosts::where('id',$id)->update([$field => $status]);
            $forumposts = ForumPosts::where('id', $id)
                ->first();
            //写入论坛消息
            $content = '您的帖子『' . $forumposts->title . '』。' . $info;
            $bl = ForumMessageService::create(1, '系统消息', $content, $forumposts->user_id, $forumposts->user_id, $forumposts->id, 0);
            if(!$bl){
                throw new Exception("Error Processing Request", 1);
            }
        }
        // print_r(DB::getQueryLog());exit;
        return $result;
    }

    /**
     * 更新信息
     */
    public static function update($id, $key, $val){
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];
        $val = $val ? 1 : 0;
        $forumposts = ForumPosts::where('id', $id)
            ->with('user')
            ->first();
        if(in_array($key, ['top', 'hot'])){
            ForumPosts::where('id',$id)->update([$key => $val]);
            if($key == 'top'){
                //写入论坛消息
                $content = '系统顶置了您的帖子『' . $forumposts->title . '』。';
                $bl = ForumMessageService::create(1, '系统消息', $content, 0, $forumposts->user_id, $forumposts->id, 0);
                if(!$bl){
                    throw new Exception("Error Processing Request", 1);
                }
            }
        } else {
            $result['code'] = 99999;
        }
        return $result;
    }

}
