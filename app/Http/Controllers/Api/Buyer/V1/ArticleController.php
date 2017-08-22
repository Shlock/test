<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\ArticleService;
use Lang, Validator;

/**
 * 文章管理
 */
class ArticleController extends BaseController 
{ 
    /**
     * 公告列表
     */
    public function lists(){
    	$result = ArticleService::getList(intval($this->request('sellerId')));
    	$this->outputData($result);
    }

    public function get() {
        $result = ArticleService::getById(intval($this->request('id'))); 
        $this->outputData($result == false ? [] : $result->toArray());
    }

    public function read(){
    	$result = ArticleService::readArticle(intval($this->request('id')));
    	$this->output($result);
    }
}