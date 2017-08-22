<?php 
namespace YiZan\Services\Buyer;

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
     * @param  [type] $clientType  [客户端类型]
     * @return [type]           [description]
     */
	public static function create($userId, $content, $clientType) 
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
        $feedback->client_type = $clientType;
        $feedback->client_info = $_SERVER['HTTP_USER_AGENT'];
        $feedback->create_time = UTC_TIME;
        $feedback->status = 0;
        $feedback->save();
        return $result;

	}
    
}
