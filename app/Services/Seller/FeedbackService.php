<?php 
namespace YiZan\Services\Seller;

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
     * @param  [type] $sellerId   [商家编号]
     * @param  [type] $content  [反馈内容]
     * @param  [type] $clientType  [客户端类型]
     * @return [type]           [description]
     */
	public static function create($sellerId, $content, $clientType) 
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
        $feedback->type = 'seller';
        $feedback->seller_id = $sellerId;
        $feedback->content = $content;
        $feedback->client_type = $clientType;
        $feedback->create_time = UTC_TIME;
        $feedback->status = 0;
        $feedback->save();
        return $result;

	}
    
}
