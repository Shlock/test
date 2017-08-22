<?php namespace YiZan\Services\Buyer;

use YiZan\Services\Buyer\GoodsService;
use YiZan\Models\Buyer\ShoppingCart;
use YiZan\Models\Goods; 
use YiZan\Utils\Time;
use Lang, Validator, DB;

class ShoppingService extends \YiZan\Services\ShoppingService {
    /**
     * [save 购物车]
     * @param  [int] $userId           [会员编号]
     * @param  [int] $goodsId          [商品/服务编号]
     * @param  [int] $normsId          [规格编号]
     * @param  [int] $num              [数量] 
     * @param  [string] $serviceTime   [预约时间] 
     * @return [array]                 [返回数组]
     */
    public static function save($userId, $goodsId, $normsId, $num, $serviceTime) {   
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => Lang::get('api_staffwap.success.set')
        );

        if($userId <= 0) {
            $result['code'] = 50219;
            $result['data'] = self::getCartInfo($userId);
            return $result;
        }

        if($goodsId <= 0) {
            $result['code'] = 50220;
            $result['data'] = self::getCartInfo($userId);
            return $result;
        }
        
        $serviceTime = Time::toTime($serviceTime);
        
        //查询购物车是否存在商品  
        $shoppingCart = ShoppingCart::where('user_id', $userId)
                                    ->where('goods_id', $goodsId);
        if($normsId > 0){
            $shoppingCart->where('norms_id', $normsId);
        } 
        $shoppingCart = $shoppingCart->first();
        $goods = Goods::where('id', $goodsId); 
        if($normsId > 0){ 
            $goods->with(['norms' => function($query) use($normsId) {
                $query->where('id', $normsId);
            }]); 
        }
        $goods = $goods->first();  
        $isHasStock = true;    
        //判断库存
        if($normsId > 0){
            if($goods->norms[0]['stock'] < $num) { 
                $isHasStock = false;
            }
        } else {
            if($goods->stock < $num) {
                $isHasStock = false;
            }
        }
        if(!$isHasStock && $goods->type == Goods::SELLER_GOODS){
            $result['code'] = 50224;
            $result['data'] = self::getCartInfo($userId);
            return $result;
        }

        //如果是添加商品 不判断 限制条件
        if(empty($shoppingCart) || $shoppingCart['num'] <= $num){
            //判断是否限制购买数量
            if(!GoodsService::checkGoodsLimit($goods, $normsId, $userId, $num)){
                $result['code'] = 50223;
                $result['data'] = self::getCartInfo($userId);
                return $result;
            } 
        }  

