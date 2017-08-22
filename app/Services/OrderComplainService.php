<?php 
namespace YiZan\Services;

use YiZan\Models\OrderComplain;
use YiZan\Services\OrderService;
use YiZan\Utils\Time;
use Lang, DB;

/**
 * 订单举报
 */
class OrderComplainService extends BaseService
{
   /**
     * [create 创建订单举报]
     * @param  [type] $userId   [用户编号]
     * @param  [type] $orderId  [订单编号]
     * @param  [type] $content  [举报内容]
     * @param  [array] $images  [图片数组]
     * @return [type]           [description]
     */
    public static function create($userId, $orderId, $content, $images)
    {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg' => Lang::get('api.success.order_complain_create')
        );
        $order = OrderService::getOrderById($userId, $orderId);
        if (!$order) {
            $result['code'] = 60301;
            return $result; 
        }
        if ($content == '') {
            $result['code'] = 60302;
            return $result;
        } 

        if (count($images) > 0) {
            foreach ($images as $key => $image) {
                $images[$key] = self::moveOrderImage($orderId, $image);
                if (!$images[$key]) {
                    $result['code'] = 50004;
                    return $result;
                }
            }
            $images = implode(',', $images);
        } else {
            $images = '';
        }

        $complain = OrderComplain::where('user_id', $userId)
                                 ->where('order_id', $orderId)
                                 ->with('staff')
                                 ->first();  

        if($complain){
            $result['code'] = 60304;
            return $result;
        }
        $order_complain = new OrderComplain;
        $order_complain->order_id       = $order->id; 
        $order_complain->sn             = $order->sn; 
        $order_complain->staff_id       = $order->seller_staff_id;
        $order_complain->user_id        = $userId;
        $order_complain->content        = $content;
        $order_complain->images         = $images;
        $order_complain->create_time    = UTC_TIME;
        $order_complain->status         = OrderComplain::STATUS_NO;

        try{
            $order_complain->save();
        } catch(Exception $e){
            $result['code'] = 60303;
        }
        return $result;

    } 

    /**
     * [get 获取订单举报信息]
     * @param  [type] $userId  [会员编号]
     * @param  [type] $complainId [举报编号]
     * @return [type]          [description]
     */
    public static function get($userId, $complainId){  
        $complain = OrderComplain::where('user_id', $userId)
                                 ->where('id', $complainId)
                                 ->with('order','staff')
                                 ->first();  
        return $complain ? $complain->toArray() : null;
    }

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
        return $list ? $list->toArray() : null;
    }

    /**
     * 订单举报列表
     * @param  int $sn 订单流水号
     * @param  string $beginTime 开始时间
     * @param  string $endTime 结束时间
     * @param  int $status 回复状态
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          服务举报信息
     */
    public static function getSystemLists($sn, $beginTime, $endTime, $status, $page, $pageSize) 
    {
        $list = OrderComplain::orderBy('id', 'desc'); 
        if($sn == true)
        {
            $list->whereRaw("sn like '%".$sn."%'");
        } 
        
        if($beginTime == true)
        {
            $list->where('create_time', '>=', Time::toTime($beginTime));
        }
        
        if($endTime == true)
        {
            $list->where('create_time', '<=', Time::toTime($endTime));
        }
        
        if($status == OrderComplain::STATUS_NO)
        {
            $list->where('status', 1);
        }
        else if($status == OrderComplain::STATUS_OK)
        {
            $list->where('status', 2);
        } else if($status == OrderComplain::STATUS_BACK) 
        {
            $list->where('status', 3);
        }
        
        $totalCount = $list->count();
    
        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('user','adminUser', 'order')
            ->get()
            ->toArray();
        
        return ["list"=>$list, "totalCount"=>$totalCount];
    }
    /**
     * 处理订单举报
     * @param int  $id 服务举报id
     * @param int  $status 状态
     * @param  string $content 处理结果
     * @param  int $adminId 处理人
     * @return array   处理结果
     */
    public static function dispose($id, $status, $content, $adminId) 
    {
        $result = 
        [
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.update_info')
        ];

        if($content == false)
        {
            $result['code'] = 30302; // 处理结果不能为空
            
            return $result;
        }
        
        OrderComplain::where('id', $id)->update(array('dispose_result' => $content, 'dispose_time'=>Time::getTime(), "dispose_admin_id"=>$adminId, "status"=>$status));
        
        return $result;
    }

}
