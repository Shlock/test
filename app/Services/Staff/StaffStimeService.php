<?php 
namespace YiZan\Services\Staff;

use YiZan\Models\Staff\StaffServiceTimeSet;
use YiZan\Models\Staff\StaffServiceTime;
use YiZan\Models\Staff\StaffServiceTimeNo;
use YiZan\Models\SellerStaff;
use YiZan\Utils\Time;
use DB, Lang, Validator;

class StaffStimeService extends \YiZan\Services\BaseService 
{
    /**
     * 服务时间添加
     * @param $staffId 员工编号
     * @param array $weeks 星期, 0:星期日 1:星期一 2:星期二 3:星期三 4:星期四 5:星期五 6:星期六
     * @param array $hours 时间
     */
   public static function insert($staffId, $weeks, $hours) {
       $result = [
           'code'   => 0,
           'data'   => null,
           'msg'    => Lang::get('api_staff.success.add')
       ];
       if (!is_array($weeks) || count($weeks) < 1 || !is_array($hours) || count($hours) < 1 ) {
           $result['code'] = 50401; //选择的天和服务时间不能为空
           return $result;
       }
       //时间是否已经设置过
       $check = StaffServiceTime::whereIn("week", $weeks)->where('staff_id', $staffId)->first();
       if ($check) {
           $result['code'] = 50402;
           return $result;
       }

       DB::beginTransaction();
       $seller_id = SellerStaff::where('id',$staffId)->pluck('seller_id');
       $sid = StaffServiceTimeSet::insertGetId([
                    'seller_id' => $seller_id,
                    'staff_id' => $staffId,
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
                            StaffServiceTime::insert([
                                'service_time_id' => $sid,
                                'seller_id' => $seller_id,
                                'staff_id' => $staffId,
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
                        $endTime +=  30 * 60;
                    }
                }

                //非工作时间表插入
                /*$no_hours = [];
                $startTime =  Time::toTime('00:00');
                for($i = 0;$i < 48; $i++) {
                    $hour =  Time::toDate($startTime + $i * 1800, 'H:i');
                    if (!in_array($hour, $hours)) {
                        $no_hours[] =  $hour;
                    }
                }

                $beginTime = null;
                $endTime = null;
                $nextHour = null;
                for($i = 0, $count = count($no_hours); $i < $count; $i++) {
                    if($beginTime == null) {
                        $beginTime = $no_hours[$i];
                        $endTime = Time::toTime($no_hours[$i]) + 30 * 60;
                    }
                    if (isset($no_hours[$i + 1])) {
                        $nextHour = Time::toTime($no_hours[$i + 1]);
                    }
                    if( $endTime != $nextHour) {
                        if (Time::toDate($endTime,'H:i') == '00:00') {
                            $endTime = $endTime - 1;
                        }
                        foreach ($weeks as $value) {
                            StaffServiceTimeNo::insert([
                                'service_time_id' => $sid,
                                'seller_id' => $seller_id,
                                'staff_id' => $staffId,
                                'week' => $value,
                                'begin_time' => $beginTime,
                                'end_time' =>Time::toDate($endTime,'H:i')
                            ]);
                        }
                        $beginTime = null;
                        $endTime = null;
                    }
                    else {
                        $endTime +=  30 * 60;
                    }
                }*/

                DB::commit();
                return  $result;
            } catch (Exception $e) {
                DB::rollback();
                $result['code'] = 50403;
                return $result;
            }
        } else {
            DB::rollback();
            $result['code'] = 50403;
            return $result;
        }

   }

