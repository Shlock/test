<?php 
namespace YiZan\Services\Wap;
use YiZan\Models\Article;
/**
 * 文章
 */
class ArticleService extends \YiZan\Services\ArticleService
{ 
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
}
