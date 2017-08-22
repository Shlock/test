<?php 
namespace YiZan\Http\Controllers\Api\Sellerweb;

use YiZan\Services\Sellerweb\RegionService;
use Input;
/**
 * 开通城市管理
 */
class CityController extends BaseController 
{
	/**
     * 开通城市列表
	 */
	public function lists()
    {
        $data = RegionService::getOpenCitys();  
		$this->outputData($data);
    }

}