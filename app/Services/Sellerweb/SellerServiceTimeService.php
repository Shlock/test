<?php 
namespace YiZan\Services\Sellerweb;

use YiZan\Models\SellerServiceTimeSet;
use YiZan\Models\SellerServiceTime; 
use YiZan\Models\Goods;
use YiZan\Models\Seller;
use YiZan\Utils\Time;
use DB, Lang, Validator;

class SellerServiceTimeService extends \YiZan\Services\BaseService 
{
    /**
     * 服务时间添加
     * @param $sellerId 商家编号
     * @param $goodsId 服务编号
     * @param array $weeks 星期, 0:星期日 1:星期一 2:星期二 3:星期三 4:星期四 5:星期五 6:星期六
     * @param array $hours 时间
     */
   public static function insert($sellerId, $goodsId, $weeks, $hours) {

       $result = [
           'code'   => 0,
           'data'   => null,
           'msg'    => Lang::get('api_sellerweb.success.add')
       ];
        $check_goods = Goods::where('id', $goodsId)->first();

       if (!$check_goods) {
           $result['code'] = 50223; //商家不存在
           return $result;
       }

       if (!is_array($weeks) || count($weeks) < 1 || !is_array($hours) || count($hours) < 1 ) {
           $result['code'] = 50701; //选择的天和服务时间不能为空
           return $result;
       }
      
       //时间是否已经设置过
       $check = SellerServiceTime::whereIn("week", $weeks)->where('seller_id', $sellerId)->where('goods_id', $goodsId)->first();
       if ($check) {
           $result['code'] = 50702;
           return $result;
       }

       DB::beginTransaction();
       $sid = SellerServiceTimeSet::insertGetId([
                    'seller_id' => $sellerId,
                    'goods_id' => $goodsId,
                    'week' => json_encode($weeks),
                    'hours' => json_encode($hours)
               ]);
        if ($sid > 0) {
            try {

                //服务时间表数据插入
                asort($hours);
                $hours = array_unique(array_values($hours));
                $beginTime = null;
                $endTime = null;
                $nextHour = null;
                for($i = 0, $count = count($hours); $i < $count; $i++) {
                    if($beginTime == null) {
                        $beginTime = $hours[$i];
                        $endTime = Time::toTime($hours[$i]) + 30 * 60;
                    }
                    if (isset($hours[$i + 1])) {
                        $nextHour = Time::toTime($hours[$i + 1]);
                    }
                    if( $endTime != $nextHour) {
                        foreach ($weeks as $value) {
                            SellerServiceTime::insert([
                                'service_time_id'   => $sid,
                                'seller_id'         => $sellerId,
                                'goods_id'          => $goodsId,
                                'week'              => $value,
                                'begin_time'        => $beginTime,
                                'end_time'          =>Time::toDate($endTime,'H:i'),
                                'end_stime'         =>Time::toDate($endTime - 1,'H:i:s')
                            ]);
                        }
                        $beginTime = null;
                        $endTime = null;
                    }
                    else {
                        $endTime +=  30 * 60;
                    }
                }

                DB::commit();
                return  $result;
            } catch (Exception $e) {
                DB::rollback();
                $result['code'] = 50703;
                return $result;
            }
        } else {
            DB::rollback();
            $result['code'] = 50703;
            return $result;
        }

   }

    /**
     * 服务时间列表
     * @param $sellerId 服务机构编号
     * @param $goodsId 服务编号
     */
    public static function getList($sellerId, $goodsId) {
        $list = SellerServiceTimeSet::with('stime')->where('seller_id', $sellerId)->where('goods_id', $goodsId)->get()->toArray(); 
        foreach ($list as $key => $val) {
           $list[$key]['times'] = '';
           $hours = [];
           foreach ($val['stime'] as $v) {
              $hours[] = $v['beginTime'].'-'.$v['endTime'];
           }
           $list[$key]['times'] = implode(' ',array_unique($hours));
           unset($list[$key]['stime']);
        }
        return $list;
    }

    /**
     * 服务时间详情
     * @param $sellerId 服务机构编号
     * @param $goodsId 服务编号 
     */
    public static function detail($sellerId, $goodsId) {  
        $data = SellerServiceTimeSet::where('seller_id', $sellerId)->where('goods_id', $goodsId)->first();  
        return $data;
    }


