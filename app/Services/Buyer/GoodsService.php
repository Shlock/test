<?php namespace YiZan\Services\Buyer;
      
use YiZan\Models\Buyer\GoodsType;
use YiZan\Models\Buyer\Goods;
use YiZan\Models\GoodsCate;
use YiZan\Models\Order;
use YiZan\Models\Seller;
use YiZan\Services\AdvService;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use DB;
class GoodsService extends \YiZan\Services\GoodsService {
    
    //商家状态检测
    private static function sellerCheck($id){  
        $seller = Seller::find($id);
        if(empty($seller) || $seller->is_check < 1 || $seller->status == 0 ){
            return false;
        }
        return true;
    }

    /**
     * 获取商品类的列表
     * @param [int] $id 商家编号
     */
    public static function getSellerGoodsLists($id){ 
    
        if(!self::sellerCheck($id)){
            return null;
        }
        $list = GoodsCate::where('seller_id', $id)
                         ->where('type', Goods::SELLER_GOODS)
						 ->where('status', 1)
                         ->with(['goods' => function($query) use($id) {
                                    $query->where('seller_id', $id)
                                          ->where('status', 1)
                                          ->orderBy('id', 'desc');
                                },'goods.norms' => function($query) use ($id){
                                    $query->where('seller_id', $id);
                                },'goods.extend'])
                         ->orderBy('sort','asc')->get()->toArray();
        foreach ($list as $key=>$val) {
            foreach ($val['goods'] as $k=>$v) {
                $list[$key]['goods'][$k]['salesCount'] = 0;
                if (!empty($v['extend'])) {
                    $list[$key]['goods'][$k]['salesCount'] = $v['extend']['salesVolume'];
                }
                unset($list[$key]['goods'][$k]['extend']);
            }
        }
        return $list;
    }

    /**
     * 获取服务类的列表
     * @param [int] $id 商家编号
     */
    public static function getSellerServiceLists($id, $page, $pageSize){
        if(!self::sellerCheck($id)){
            return null;
        }
        $list = Goods::where('seller_id', $id)
                     ->where('type', Goods::SELLER_SERVICE)
                     ->where('status', 1)
                     ->skip(($page - 1) * $pageSize)
                     ->take($pageSize)
                     ->orderBy('id', 'desc')
                     ->get()
                     ->toArray();
        foreach ($list as $k=>$v) {
            $list[$k]['duration'] = $v['unit'] == 1 ? $v['duration'] * 60 : $v['duration'];
        }
        return $list;
    }

    /**
     * 根据编号获取服务
     * @param  integer $goodsId 服务编号
     * @param  integer $userId  会员编号
     * @return array            服务信息
     */
    public static function getById($goodsId, $userId = 0) {
        $goods = Goods::with('extend', 'norms', 'seller');
        if ($userId > 0) {
            $goods->with(['collect' => function($query) use($userId) {
                    $query->where('user_id', $userId);
                }]);
        }
        $goods = $goods->find($goodsId);
        
        if(!self::sellerCheck($goods->seller_id)){
            return null;
        }
        if ($goods) {
            $goods = $goods->toArray();
            $goods['salesCount'] = !empty($goods['extend']) ? $goods['extend']['salesVolume'] : 0;
        } else {
            $goods = [];
        }
        return $goods;
    }

    /**
     * [getList description]
     * @param  [int] $restaurantId       餐厅编号
     * @param  [int] $type       送餐类型 1:即时送餐 2:预约午餐
     */
    public static function getList($restaurantId, $serviceType){
        $list = [];
        $type = [];
        $goods = Goods::where('restaurant_id', $restaurantId);
        if ($serviceType == 1) {
            $goods->whereIn('join_service',['1','3']);
        }
        if ($serviceType == 2) {
            $goods->whereIn('join_service',['2','3']);
        }
        $data = $goods->with('extend', 'goodsType')
                        ->orderBy('type_id','desc')
                        ->orderBy('id', 'desc')->get()->toArray();
        foreach ($data as $key=>$val) {
            $typeId = $val['goodsType']['id'];
            $type[$typeId] = $val['goodsType'];
            $list[$typeId]['goods'][] = [
                'id'    => $val['id'],
                'name'  => $val['name'],
                'price' => $val['price'],
                'logo'  => $val['logo'],
                'url'   => $val['url'],
                'saleCount' => $val['extend']['salesVolume'],
            ];
            $list[$val['goodsType']['id']] = array_merge($type[$typeId], $list[$typeId]);
        }
        return array_values($list);
    }


