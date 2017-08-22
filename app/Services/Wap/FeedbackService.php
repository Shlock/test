<?php 
namespace YiZan\Services\Wap;

use YiZan\Models\Feedback;
use YiZan\Utils\Time;
use Lang;

/**
 * 意见反馈
 */
class FeedbackService extends \YiZan\Services\BaseService 
{

	/**
     * [create 意见反馈增加]
     * @param  [type] $userId   [用户编号]
     * @param  [type] $content  [反馈内容]
     * @return [type]           [description]
     */
	public static function create($userId, $content) 
    {
		$result = array(
            'code'  => 0,
            'data'  => null,
            'msg' => Lang::get('api.success.feedback_create')
        );
       
        if ($content == '') {
            $result['code'] = 70002;
            return $result;
        }
        $feedback = new Feedback;
        $feedback->type = 'buyer';
        $feedback->user_id = $userId;
        $feedback->content = $content;
        $feedback->client_type = 'wap';
        $feedback->create_time = UTC_TIME;
        $feedback->status = 0;
        $feedback->save();
        return $result;

	}
    
}
