<?php 
namespace YiZan\Services;

use YiZan\Models\GoodsComplain;
use YiZan\Utils\String;;
use YiZan\Utils\Time;
use DB, Validator, Lang;

/**
 * 服务举报
 */
class GoodsComplainService extends BaseService 
{
    /**
     * 未处理
     */
    const STATUS_NO = 1;
    /**
     * 已处理
     */
    const STATUS_OK = 2;
	/**
     * 服务举报列表
     * @param  int $disposeAdminId 处理人员
     * @param  int $beginTime 开始时间
     * @param  int $endTime 结束时间
     * @param  int $status 回复状态
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          服务举报信息
     */
	public static function getList($disposeAdminId, $beginTime, $endTime, $status, $page, $pageSize) 
    {
		$list = GoodsComplain::orderBy('id', 'desc');
        
        if($disposeAdminId == true)
        {
            $list->where('dispose_admin_id', $disposeAdminId);
        }
        
        if($beginTime == true)
        {
            $list->where('create_time', '>=', Time::toTime($beginTime));
        }
        
        if($endTime == true)
        {
            $list->where('create_time', '<=', Time::toTime($endTime));
        }
        
        if($status == self::STATUS_NO)
        {
            $list->where('status', 0);
        }
        else if($status == self::STATUS_OK)
        {
            $list->where('status', ">", 0);
        }
        
        $totalCount = $list->count();
		$list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('seller','goods','user','adminUser','staff')
            ->get()
            ->toArray();
        
        return ["list"=>$list, "totalCount"=>$totalCount];
	}
    /**
     * 处理服务举报
     * @param int  $id 服务举报id
     * @param  string $content 处理结果
     * @param  int $adminId 处理人
     * @return array   处理结果
     */
	public static function dispose($id, $content, $adminId) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.update_info')
		];

        if($content == false)
        {
            $result['code'] = 30302; // 处理结果不能为空
            
	    	return $result;
        }
        
        GoodsComplain::where('id', $id)->update(array('dispose_result' => $content, 'dispose_time'=>Time::getTime(), "dispose_admin_id"=>$adminId, "status"=>1));
        
		return $result;
	}
}
