<?php
namespace YiZan\Services\System;

use YiZan\Models\System\Goods;
use YiZan\Services\SystemGoodsService;
use YiZan\Models\System\Activity;
use YiZan\Utils\String;
use YiZan\Models\SystemGoods;
use DB, Validator;
use YiZan\Models\System\ActivityGoods;


class ShoppingSpreeService extends \YiZan\Services\ShoppingSpreeService{
    public function shoppingSave($id, $name, $starttime, $endtime, $image, $type, $sort, $status){
        DB::connection()->enableQueryLog();
        $result = array(
            'code' => self::SUCCESS,
            'data' => null,
            'msg' => ''
        );
        $rules = array(
            'name' => ['required']
        );
        
        $messages = array(
            'name.required' => 30604// 请输入活动名称
        );
        
        $validator = Validator::make(['name' => $name], $rules, $messages);
        
        //验证信息
        if ($validator->fails()) {
            $messages       = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        $activity = new Activity;
        
        DB::beginTransaction();
        try {
            if($id > 0){
                $data = [
                    'name'                  => $name,
                    'start_time'            => $starttime,
                    'end_time'              => $endtime,
                    'image'                 => $image,
                    'type'                  => $type,
                    'sort'                  => $sort,
                    'status'                => $status
                ];
                Activity::where('id',$id)->update($data);
                $lists = $activity->where('id',$id)->first()->toArray();
            }else{
                $activity->name                     = $name;
                $activity->start_time               = $starttime;
                $activity->end_time                 = $endtime;
                $activity->image                    = $image;
                $activity->create_time              = UTC_TIME;
                $activity->type                     = $type;
                $activity->sort                     = $sort;
                $activity->status                   = $status;
                $activity->save();
                $lists = $activity->where('id',$activity->id)->first()->toArray();
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        //print_r(DB::getQueryLog());exit;
        return $lists;    
    }
    
    /**
     * 获取抢购活动
     * @param $id
     */
    public function getShoppingConfig($id,$type){
        return Activity::where('id',$id)->where('type',$type)->first();
    }
    
    /**
     * 设置抢购活动的状态
     */
    public function setStatus($id, $status){
        $result = array(
            'code' => self::SUCCESS,
            'data' => null,
            'msg' => ''
        );
        DB::beginTransaction();
        try {
            $data = Activity::where('id',$id)->update(['status'=>$status]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
       return $result;
    }
    /**
     * 设置抢购活动的价格
     */
    public function setPrice($id, $activityIid, $price){
        $result = array(
            'code' => self::SUCCESS,
            'data' => null,
            'msg' => ''
        );
        
        if($id < 1){
            return false;
        }
        
        $data = SystemGoodsService::getById($id);
        if(!$data){
            $result['code'] = 50027;
            return $result;
        }
        DB::beginTransaction();
        try {
            $data = ActivityGoods::where('goods_id',$id)->where('activity_id',$activityIid)->update(['shopping_spree_price'=>$price]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    }
    /**
     * 选择服务
     * @param $goodsId
     * @param $activityId
     * @param $type
     */
    public function setService($goodsId, $activityId, $type){
        $result = array(
            'code' => self::SUCCESS,
            'data' => null,
            'msg' => ''
        );
        if($activityId < 1){
            $result['code'] = 30401;
            return $result;
        }
        if($goodsId < 1){
            $result['code'] = 30402;
            return $result;
        }
        
        $activityGoods = new ActivityGoods;
        if($type == 'add'){
            $activityGoods->goods_id            = $goodsId;
            $activityGoods->activity_id         = $activityId;
            $activityGoods->save();
        }else{
            $rs = ActivityGoods::where('goods_id',$goodsId)->where('activity_id',$activityId)->delete();
        }
        return $result;
    }
}