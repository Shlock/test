<?php 
namespace YiZan\Services\Buyer;

use YiZan\Models\OrderComplain;
use YiZan\Services\SellerStaffService;
use YiZan\Utils\Time;
use Lang;

/**
 * 订单举报服务类
 */
class OrderComplainService extends \YiZan\Services\OrderComplainService
{  

    /**
     * [getLists 获取订单举报列表]
     * @param  [type] $userId  [会员编号] 
     * @return [type]          [description]
     */
    public static function getLists($userId, $page, $pageSize = 20){
        $list = OrderComplain::where('user_id', $userId)
                             ->with('order','staff')
                             ->skip(($page - 1) * $pageSize)
                             ->take($pageSize)
                             ->get();
        $list = $list ? $list->toArray() : null;

        foreach ($list as $key => $value) {
        	$list[$key]['url'] = u('wap#UserCenter/appreportdetail',['id'=>$value['id']]);
        }

        return $list;
    }

}