        //如果购物车存在此商品就更新，否者就插入一条购物车数据
        if($shoppingCart){
            //如果数量为0 则删除此条购物车信息
            if($num <= 0) {
                ShoppingCart::where('id', $shoppingCart->id)
                            ->delete();
            } else {
                $cartInfo = ['num' => $num];
                if($shoppingCart->type == Goods::SELLER_SERVICE){
                    $cartInfo['service_time'] = $serviceTime;
                } else {
                    $cartInfo['norms_id'] = $normsId;
                }
                ShoppingCart::where('id', $shoppingCart->id)
                            ->update($cartInfo);
            }
        } else {//判断商品是否存在
            if($num <= 0){
                $result['data'] = self::getCartInfo($userId);
                return $result;
            }
            $goods_data = Goods::where('id', $goodsId)
                               ->with(['norms'=>function($query) use($normsId){
                                    $query->where('id', $normsId);
                               }])
                               ->first(); 
            if(empty($goods_data) || $goods_data->status == STATUS_DISABLED) {
                $result['code'] = 50220;
                $result['data'] = self::getCartInfo($userId);
                return $result;
            } else {
                if(empty($goods_data->norms)){
                    $result['code'] = 50221;
                    $result['data'] = self::getCartInfo($userId);
                    return $result;
                } 
            }
            $cart_item = new ShoppingCart();
            $cart_item->user_id         = $userId;
            $cart_item->seller_id       = $goods_data->seller_id;
            $cart_item->goods_id        = $goods_data->id;
            $cart_item->norms_id        = $normsId;
            $cart_item->num             = $num;
            $cart_item->price           = $goods_data->price;
            $cart_item->type            = $goods_data->type;
            $cart_item->service_time    = $serviceTime;
            $cart_item->save();
            if($cart_item->id < 0) {
                $result['code'] = 50222;
                $result['data'] = self::getCartInfo($userId);
            }
        } 
        $result['data'] = self::getCartInfo($userId);
        return $result;
    }

    /**
     * [delete 清空购物车]
     * @param  [type] $userId [会员ID]
     * @return [type]         [description]
     */
    public static function delete($userId, $id) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => Lang::get('api_staffwap.success.delete')
        );
        $res = ShoppingCart::where("user_id", $userId);
        if($id > 0){
            $res->where('id', $id);
        }
        $res->delete(); 
        return $result;

    }

    /**
     * [lists 查看购物车]
     * @param  [type] $userId [会员ID]
     * @return [type]         [description]
     */
    public static function lists($userId) {
        $list =  self::getCartInfo($userId);
        return $list;
    }

    /**
     * [updateCart 更新购物车]
     * @param [int] $id     [购物车项目编号]
     * @param [int] $num    [数量] 
     * @array               [结果]
     */
    public static function updateCart($id, $num){
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => '',
        );
        $data = ShoppingCart::where('id', $id);
        if($num <= 0){
            $data->delete();
        } else {
            $data->update(['num'=>$num]);
        }
        return $result;
    }

    /**
     * [getCartInfo 获取会员购物车信息]
     * @param [int] $userId     [会员编号]
     * @param [array] $result   [购物车信息]
     */
    public static function getCartInfo($userId) {  
        $result = ShoppingCart::where('user_id', $userId)
                              ->groupBy('seller_id', 'type')
                              ->select(DB::raw('sum(price) as price, seller_id as seller_id, type'))
                              ->with('seller')
                              ->get()
                              ->toArray(); 
        $data = [];  
        foreach ($result as $key => $value) {
            $item = array();
            $item['id'] = $value['sellerId'];
            $item['name'] = $value['seller']['name']; 
            $item['logo'] = $value['seller']['logo']; 
            $item['serviceFee'] = $value['seller']['serviceFee']; 
            $item['deliveryFee'] = $value['seller']['deliveryFee']; 
            $item['type'] = $value['type'];
            $item['countGoods'] = Goods::where('type', 1)->where('seller_id', $value['sellerId'])->where('status', 1)->count('id');
            $item['countService'] = Goods::where('type', 2)->where('seller_id', $value['sellerId'])->where('status', 1)->count('id');
            $seller_cart_info = ShoppingCart::where('seller_id', $value['sellerId'])
                                            ->where('user_id', $userId)
                                            ->where('type', $value['type'])
                                            ->with('goods', 'goods.collect', 'norms')
                                            ->get()
                                            ->toArray();
            $total_price = 0;
            $goods = array(); 
            foreach ($seller_cart_info as $key1 => $value1) {
                $goods_item = array();
                $goods_item['id']           = $value1['id'];
                $goods_item['type']         = $value1['goods']['type'];
                $goods_item['goodsId']      = $value1['goodsId'];
                $goods_item['normsId']      = $value1['normsId'];
                $goods_item['name']         = $value1['goods']['name'];
                $goods_item['num']          = $value1['num'];
                $goods_item['logo']         = $value1['goods']['logo'];
                $goods_item['duration']     = $value1['goods']['duration'];
                if($value1['norms']){
                    $goods_item['price']    = $value1['norms']['price'];
                    $goods_item['name']     = $goods_item['name'] .'('. $value1['norms']['name'].')';
                } else {
                    $goods_item['serviceTime'] = Time::toDate($value1['serviceTime'], 'Y-m-d H:i:s');
                    $goods_item['price']        = $value1['goods']['price'];
                }
                $total_price += $goods_item['price'] * $value1['num'];
                if($value1['goods']['collect']){
                    $goods_item['isCollect'] = 1;
                } else {
                    $goods_item['isCollect'] = 0;
                }
                $goods[] = $goods_item;
            } 
            $item['price'] = $total_price;
            $item['goods'] = $goods;
            $data[] = $item;
        }     
        return $data;
    }


    /**
     * 根据编号获取信息
     * @param int $userId 会员编号
     * @param array $ids 购物车编号
     */
    public static function getCartList($userId, $ids) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => ''
        ];
        $check = ShoppingCart::where('user_id', $userId)
                            ->whereIn('id', $ids);
        $checkSeller = $check->groupBy('seller_id')
                        ->select('seller_id')
                        ->get()->toArray();
        if (count($checkSeller) > 1) {
            $result['code'] = 60511;
            return $result;
        }

        $checkSeller = $check->groupBy('type')
                        ->select('type')
                        ->get()->toArray();
        if (count($checkSeller) > 1) {
            $result['code'] = 60512;
            return $result;
        }

        $checkService = $check->where('type', 2)->count();
        if ($checkService > 1) {
            $result['code'] = 60513;
            return $result;
        }

        $result['data'] = ShoppingCart::where('user_id', $userId)
                    ->whereIn('id', $ids)
                    ->with('goods', 'seller.extend', 'norms')
                    ->get()->toArray();
        return $result;
    }
}
