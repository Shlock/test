<?php

namespace YiZan\Services\System;

use YiZan\Models\GoodsCate;
use YiZan\Models\System\PromotionSn;
use YiZan\Models\System\Promotion;
use YiZan\Models\System\Seller;
use YiZan\Models\System\User;
use YiZan\Models\SellerCate;
use YiZan\Models\PromotionSellerCate;
use YiZan\Models\PromotionUnableDate;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use DB,
    Validator,
    Lang;

class PromotionService extends \YiZan\Services\PromotionService {

    /**
     * 获取优惠券列表
     * @param  integer $userId 会员编号
     * @param  integer $status 状态
     * @param  integer $page   页码
     * @return array           优惠券数组
     */
    public static function getLists($name, $sellerName, $beginTime, $endTime, $useType, $money, $page, $pageSize, $startTime, $endTime2) {
        DB::connection()->enableQueryLog();
        $list = Promotion::with('sellerCates.cates', 'seller', 'activityCount', 'promotionSnCount');
        if (!empty($name)) {
            $name = String::strToUnicode($name, '+');
            $list->whereRaw('MATCH(name_match) AGAINST(\'' . $name . '\' IN BOOLEAN MODE)');
            //$list->where('name', 'like', '%'.$name.'%');
        }

        if (!empty($sellerName)) {//搜索名称或手机号
            $sellerName = String::strToUnicode($sellerName, '+');
            $list->whereIn('seller_id', function($query) use ($sellerName) {
                $query->select('id')
                        ->from('seller')
                        ->whereRaw('MATCH(name_match) AGAINST(\'' . $sellerName . '\' IN BOOLEAN MODE)');
            });
        }

        if ($beginTime > 0) {//创建开始时间
            $list->where('create_time', '>', $beginTime);
        }

        if ($endTime > 0) {//创建开始时间
            $list->where('create_time', '<', $endTime);
        }

        if ($useType > 0) {
            $list->where('use_type', $useType);
        }

        if ($money > 0) {
            $list->where('money', $money);
        }

        if ($startTime > 0 && $endTime2 > 0) {
            $list->where(function($query) use ($startTime, $endTime2) {
                $query->where('type', 1)->where('end_time', '>=', $endTime2);
            })->orwhere('type', 2);
        }

        $total_count = $list->count();

        $list->orderBy('id', 'desc');

        $list = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
        foreach ($list as $key => $val) {
            if ($val['type'] == 1) {
                $beginTime = Time::toDate($val['beginTime'], 'Y/m/d H:i');
                $endTime = Time::toDate($val['endTime'], 'Y/m/d H:i');
                $list[$key]['ableDateTime'] = $beginTime . ' - ' . $endTime;
            } else {
                $list[$key]['ableDateTime'] = $val['expireDay'] . '天';
            }

            if ($val['useType'] == 2) {
                $cates = [];
                foreach ($val['sellerCates'] as $v) {
                    $cates[] = $v['cates']['name'];
                }
                $list[$key]['useTypeStr'] = implode('，', $cates);
            } elseif ($val['useType'] == 3) {
                $list[$key]['useTypeStr'] = $val['seller']['name'];
            } else {
                $list[$key]['useTypeStr'] = '不限制';
            }
            unset($list[$key]['sellerCates']);
        }
        return ["list" => $list, "totalCount" => $total_count];
    }

    /**
     * 获取优惠券
     * @param  integer $id 优惠券编号
     * @return Promotion|false 
     */
    public static function getPromotion($id) {
        return Promotion::with('sellerCates.cates', 'seller', 'unableDate')->find($id);
    }

    /**
     * 保存优惠券
     * @param int $id 优惠券编号
     * @param string $name 名称
     * @param double $money 面额
     * @param int $type 有效期类型 1:固定有效期 2:发放之日起算
     * @param int $beginTime 开始时间
     * @param int $endTime 结束时间
     * @param int $expireDay 过期天数
     * @param array $unableDate 不可用日期(可多个)
     * @param int $useType 使用条件类型 1:无限制 2:指定分类 3:指定商家
     * @param array $sellerCateIds 商家分类编号(可多个)
     * @param int $sellerId 商家编号
     * @param double $limitMoney 消费满多少使用
     * @param string $brief 描述
     * @param int $status 状态
     * @param int $sort 排序
     * @return array
     */
    public static function savePromotion($id, $name, $money, $type, $beginTime, $endTime, $expireDay, $unableDate, $useType, $sellerCateIds, $sellerId, $limitMoney, $brief, $status = 1, $sort = 100) {
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api_system.success.handle')
        );

        //名称最多长为20
        if ($name == '' || mb_strlen($name, 'UTF-8') > 20) {
            $result['code'] = 40423;
            return $result;
        }

        //面额必须大于0且最多一位小数
        if ($money < 0.1) {
            $result['code'] = 40424;
            return $result;
        }

        //固定有效期
        if ($type == 1) {
            if ($beginTime < 1 || $endTime < 1) {
                $result['code'] = 40431;
                return $result;
            }
            if ($beginTime > $endTime) {
                $result['code'] = 40305;
                return $result;
            }
        }

        //发放之日起算
        if ($type == 2 && $expireDay < 1) {
            $result['code'] = 40422;
            return $result;
        }

        //指定分类

        if ($useType == 2) {
            $sellerCateIds = array_unique(array_filter(explode(',', $sellerCateIds)));
            if (count($sellerCateIds) < 1) {
                $result['code'] = 40425;
                return $result;
            }
            $catesCount = SellerCate::whereIn('id', $sellerCateIds)->count();
            if ($catesCount != count($sellerCateIds)) {
                $result['code'] = 40428;
                return $result;
            }
        }