    /**
     * 服务时间更新
     * @param $sellerId 服务机构编号
     * @param $goodsId 服务编号
     * @param $id 服务记录编号
     * @param array $weeks 星期, 0:星期日 1:星期一 2:星期二 3:星期三 4:星期四 5:星期五 6:星期六
     * @param array $hours 时间
     */
    public static function update($sellerId, $goodsId, $id, $weeks, $hours) {
        $result = [
            'code'   => 0,
            'data'   => null,
            'msg'    => Lang::get('api_sellerweb.success.update')
        ];
        $check_goods = Goods::where('id', $goodsId)->first();
        if (!$check_goods) {
            $result['code'] = 50233; //员工不存在
            return $result;
        } 
        if (!is_array($weeks) || count($weeks) < 1 || !is_array($hours) || count($hours) < 1 ) {
            $result['code'] = 50701; //选择的天和服务时间不能为空
            return $result;
        }
        //记录是否存在
        $checkhas = SellerServiceTimeSet::where('seller_id', $sellerId)->where('id',$id)->first();
        if (!$checkhas) {
            $result['code'] = 50704;
            return $result;
        }

        //时间是否已经设置过
        $check = SellerServiceTime::whereIn("week", $weeks)
                                ->where('seller_id', $sellerId)
                                ->where('goods_id', $goodsId)
                                ->where('service_time_id','!=',$id)->first();
        if ($check) {
            $result['code'] = 50702;
            return $result;
        }

        DB::beginTransaction();

        $res = SellerServiceTimeSet::where('id',$id)->update([
                    'week' => json_encode($weeks),
                    'hours' => json_encode($hours)
                ]);
        if ($res !== false) {
            try {
                SellerServiceTime::where('service_time_id', $id)->delete();
                //SellerServiceTimeNo::where('service_time_id', $id)->delete();
                asort($hours);
                $hours = array_values($hours);
                $beginTime = null;
                $endTime = null;
                $nextHour = null;
                for($i = 0, $count = count($hours); $i < $count; $i++) {
                    if($beginTime == null) {
                        $beginTime = $hours[$i];
                        $endTime = Time::toTime($hours[$i]) + 30 * 60;
                    }
                    if (isset($hours[$i + 1])) {
                        $nextHour = Time::toTime($hours[$i + 1]);
                    }
                    if( $endTime != $nextHour) {
                        foreach ($weeks as $value) {
                            SellerServiceTime::insert([
                                'service_time_id' => $id,
                                'seller_id' => $sellerId,
                                'week' => $value,
                                'begin_time' => $beginTime,
                                'end_time' =>Time::toDate($endTime,'H:i'),
                                'end_stime' =>Time::toDate($endTime - 1,'H:i:s')
                            ]);
                        }

                        $beginTime = null;
                        $endTime = null;
                    } else {
                        $endTime +=  30 * 60;
                    }
                }
                DB::commit();
                return  $result;
            } catch (Exception $e) {
                DB::rollback();
                $result['code'] = 50703;
                return $result;
            }
        } else {
            DB::rollback();
            $result['code'] = 50703;
            return $result;
        }

    }

    /**
     * 服务时间记录删除
     * @param $sellerId 服务机构编号
     * @param $goodsId 员工编号
     * @param $id 服务时间记录编号
     */
    public static function delete($sellerId, $goodsId, $id) {

        $result = [
            'code'   => 0,
            'data'   => null,
            'msg'    => Lang::get('api_sellerweb.success.delete')
        ];
        
        $check_staff = Goods::where('id', $goodsId)->first();
        
        if (!$check_staff) {
            $result['code'] = 50233; //员工不存在
            return $result;
        }
       
        //记录是否存在
        $check = SellerServiceTimeSet::where('seller_id', $sellerId)->where('id',$id)->first();
        if (!$check) {
            $result['code'] = 50704;
            return $result;
        }
       
        $res = SellerServiceTimeSet::where('id', $id)->delete();
        if ($res !== false) {
            SellerServiceTime::where('service_time_id', $id)->delete();
            //StaffServiceTimeNo::where('service_time_id', $id)->delete();
            return $result;
        } else {
            $result['code'] = 50705;
            return $result;
        }
    }
}