    /**
     * [getServiceList 服务列表]
     * @param  [type] $id       [分类编号]
     * @param  [type] $page     [分页页码]
     * @param  [type] $pageSize [分页条数]
     * @return [type]           [返回数组]
     */
    public static function getServiceList($id, $page, $pageSize) {
        $type = [
            '1' => '外卖',
            '2' => '跑腿',
            '3' => '家政',
            '4' => '汽车',
            '5' => '其他'
        ];
        $list = [
            'name' => $type[$id],
            'banner' => [],
            'serviceList' => []
        ];

        $advType = [
            '2' => 'BUYER_RUN_BANNER',
            '3' => 'BUYER_HOUSE_BANNER',
            '4' => 'BUYER_CAR_BANNER',
            '5' => 'BUYER_OTHER_BANNER'
        ];
        $advCode = $advType[$id];
        $list['banner'] = AdvService::getAdvByCode($advCode,0);
        $list['serviceList'] = Goods::where('type', $id)
                                    ->where('status', '1')
                                    ->selectRaw("*, (select count(1) from ".env('DB_PREFIX')."order as o where o.goods_id = ".env('DB_PREFIX')."goods.id) as saleCount,
                                    (select count(1) from ".env('DB_PREFIX')."order_rate as r where r.goods_id = ".env('DB_PREFIX')."goods.id) as commentCount")
                                    ->skip(($page - 1) * $pageSize)
                                    ->take($pageSize)
                                    ->get()
                                    ->toArray();
        return $list;
    }

    /**
     * 获取服务详情
     * @param int $id 服务编号
     * @param int $type 服务类型（为空则为跑腿和家政 直接查ID）
     */
    public static function getServiceDetail($id) {
        $data = Goods::where('id', $id)
                     ->where('status', '1')
                     // ->where('seller_id', '0')
                     ->selectRaw("*, (select count(1) from ".env('DB_PREFIX')."order as o where o.goods_id = ".env('DB_PREFIX')."goods.id) as saleCount,
                                    (select count(1) from ".env('DB_PREFIX')."order_rate as r where r.goods_id = ".env('DB_PREFIX')."goods.id) as commentCount")
                     ->first();
        return $data;
    }

    /**
     * 检查商品是否被限制购买
     * @param  Object $goods     商品
     * @param  int    $normsId    规格编号
     * @param  int    $userId    会员编号
     * @param  int    $amount    数量
     * @return boolean           是否可以购买 返回true表示可以买，返回false表示不可以买
     */
    public static function checkGoodsLimit($goods, $normsId, $userId, $amount){
        DB::connection()->enableQueryLog();
        if(($goods->buy_limit <= 0 && $goods->type == 1) || $goods->type == 2){
            return true;
        } 
        $data = [
            'goodsId' => $goods->id,
            'normsId' => $normsId
        ]; 
        $dbPrefix = DB::getTablePrefix();
        //获取当前商品的总的已购买数量
        $num = Order::where('user_id', $userId)
                    ->whereNotIn('status', [ORDER_STATUS_CANCEL_USER, 
                                            ORDER_STATUS_CANCEL_AUTO, 
                                            ORDER_STATUS_CANCEL_SELLER,
                                            ORDER_STATUS_CANCEL_ADMIN, 
                                            ORDER_STATUS_USER_DELETE, 
                                            ORDER_STATUS_SELLER_DELETE, 
                                            ORDER_STATUS_ADMIN_DELETE, 
                                            ORDER_STATUS_REFUND_SUCCESS])
                    ->join('order_goods', function($join) use($data) {
                        $join->on('order_goods.order_id', '=', 'order.id')
                             ->where('order_goods.goods_norms_id', '=', $data['normsId'])
                             ->where('order_goods.goods_id', '=', $data['goodsId']);
                    })
                    ->select(DB::raw('IFNULL(sum('.$dbPrefix.'order_goods.num), 0) as total_num'))
                    ->first();
        //file_put_contents('/mnt/wwwroot/o2o/storage/logs/ccccc.log', print_r(DB::getQueryLog(), true), FILE_APPEND); 
        if($num) {
            $num = $num->toArray();
            $current_amount = $num['totalNum'] + $amount;
            
        } else {
            $current_amount = $amount;
        }

        if($current_amount > $goods->buy_limit) {
            return false;
        } else {
            return true;
        }
    }

}
