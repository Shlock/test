<?php 
namespace YiZan\Services\Buyer;

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
     */
	public static function getList($sellerId) {
        $list = Article::where('seller_id', $sellerId)
					   ->where('status', 1)
                       ->orderBy('id', 'desc')
					   ->get()
					   ->toArray(); 
        foreach ($list as $key => $value) {
            $list[$key]['readTime'] = $value['readTime'] ? yztime($value['readTime']) : 0;
            $list[$key]['createTime'] = yztime($value['createTime']);
            $list[$key]['url'] = u('Wap#Article/propertyarticle',array('id'=>$value['id']));
        }     
        return $list;
	}


	public static function readArticle($id) 
    {

        return Article::where("id", $id)->update(['read_time'=>UTC_TIME]);
        
    }
}
