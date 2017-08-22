<?php 
namespace YiZan\Services\Buyer;

use YiZan\Models\SellerComplain;
use YiZan\Services\SellerService;
use YiZan\Utils\Time;
use Lang;

/**
 * 服务人员举报
 */
class SellerComplainService extends \YiZan\Services\BaseService 
{
   /**
     * [create 服务人员举报增加]
     * @param  [type] $userId   [用户编号]
     * @param  [type] $sellerId [服务人员编号]
     * @param  [type] $content  [举报内容]
     * @return [type]           [description]
     */
    public static function create($userId, $sellerId, $content) 
    {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg' => Lang::get('api.success.goods_complain_create')
        );
        $seller = SellerService::getById($sellerId);
        if ($sellerId < 1 || empty($seller)) {
            $result['code'] = 90001;
            return $result; 
        }
        if ($content == '') {
            $result['code'] = 90002;
            return $result;
        }
        $seller_complain = new SellerComplain;
        $seller_complain->seller_id = $sellerId;
        $seller_complain->user_id = $userId;
        $seller_complain->content = $content;
        $seller_complain->create_time = UTC_TIME;
        $seller_complain->status = 0;
        $seller_complain->save();
        return $result;

    } 
}
