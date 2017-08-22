<?php 
namespace YiZan\Services;

use YiZan\Models\Article;
use YiZan\Utils\String;
use DB, Validator;
/**
 * 文章
 */
class ArticleService extends BaseService 
{
	/**
     * 文章列表
     * @param string $title 文章标题
     * @param int $cateId 分类编号
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          文章信息
     */
	public static function getList($title, $cateId, $page, $pageSize) 
    {
        $list = Article::orderBy('id', 'desc')->where('seller_id', 0)->where('cate_id', '>', 0);
        
        if($title == true)
        {
            $list->where("title", "LIKE", "%{$title}%");
        }
        
        if($cateId == true)
        {
            $list->where("cate_id", $cateId);
        }
        
		$totalCount = $list->count();
        
		$list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('cate')
            ->get()
            ->toArray();
        
        return ["list"=>$list, "totalCount"=>$totalCount];
	}
    /**
     * 添加文章
     * @param string $title 文章标题
     * @param int $cateId 分类编号
     * @param string $brief 简介
     * @param string $image 图片
     * @param string $content 详细
     * @param int $sort 排序
     * @param int $status 状态
     * @return array   创建结果
     */
    public static function create($title, $cateId, $brief, $image, $content, $sort, $status) 
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

		$rules = array(
			'title'         => ['required'],
			'cateId'        => ['min:1'],
			'content'       => ['required']
		);

		$messages = array
        (
            'title.required'	    => 60101,	// 请输入标题
            'cateId.min'	        => 60102,	// 请选择分类
            'content.required'	    => 60103	// 请输入详细
        );

		$validator = Validator::make(
            [
				'title'      => $title,
				'cateId'     => $cateId,
				'content'    => $content
			], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }

        $cate = new Article();
  
        $cate->title     = $title;
        $cate->cate_id   = $cateId;
        $cate->brief     = $brief;
        $cate->image     = $image;
        $cate->content   = $content;
        $cate->sort 	 = $sort;
        $cate->status 	 = $status;
        
        $cate->save();
        
        return $result;
    }
    /**
     * 更新文章
     * @param int $id 文章id
     * @param string $title 文章标题
     * @param int $cateId 分类编号
     * @param string $brief 简介
     * @param string $image 图片
     * @param string $content 详细
     * @param int $sort 排序
     * @param int $status 状态
     * @return array   创建结果
     */
    public static function update($id, $title, $cateId, $brief, $image, $content, $sort, $status) 
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

		$rules = array(
			'title'         => ['required'],
			'cateId'        => ['min:1'],
			'content'       => ['required']
		);

		$messages = array
        (
            'title.required'	    => 60101,	// 请输入标题
            'cateId.min'	        => 60102,	// 请选择分类
            'content.required'	    => 60103	// 请输入详细
        );

		$validator = Validator::make(
            [
				'title'      => $title,
				'cateId'     => $cateId,
				'content'    => $content
			], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }
        
        Article::where("id", $id)->update(array(
               'title'    => $title,
               'cate_id'  => $cateId,
               'brief'    => $brief,
               'image'    => $image,
               'content'  => $content,
               'sort' 	  => $sort,
               'status'   => $status
           ));
        
        return $result;
    }
    /**
     * 获取文章
     * @param  int $id 文章id
     * @return array   文章
     */
	public static function getById($id) 
    {
		return Article::where('id', $id)
            ->with('cate')
		    ->first();
	}
    /**
     * 删除文章
     * @param string  $ids 文章id
     * @return array   删除结果
     */
	public static function delete($ids) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		];
        
        self::replaceIn($ids);
  
		Article::whereRaw("id IN ({$ids})")->delete();
        
		return $result;
	}
}
