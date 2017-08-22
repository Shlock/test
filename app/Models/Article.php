<?php 
namespace YiZan\Models;

/**
 * 文章
 */
class Article extends Base 
{
    public function cate()
    {
        return $this->belongsTo('YiZan\Models\ArticleCate');
    }
    
    public function seller(){
        return $this->belongsTo('YiZan\Models\Seller', 'seller_id', 'id');
    }
}
