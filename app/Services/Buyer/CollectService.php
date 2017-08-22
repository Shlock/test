<?php 
namespace YiZan\Services\Buyer;

use YiZan\Models\Buyer\OrderRate;
use YiZan\Models\UserCollect;
use YiZan\Models\Goods;
use YiZan\Models\Seller;
use YiZan\Models\StaffServiceTime;
use YiZan\Models\SellerDeliveryTime;
use YiZan\Utils\Time;
use DB, Exception, Validator, Lang;

class CollectService extends \YiZan\Services\CollectService 
{
	/**
     * 收藏服务列表
     * @param  [type] $userId [description]
     * @param  [type] $type  1、商品2、服务
     * @param  [type] $page   [description]
     * @return [type]         [description]
     */
	public static function goodsList($userId, $type, $page) 
    {   
        if (!isset($type)) {
            $type = 1;
        }
        $list = UserCollect::where('type', $type)->where('user_id', $userId);

       // $totalCount = $list->count();
        $list = $list->orderBy('id', 'desc')
            ->skip(($page - 1) * 20)
            ->with('seller','goods','seller.extend','goods.extend')
            ->take(20)
            ->get()
            ->toArray();
           
        $dbPrefix = DB::getTablePrefix(); 
        $lists = [];
        foreach ($list as $key => $value) {
            $lists[$key]['isCollect'] = 1;
            if ($type == 2) {
                $sql = "SELECT * FROM {$dbPrefix}staff_service_time where seller_id = {$value['seller']['id']}";
                $stime =  DB::select($sql);
                $count = count($stime);
                $lists[$key]['businessHours'] = $count > 0 ? $stime[0]->begin_time . '-' . $stime[$count - 1]->end_time : '0:00-24:00';
                $lists[$key]['logo'] = $value['seller']['logo'];
                $lists[$key]['name'] = $value['seller']['name'];
                if ($value['seller']['serviceFee'] > 0) {
                    $serviceFee = '<font color="#979797">起送价</font><font color="#ff2d4b">￥'.$value['seller']['serviceFee'].'</font>';
                } else {
                    $serviceFee = '<font color="#979797">无起送价</font>';
                }
                $html = $serviceFee . '&nbsp;<font color="#979797">运费</font><font color="#ff2d4b">'.$value['seller']['deliveryFee'].'</font><font color="#979797">元起</font>&nbsp;<font color="#ff2d4b"></font>';
                $lists[$key]['freight'] = $html;
                $lists[$key]['price'] = 0;
                $lists[$key]['id'] = $value['seller']['id'];
                $lists[$key]['mapPoint'] = array(
                        'x' => $value['seller']['mapPoint']['x'],
                        'y' => $value['seller']['mapPoint']['y']
                    );
                $time =  SellerDeliveryTime::where('seller_id', $lists[$key]['seller']['id'])->get()->toArray();
                foreach ($time as $k => $v) {
                    $lists[$key]['stimes'][] = $v['stime'] . '-' . $v['etime'];
                }
                $lists[$key]['deliveryTime'] = $lists[$key]['stimes'] ? implode(',', $lists[$key]['stimes']) : '00:00-24:00';
     
                $isDelivery = SellerService::isCanBusiness($value['seller']['id']);
                $lists[$key]['isDelivery'] = $isDelivery;
                $lists[$key]['orderCount'] = $value['seller']['extend']['orderCount'];
                $lists[$key]['score'] = OrderRate::where('seller_id', $value['seller']['id'])->selectRaw('IFNULL(ROUND(SUM(star)/COUNT(id),1),0) as score')->pluck('score');
                $lists[$key]['countGoods'] = Goods::where('seller_id', $value['seller']['id'])->where('type', 1)->count();
                $lists[$key]['countService'] = Goods::where('seller_id', $value['seller']['id'])->where('type', 2)->count();

            } else {
                $lists[$key]['id'] = $value['goods']['id'];
                $lists[$key]['logo'] = $value['goods']['images'][0];
                $lists[$key]['name'] = $value['goods']['name'];
                $lists[$key]['price'] = $value['goods']['price'];
                $lists[$key]['salesCount'] = !empty($value['goods']['extend']) ? $value['goods']['extend']['salesVolume'] : 0;
                $lists[$key]['type'] = Goods::where('id', $value['goods']['id'])->pluck('type');
            }
            
        }
        //print_r($lists);
       // exit;
        return $lists;
	}

    public static function deleteGoods($userId, $id, $type) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.collect_delete')
        ];
        $res = UserCollect::where('user_id', $userId);
        if ($type == 2) {
            $res->where('seller_id', $id);
        } else {
            $res->where('goods_id', $id);
        }

        $res->where('type', $type)->delete();
        if (!$res) {
            $result['code'] = '10503';
            return $result;
        }
        return $result;
    }

    public static function collectGoods($userId, $id, $type) {
        $result = [
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api.success.collect_create')
        ];

        if ($type == 2) {
            $checkRes = Seller::where('id', $id)->first();
            $check = UserCollect::where('user_id', $userId)
                            ->where('type', $type)
                            ->where('seller_id', $id)
                            ->first();
        } else {
            $checkRes = Goods::where('id', $id)->first();
            $check = UserCollect::where('user_id', $userId)
                            ->where('type', $type)
                            ->where('goods_id', $id)
                            ->first();
        }
        if (!$checkRes) {
            $result['code'] = 10501;
            return $result;
        }

        if ($check) {
            $result['code'] = 10504;
            return $result;
        }
        $res = new UserCollect();
        $res->user_id = $userId;
        $res->create_time = UTC_TIME;
        $res->type = $type;
        if ($type == 2) {
            $res->seller_id = $id;
        } else {
            $res->goods_id = $id;
        }
        $res->save();
        
        return $result;
    }

}
