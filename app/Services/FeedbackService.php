<?php 
namespace YiZan\Services;

use YiZan\Models\Feedback;
use YiZan\Utils\String;;
use YiZan\Utils\Time;
use DB, Validator, Lang;

/**
 * 意见反馈
 */
class FeedbackService extends BaseService 
{
	/**
     * 意见反馈列表
     * @param  string $type 类型
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          意见反馈信息
     */
	public static function getList($type, $page, $pageSize) 
    {
		$list = Feedback::orderBy('id', 'desc');
        
        if($type == true)
        {
            $list->where('type', $type);
        }
                
        $totalCount = $list->count();
        
		$list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('seller','user','adminUser')
            ->get()
            ->toArray();
        
        return ["list"=>$list, "totalCount"=>$totalCount];
	}
    /**
     * 意见反馈举报
     * @param int  $id 意见反馈id
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
        
        Feedback::where('id', $id)->update(array('dispose_result' => $content, 'dispose_time'=>Time::getTime(), "dispose_admin_id"=>$adminId, "status"=>1));
        
		return $result;
	}
    /**
     * 删除意见反馈
     * @param int  $id 意见反馈id
     * @return array   删除结果
     */
	public static function delete($id) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.delete_info')
		];
        
		Feedback::where('id', $id)->delete();
        
		return $result;
	}
}
