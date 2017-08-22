<?php 
namespace YiZan\Http\Controllers\Api\Buyer;

use YiZan\Services\Buyer\HotWordsService;
use Input;
/**
 * 热搜关键词管理
 */
class HotwordsController extends BaseController 
{
   	/**
     * 列表
	 */
	public function lists()
    {
        $data = HotWordsService::getLists(  
            (int)$this->request('cityId'),   
            $this->request('pageSize')?(int)$this->request('pageSize'):5
        );
        
		$this->outputData($data);
    } 

}