    /**
     * 服务时间列表
     * @param $staffId 员工编号
     */
    public static function getList($staffId) {
        $list = StaffServiceTimeSet::with('stime')->where('staff_id', $staffId)->get()->toArray();
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
     * @param $staffId 员工编号
     * @param $id 服务时间编号
     */
    public static function detail($staffId, $id) {
        $data = StaffServiceTimeSet::where('staff_id',$staffId)->where('id', $id)->first()->toArray();
        return $data;
    }


    /**
     * 服务时间更新
     * @param $staffId 员工编号
     * @param $id 服务记录编号
     * @param array $weeks 星期, 0:星期日 1:星期一 2:星期二 3:星期三 4:星期四 5:星期五 6:星期六
     * @param array $hours 时间
     */
    public static function update($staffId, $id, $weeks, $hours) {
        $result = [
            'code'   => 0,
            'data'   => null,
            'msg'    => Lang::get('api_staff.success.update')
        ];
        if (!is_array($weeks) || count($weeks) < 1 || !is_array($hours) || count($hours) < 1 ) {
            $result['code'] = 50401; //选择的天和服务时间不能为空
            return $result;
        }
        //记录是否存在
        $checkhas = StaffServiceTimeSet::where('staff_id', $staffId)->where('id',$id)->first();
        if (!$checkhas) {
            $result['code'] = 50404;
            return $result;
        }

        //时间是否已经设置过
        $check = StaffServiceTime::whereIn("week", $weeks)
                                ->where('staff_id', $staffId)
                                ->where('service_time_id','!=',$id)->first();
        if ($check) {
            $result['code'] = 50402;
            return $result;
        }

        DB::beginTransaction();
        $seller_id = SellerStaff::where('id',$staffId)->pluck('seller_id');
        $res = StaffServiceTimeSet::where('id',$id)->update([
                    'week' => json_encode($weeks),
                    'hours' => json_encode($hours)
                ]);
        if ($res) {
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
                    if (isset($no_hours[$i + 1])) {
                        $nextHour = Time::toTime($hours[$i + 1]);
                    }
                    if( $endTime != $nextHour) {
                        foreach ($weeks as $value) {
                            StaffServiceTime::insert([
                                'service_time_id' => $id,
                                'seller_id' => $seller_id,
                                'staff_id' => $staffId,
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

                //非工作时间表插入
                /*$no_hours = [];
                $startTime =  Time::toTime('00:00');
                for($i = 0;$i < 48; $i++) {
                    $hour =  Time::toDate($startTime + $i * 1800, 'H:i');
                    if (!in_array($hour, $hours)) {
                        $no_hours[] =  $hour;
                    }
                }

                $beginTime = null;
                $endTime = null;
                $nextHour = null;
                for($i = 0, $count = count($no_hours); $i < $count; $i++) {
                    if($beginTime == null) {
                        $beginTime = $no_hours[$i];
                        $endTime = Time::toTime($no_hours[$i]) + 30 * 60;
                    }
                    if (isset($no_hours[$i + 1])) {
                        $nextHour = Time::toTime($no_hours[$i + 1]);
                    }
                    if( $endTime != $nextHour) {
                        if (Time::toDate($endTime,'H:i') == '00:00') {
                            $endTime = $endTime - 1;
                        }
                        foreach ($weeks as $value) {
                            StaffServiceTimeNo::insert([
                                'service_time_id' => $id,
                                'seller_id' => $seller_id,
                                'staff_id' => $staffId,
                                'week' => $value,
                                'begin_time' => $beginTime,
                                'end_time' =>Time::toDate($endTime,'H:i')
                            ]);
                        }
                        $beginTime = null;
                        $endTime = null;
                    }
                    else {
                        $endTime +=  30 * 60;
                    }
                }*/

                DB::commit();
                return  $result;
            } catch (Exception $e) {
                DB::rollback();
                $result['code'] = 50403;
                return $result;
            }
        } else {
            DB::rollback();
            $result['code'] = 50403;
            return $result;
        }

    }

    /**
     * 服务时间记录删除
     * @param $staffId 员工编号
     * @param $id 服务时间记录编号
     */
    public static function delete($staffId, $id) {
        $result = [
            'code'   => 0,
            'data'   => null,
            'msg'    => Lang::get('api_staff.success.delete')
        ];
        //记录是否存在
        $check = StaffServiceTimeSet::where('staff_id', $staffId)->where('id',$id)->first();
        if (!$check) {
            $result['code'] = 50404;
            return $result;
        }
        $res = StaffServiceTimeSet::where('id',$id)->delete();
        if ($res !== false) {

            StaffServiceTime::where('service_time_id', $id)->delete();
            StaffServiceTimeNo::where('service_time_id', $id)->delete();
            return $result;
        } else {
            $result['code'] = 50405;
            return $result;
        }
    }
}
