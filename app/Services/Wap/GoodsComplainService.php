<?php 
namespace YiZan\Services\Wap;

use YiZan\Models\GoodsComplain;
use YiZan\Services\GoodsService;
use YiZan\Utils\Time;
use Lang;

/**
 * 服务举报
 */
class GoodsComplainService extends \YiZan\Services\BaseService 
{
   /**
     * [create 服务举报增加]
     * @param  [type] $userId   [用户编号]
     * @param  [type] $goodsId [服务编号]
     * @param  [type] $content  [举报内容]
     * @return [type]           [description]
     */
    public static function create($userId, $goodsId, $content) 
    {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg' => Lang::get('api.success.goods_complain_create')
        );
        $goods = GoodsService::getById($goodsId);
        if ($goodsId < 1 || empty($goods)) {
            $result['code'] = 80001;
            return $result; 
        }
        if ($content == '') {
            $result['code'] = 80002;
            return $result;
        }
        $goods_complain = new GoodsComplain;
        $goods_complain->goods_id = $goodsId;
        $goods_complain->seller_id = $goods->seller_id;
        $goods_complain->user_id = $userId;
        $goods_complain->content = $content;
        $goods_complain->create_time = UTC_TIME;
        $goods_complain->status = 0;
        $goods_complain->save();
        return $result;

    } 
}