        //指定商家
        if ($useType == 3) {
            if ($sellerId < 1) {
                $result['code'] = 40426;
                return $result;
            }
            $seller = Seller::where('id', $sellerId)->first();
            if (!$seller) {
                $result['code'] = 40427;
                return $result;
            }
        }

        if ($id > 0) {//优惠券不存在
            $promotion = Promotion::find($id);
            if (!$promotion) {
                $result['code'] = 40409;
                return $result;
            }
        } else {
            $promotion = new Promotion;
        }
        DB::beginTransaction();
        try {
            if ($id > 0) {
                PromotionSellerCate::where('promotion_id', $id)->delete();
                PromotionUnableDate::where('promotion_id', $id)->delete();
            }
            $promotion->name = $name;
            $promotion->name_match = String::strToUnicode($name, '+');
            ;
            $promotion->money = $money;
            $promotion->type = $type;
            $promotion->begin_time = $beginTime;
            $promotion->end_time = $endTime;
            $promotion->expire_day = $expireDay;
            $promotion->use_type = $useType;
            $promotion->seller_id = $sellerId;
            $promotion->limit_money = $limitMoney;
            $promotion->brief = $brief;
            $promotion->create_time = UTC_TIME;
            $promotion->status = $status;
            $promotion->sort = $sort;
            $promotion->save();

            //添加不可用日期
            $unableDate = array_unique(array_filter($unableDate));
            if (count($unableDate) > 0) {
                $unableDateData = [];
                foreach ($unableDate as $val) {
                    $unableDateData[] = [
                        'promotion_id' => $promotion->id,
                        'date_time' => Time::toTime($val)
                    ];
                }
                PromotionUnableDate::insert($unableDateData);
            }

            //添加指定分类
            if ($useType == 2) {
                $sellerCateIdsData = [];
                foreach ($sellerCateIds as $val) {
                    $sellerCateIdsData[] = [
                        'promotion_id' => $promotion->id,
                        'seller_cate_id' => $val
                    ];
                }
                PromotionSellerCate::insert($sellerCateIdsData);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    }

    /**
     * 更新优惠券状态
     * @param  [type] $id     [description]
     * @param  [type] $status [description]
     * @return [type]         [description]
     */
    public static function updateStatus($id, $status) {
        Promotion::where('id', $id)->update(['status' => $status]);
    }

    /**
     * [generatePromotion description]
     * @param  integer $promotionId   优惠券编号
     * @param  string  $prefix        前缀
     * @param  integer $number 每人发放数量
     * @param  array   $userIds       要发放的会员
     * @return [type]                 
     */
    public static function send($promotionId, $prefix, $number, $userIds) {
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api_system.success.handle')
        );
        $promotion = Promotion::where('id', $promotionId)->first();
        if (!$promotion) {//优惠券不存在
            $result['code'] = 40409;
            return $result;
        }

        $prefix = strtoupper($prefix);
        if ($prefix != '' && !preg_match('/^[A-Z0-9]{1,3}$/', $prefix)) {
            $result['code'] = 40430;
            return $result;
        }

        if ($number < 1 || $number > 10) {//请设置生成数量
            $result['code'] = 40412;
            return $result;
        }

        $userIds = array_unique(array_filter(explode(',', $userIds)));
        if (count($userIds) < 1) {
            $result['code'] = 40429;
            return $result;
        }

        $user_count = User::whereIn('id', $userIds)->count();
        if ($user_count != count($userIds)) {
            //会员不存在
            $result['code'] = 40414;
            return $result;
        }
        DB::beginTransaction();
        try {
            $time = time()-8*3600;
            if ($promotion->type == 1) {
                $beginTime = $promotion->begin_time;
                $expireTime = $promotion->end_time;
            } elseif ($promotion->type == 2) {
                $beginTime = $time;
                $expireTime = $time + $promotion->expire_day * 24 * 3600;
            }
            $data = [];
            foreach ($userIds as $val) {
                for ($i = 0; $i < $number; $i++) {
                    $data[] = [
                        'sn' => $prefix . 'S' . String::randString(9, '1'),
                        'user_id' => $val,
                        'promotion_id' => $promotionId,
                        'create_time' => $time,
                        'send_time' => $time,
                        'begin_time' => $beginTime,
                        'expire_time' => $expireTime,
                        'money' => $promotion->money
                    ];
                }
            }
            PromotionSn::insert($data);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    }

    /**
     * 删除优惠券
     * @param  [type] $id     [description]
     * @return [type]         [description]
     */
    public static function deletePromotion($id) {
        $result = array(
            'code' => 0,
            'data' => null,
            'msg' => Lang::get('api_system.success.delete')
        );
        $check = Promotion::with('promotionSnCount', 'activityCount')->where('id', $id)->first();
        if (!$check) {
            $result['code'] = 40409;
            return $result;
        }
        $check = $check->toArray();
        if ($check['promotionSnCount'] > 0 || $check['activityCount'] > 0) {
            $result['code'] = 40310;
            $result['data'] = $check;
            return $result;
        }
        DB::beginTransaction();
        try {
            //删除优惠券
            Promotion::destroy($id);
            //删除优惠券不可用时间
            PromotionUnableDate::where('promotion_id', $id)->delete();
            //删除优惠券商家分类
            PromotionSellerCate::where('promotion_id', $id)->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 40311;
        }
        return true;
    }

}
