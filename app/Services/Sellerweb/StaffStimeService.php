<?php 
namespace YiZan\Services\Sellerweb;

use YiZan\Models\StaffServiceTimeSet;
use YiZan\Models\StaffServiceTime;
use YiZan\Models\StaffServiceTimeNo;
use YiZan\Models\SellerStaff;
use YiZan\Models\Seller;
use YiZan\Utils\Time;
use DB, Lang, Validator;

class StaffStimeService extends \YiZan\Services\BaseService 
{
    /**
     * 服务时间添加
     * @param $sellerId 商家编号
     * @param $staffId 员工编号
     * @param array $weeks 星期, 0:星期日 1:星期一 2:星期二 3:星期三 4:星期四 5:星期五 6:星期六
     * @param array $hours 时间
     */
   public static function insert($sellerId, $weeks, $hours) {

       $result = [
           'code'   => 0,
           'data'   => null,
           'msg'    => Lang::get('api_sellerweb.success.add')
       ];
        $check_staff = Seller::where('id', $sellerId)->first();

       if (!$check_staff) {
           $result['code'] = 50201; //员工不存在
           return $result;
       }
       if (!is_array($weeks) || count($weeks) < 1 || !is_array($hours) || count($hours) < 1 ) {
           $result['code'] = 50701; //选择的天和服务时间不能为空
           return $result;
       }
      
       //时间是否已经设置过
       $check = StaffServiceTime::whereIn("week", $weeks)->where('seller_id', $sellerId)->where('staff_id', 0)->first();
       if ($check) {
           $result['code'] = 50702;
           return $result;
       }

       DB::beginTransaction();
       $sid = StaffServiceTimeSet::insertGetId([
                    'seller_id' => $sellerId,
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
                        $endTime = Time::toTime($hours[$i]) + 60 * 60;
                    }
                    if (isset($hours[$i + 1])) {
                        $nextHour = Time::toTime($hours[$i + 1]);
                    }
                    if( $endTime != $nextHour) {
                        foreach ($weeks as $value) {
                            StaffServiceTime::insert([
                                'service_time_id' => $sid,
                                'seller_id' => $sellerId,
                                'week' => $value,
                                'begin_time' => $beginTime,
                                'end_time' =>Time::toDate($endTime,'H:i'),
                                'end_stime' =>Time::toDate($endTime - 1,'H:i:s')
                            ]);
                        }
                        $beginTime = null;
                        $endTime = null;
                    }
                    else {
                        $endTime +=  60 * 60;
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
     * @param $staffId 员工编号
     */
    public static function getList($sellerId) {
        $list = StaffServiceTimeSet::with('stime')->where('seller_id', $sellerId)->where('staff_id', 0)->get()->toArray();
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
     * @param $staffId 员工编号
     * @param $id 服务时间编号
     */
    public static function detail($sellerId, $id) {
        if(OPERATION_VERSION == 'oneself') {
            $data = StaffServiceTimeSet::where('id', $id)->first()->toArray();
        }else{
            $data = StaffServiceTimeSet::where('seller_id', $sellerId)->where('id', $id)->first()->toArray();
        }
        return $data;
    }



    /**
     * 服务时间更新
     * @param $sellerId 服务机构编号
     * @param $staffId 员工编号
     * @param $id 服务记录编号
     * @param array $weeks 星期, 0:星期日 1:星期一 2:星期二 3:星期三 4:星期四 5:星期五 6:星期六
     * @param array $hours 时间
     */
    public static function update($sellerId, $id, $weeks, $hours) {
        $result = [
            'code'   => 0,
            'data'   => null,
            'msg'    => Lang::get('api_sellerweb.success.update')
        ];
        $check_staff = Seller::where('id', $sellerId)->first();
        if (!$check_staff) {
            $result['code'] = 50201; //员工不存在
            return $result;
        }
        if (!is_array($weeks) || count($weeks) < 1 || !is_array($hours) || count($hours) < 1 ) {
            $result['code'] = 50701; //选择的天和服务时间不能为空
            return $result;
        }
        //记录是否存在
        $checkhas = StaffServiceTimeSet::where('seller_id', $sellerId)->where('staff_id', 0)->where('id',$id)->first();
        if (!$checkhas) {
            $result['code'] = 50704;
            return $result;
        }

        //时间是否已经设置过
        $check = StaffServiceTime::whereIn("week", $weeks)
                                ->where('seller_id', $sellerId)
                                ->where('staff_id', 0)
                                ->where('service_time_id','!=',$id)->first();
        if ($check) {
            $result['code'] = 50702;
            return $result;
        }

        DB::beginTransaction();

        $res = StaffServiceTimeSet::where('id',$id)->update([
                    'week' => json_encode($weeks),
                    'hours' => json_encode($hours)
                ]);
        if ($res !== false) {
            try {
                StaffServiceTime::where('service_time_id', $id)->delete();
                //StaffServiceTimeNo::where('service_time_id', $id)->delete();
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
                            StaffServiceTime::insert([
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
     * @param $staffId 员工编号
     * @param $id 服务时间记录编号
     */
    public static function delete($sellerId, $id) {

        $result = [
            'code'   => 0,
            'data'   => null,
            'msg'    => Lang::get('api_sellerweb.success.delete')
        ];
        
        $check_staff = Seller::where('id', $sellerId)->first();
        
        if (!$check_staff) {
            $result['code'] = 50201; //员工不存在
            return $result;
        }
       
        //记录是否存在
        $check = StaffServiceTimeSet::where('seller_id', $sellerId)->where('staff_id', 0)->where('id',$id)->first();
        if (!$check) {
            $result['code'] = 50704;
            return $result;
        }
       
        $res = StaffServiceTimeSet::where('id', $id)->delete();
        if ($res !== false) {
            StaffServiceTime::where('service_time_id', $id)->delete();
            //StaffServiceTimeNo::where('service_time_id', $id)->delete();
            return $result;
        } else {
            $result['code'] = 50705;
            return $result;
        }
    }
}
