<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\ArticleService;
use Lang, Validator;

/**
 * 公告
 */
class ArticleController extends BaseController 
{ 
	//获取公告列表
	public function lists(){
		$result = ArticleService::getList(
			$this->sellerId,
			$this->request('title'),
            max((int)$this->request('page'), 1), 
            (int)$this->request('pageSize', 20)
			);
		$this->outputData($result);
	}

    /**
     * 获取公告详情
     */
    public function get() {
        $result = ArticleService::getById($this->sellerId, intval($this->request('id'))); 
        $this->outputData($result == false ? [] : $result->toArray());
    }

    /**
     * 保存公告
     */
    public function save(){
    	$result = ArticleService::save(
    			$this->sellerId,
    			intval($this->request('id')),
    			$this->request('title'),
    			$this->request('content'),
    			intval($this->request('sort')),
    			max((int)$this->request('status'),1)
    		);
    	$this->output($result);
    }

    /**
     * 删除公告
     */
    public function delete(){
    	$result = ArticleService::delete($this->sellerId, intval($this->request('id')));
    	$this->output($result);
    }

}