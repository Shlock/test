<?php 
namespace YiZan\Services\System;

use YiZan\Models\ForumComplain; 
use YiZan\Utils\Time;
use Lang;

/**
 * 帖子举报
 */
class ForumComplainService extends \YiZan\Services\ForumComplainService 
{
   /**
     * [getLists 帖子举报列表] 
     */
    public static function getLists($keywords, $status, $page, $pageSize) { 
        $list = ForumComplain::orderBy('create_time', 'DESC');
        if($keywords){
            $list->where('content', 'like', "%{$keywords}%");
        }
        if($status > 0){
            $list->where('status', $status - 2);
        }
        $totalCount = $list->count();

        $list = $list->skip(($page-1) * $pageSize)
                     ->take($pageSize)
                     ->with('posts','user')
                     ->get()
                     ->toArray();
        return ["list"=>$list, "totalCount"=>$totalCount];
    } 

    /**
     * 删除帖子举报
     */
    public static function delete($id){
        $result = 
        [
            'code'  => 0,
            'data'  => null,
            'msg'   => '删除成功'
        ];

        try{
            ForumComplain::where('id', $id)
                         ->delete();
        } catch(Exception $e) {
            $result['code'] = 30920;
        }
        return $result;
    }

    /**
     * 处理帖子举报
     */
    public static function dispose($adminId, $id, $remark, $status){ 
         $result =
            [
                'code'  => 0,
                'data'  => null,
                'msg'   => ""
            ];
        $forumcomplain = ForumComplain::find($id);

        if(!$forumcomplain){
            $result['code'] = 30918;
            return $result;
        }

        if($forumcomplain->status != 0 || $status == 0){
            $result['code'] = 30919;
            return $result;
        }

        $forumcomplain->status = $status;
        $forumcomplain->dispose_time = UTC_TIME;
        $forumcomplain->dispose_result = $remark;
        $forumcomplain->dispose_admin_id = $adminId;    
        $forumcomplain->save();
        return $result;
    }
}
