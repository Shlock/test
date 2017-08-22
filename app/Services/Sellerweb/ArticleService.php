<?php 
namespace YiZan\Services\Sellerweb;

use YiZan\Models\Article;
use YiZan\Utils\String;
use DB, Validator;
/**
 * 公告
 */
class ArticleService extends \YiZan\Services\ArticleService{
	/**
     * 公告列表
     * @param  int $sellerId 商家编号
     * @param  string $title 文章标题 
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          公告
     */
	public static function getList($sellerId, $title, $page, $pageSize) {
        $list = Article::where('seller_id', $sellerId)
                       ->orderBy('id', 'desc');
        
        if($title == true)
        {
            $list->where("title", "LIKE", "%{$title}%");
        } 
        
		$totalCount = $list->count();
        
		$list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize) 
            ->get()
            ->toArray();
        
        return ["list"=>$list, "totalCount"=>$totalCount];
	}
    /**
     * 添加公告章
     * @param int $sellerId 商家编号  
     * @param int $id 公告编号 
     * @param string $title 公告标题  
     * @param string $content 公告 
     * @param int $sort 公告排序  
     * @param int $status 公告状态  
     * @return array   创建结果
     */
    public static function save($sellerId, $id, $title, $content, $sort, $status){
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

		$rules = array(
			'title'         => ['required'], 
			'content'       => ['required']
		);

		$messages = array
        (
            'title.required'	    => 60101,	// 请输入标题 
            'content.required'	    => 60103	// 请输入详细
        );

		$validator = Validator::make(
            [
				'title'      => $title, 
				'content'    => $content
			], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }
        if($id > 0){
            $article = Article::where('seller_id', $sellerId)
                              ->where('id', $id)
                              ->first();
            if(empty($article)){
                $article = new Article();
            }
        } else {
            $article = new Article();
			$article->create_time   = UTC_TIME;
        } 
        $article->seller_id     = $sellerId;
        $article->title         = $title; 
        $article->content       = $content;
        $article->sort 	        = $sort;
        $article->status 	    = $status;
        $article->save();
        
        return $result;
    }
    
    /**
     * 获取文章
     * @param int $sellerId 商家编号  
     * @param  int $id 文章id
     * @return array   文章
     */
	public static function getById($sellerId, $id) 
    { 
        $result = Article::where('seller_id', $sellerId)
                         ->where('id', $id) 
                         ->first(); 
		return $result;
	}
    /**
     * 删除文章
     * @param int $sellerId 商家编号  
     * @param string  $id 文章id
     * @return array   删除结果
     */
	public static function delete($sellerId, $id) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		]; 
  
		Article::where('seller_id', $sellerId)
               ->where("id", $id)
               ->delete();
        
		return $result;
	}
}
