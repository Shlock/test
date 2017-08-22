<?php 
namespace YiZan\Services\System;

use YiZan\Models\StaffLeave;
use YiZan\Models\StaffAppoint;
use YiZan\Models\SellerStaff;
use YiZan\Models\Order;
use YiZan\Utils\Time;
use DB, Lang, Validator;

class StaffLeaveService extends \YiZan\Services\BaseService 
{

    /**
     * 员工请假列表
     * @param $page 页码
     * @param $pageSize 每页数
     */
    public static function getList($page, $pageSize = 20) {
        $list = StaffLeave::with('staff','seller');

        $totalCount = $list->count();
        $lists = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();

        return ["list"=>$lists, "totalCount"=>$totalCount];
    }

    /**
     * 删除员工请假记录
     * @param $id 请假记录编号
     * @param $sellerId 服务机构编号
     */
    public static function delete($id) {
        $result = [
            'code'	=> 0,
            'data'	=> null,
            'msg' => Lang::get('api_system.success.delete')
        ];
        $check = StaffLeave::where('id',$id)->first()->toArray();
        if (!$check) {
            $result['code'] = 50601;
            return $result;
        }
        DB::beginTransaction();
        try {
            StaffLeave::where('id',$id)->delete();
            StaffAppoint::where('appoint_time','>=',Time::toTime($check['beginTime']))
                ->where('appoint_time', '<', Time::toTime($check['endTime']))
                ->where('seller_id', $check['sellerId'])
                ->where('staff_id', $check['staffId'])
                ->update(['is_leave' => 0]);
            DB::commit();
            return $result;
        } catch(Exception $e) {
            $result['code'] = 50602;
            DB::rollback();
            return $result;
        }
    }


    /**
     * 处理员工请假
     * @param $id 请假记录编号
     * @param $status 处理结果
     * @param $remark 处理备注
     * @param $adminId 处理管理员编号
     */
    public static function dispose($id, $status, $remark, $adminId) {

        $result = [
            'code'	=> 0,
            'data'	=> null,
            'msg' => ''
        ];

        $check = StaffLeave::where('id',$id)
            ->where('dispose_time',0)
            ->first();
        if (!$check) {
            $result['code'] = 50601;
            return $result;
        }
        if (!in_array($status, ['1','-1'])) {
            $result['code'] = 50603;
            return $result;
        }
        if ($remark == '') {
            $result['code'] = 50605;
            return $result;
        }
        DB::beginTransaction();
        try {
            StaffLeave::where('id',$id)->update([
                'status' => $status,
                'dispose_time' => UTC_TIME,
                'dispose_result'=>$remark,
                'dispose_admin_id'=>$adminId
            ]);
            $check = $check->toArray();
            if ($status == 1) {
                $btime = Time::toTime($check['beginTime']) % 1800 == 0 ? Time::toTime($check['beginTime']) : ceil(Time::toTime($check['beginTime'])/1800) * 1800;
                $etime = Time::toTime($check['endTime']) % 1800 == 0 ? Time::toTime($check['endTime']) : ceil(Time::toTime($check['endTime'])/1800) * 1800;
                $appoint_data = [];
                for ($i = $btime; $i < $etime; $i += 1800) {
                    $appoint = StaffAppoint::where('appoint_time',$i)
                        ->where('staff_id', $check['staffId'])
                        ->first();
                    if ($appoint) {
                        StaffAppoint::where('staff_id',$check['staffId'])
                            ->where('appoint_time', $i)
                            ->update(['is_leave' => '1','appoint_week' => Time::toDate($i,'w')]);
                    } else {
                        $appoint_data[] = [
                            'staff_id' => $check['staffId'],
                            'seller_id' => $check['sellerId'],
                            'appoint_time' => $i,
                            'appoint_day' => Time::toDayTime($i),
                            'appoint_week' => Time::toDate($i,'w'),
                            'is_leave' => '1'
                        ];

                    }
                }
            }
            if (count($appoint_data) > 0) {
                StaffAppoint::insert($appoint_data);
            }
            DB::commit();
            return $result;
        } catch(Exception $e) {
            $result['code'] = 50604;
            DB::rollback();
            return $result;
        }
    }


    /**
     * 请假详情
     * @param $id  请假记录编号
     * @param $type  指派类型 1:指定指派 2:随机指派
     * @param $page 请假期间所有满足条件的指派的页码
     * @param int $pageSize 请假期间所有满足条件的指派的每页数量
     */
    public static function detail($id, $type, $isOrder, $page, $pageSize = 20) {
        $data = StaffLeave::where('id', $id)->with('staff')->first()->toArray();
        $list = [];
        $totalCount =  0;
        if ($data && (int)$isOrder == 1) {
            $beginTime = Time::toTime($data['beginTime']);
            $endTime = Time::toTime($data['endTime']);
            //允许更改的订单状态
            $allow_status = [ORDER_STATUS_WAIT_PAY,ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_SELLER_ACCEPT,ORDER_STATUS_STAFF_ACCEPT];
            $list = Order::where('seller_staff_id', $data['staffId'])
                            ->whereIn('status', $allow_status)
                            ->where(function($query) use ($beginTime, $endTime){
                                $query->where(function($query_one) use ($beginTime){
                                    $query_one->where('appoint_time', '<=', $beginTime)
                                        ->where('service_end_time', '>', $beginTime);
                                })->orWhere(function($query_two) use ($beginTime,$endTime){
                                    $query_two->where('appoint_time', '>=', $beginTime)
                                        ->where('appoint_time', '<', $endTime);
                                });
                            });
            if ($type > 0) {
                $list->where('designate_type', $type);
            }
            $totalCount = $list->count();
            $list = $list->skip(($page - 1) * $pageSize)
                    ->take($pageSize)
                    ->get()->toArray();
        }

        return ['data' => $data, 'list' => $list, 'totalCount' => $totalCount];
    }

    /**
     * 请假期间影响订单所有空闲员工
     * @param int $id 请假记录编号
     * @param array $orderIds 订单编号数组
     * @param int $page 页码
     * @param int $pageSize 每页数
     */
    public static function getStaffList($id, $orderIds, $page, $pageSize = 20){
        $staff_id = StaffLeave::where('id', $id)->pluck('staff_id');
        //允许更改的订单状态
        $allow_status = [ORDER_STATUS_WAIT_PAY,ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_SELLER_ACCEPT,ORDER_STATUS_STAFF_ACCEPT];
        $order_count = Order::whereIn('id', $orderIds)->whereIn('status', $allow_status)->count();
        $list = [];
        $total = 0;
        if ($order_count > 0) {

            $order_list = Order::whereIn('id', $orderIds)->whereIn('status', $allow_status)->select('id','appoint_time','service_end_time')->get()->toArray();
            $count_sql = 'select count(id) as total from '.env('DB_PREFIX').'seller_staff where id in (';

            $sql = 'select * from '.env('DB_PREFIX').'seller_staff where id in (';

            $com_sql = 'select staff_id from (';

            foreach ($order_list as $key => $val) {

                $query = 'select staff_id from '.env('DB_PREFIX').'staff_service_time where staff_id not in(';

                $query .= 'select staff_id from '.env('DB_PREFIX').'staff_appoint where appoint_time >= ' . $val['appointTime'] . ' and appoint_time <= '. $val['serviceEndTime'] . ' and order_id = 0 and is_leave = 0 and appoint_week = '.Time::toDate($val['appointTime'], 'w');

                $query .= ') and week = ' . Time::toDate($val['appointTime'], 'w') . ' and begin_time <= "' . Time::toDate($val['appointTime'], 'H:i') . '" and end_time >= "' . Time::toDate($val['serviceEndTime'], 'H:i') . '"';

                if ($key == 0) {
                    $com_sql .= $query;
                } else {
                    $com_sql .= ' UNION ALL '.$query;
                }
            }

            $com_sql .= ') as stids group by staff_id HAVING COUNT(staff_id) = '.$order_count;

            $sql .= $com_sql .  ') and id != ' . $staff_id . ' order by id desc limit '. ($page - 1) * $pageSize .','.$pageSize;
            $count_sql .= $com_sql . ') and id != ' . $staff_id;
            $totalCount = DB::select($count_sql);
            $total = $totalCount[0]->total;
            $list = DB::select($sql);
            if ($total > 0) {
                foreach ($list as $key=>$val) {
                    $list[$key] = (array)$val;
                }
            }

        }

        return ['list' => $list, 'totalCount' => $total];
    }

    /**
     * 创建员工请假
     * @param $staffId 员工编号
     * @param $beginTime 请假开始时间
     * @param $endTime 请假结束时间
     * @param $remark 请假理由
     */
    public static function create($staffId, $beginTime, $endTime, $remark) {
        $result = [
            'code'	=> 0,
            'data'	=> null,
            'msg' => Lang::get('api_system.success.create')
        ];
        $staff = SellerStaff::where('id',$staffId)->first();
        if (!$staff) {
            $result['code'] = 80102;
            return $result;
        }
        $rules = array(
            'begin_time' => ['required','gt:0'],
            'end_time' 	 => ['required','gt:0'],
            'remark' 	 => ['required']
        );

        $messages = array(
            'begin_time.required' => '50701',
            'begin_time.gt' => '50701',
            'end_time.required' => '50702',
            'end_time.gt' => '50702',
            'remark.required'	=> '50703'
        );
        $validator = Validator::make([
            'begin_time' => $beginTime,
            'end_time' 	 => $endTime,
            'remark' 	 => $remark
        ], $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        if ($endTime <= $beginTime) {
            $result['code'] = '50704';
            return $result;
        }
        //员工在此请假期间是否有请假记录
        $check_leave = StaffLeave::where('staff_id',$staffId)
            ->where(function($query) use ($beginTime, $endTime){
                $query->where(function($query_one) use ($beginTime){
                    $query_one->where('begin_time', '<=', $beginTime)
                        ->where('end_time', '>', $beginTime);
                })->orWhere(function($query_two) use ($beginTime,$endTime){
                    $query_two->where('begin_time', '>=', $beginTime)
                        ->where('begin_time', '<', $endTime);
                });
            })->first();
        if ($check_leave) {
            $result['code'] = 50705;
            return $result;
        }


        DB::beginTransaction();
        try {
            //员工请假
            $staffleave = new StaffLeave();
            $staffleave->seller_id = $staff->seller_id;
            $staffleave->staff_id = $staff->id;
            $staffleave->begin_time = $beginTime;
            $staffleave->end_time = $endTime;
            $staffleave->remark = $remark;
            $staffleave->create_time = UTC_TIME;
            $staffleave->status = 1;
            $staffleave->save();

            //员工预约时间
            $staffappiont = new StaffAppoint();
            //格式化时间,半小时为间隔
            $btime = $beginTime % 1800 == 0 ? $beginTime : ceil($beginTime/1800) * 1800;
            $etime = $endTime % 1800 == 0 ? $endTime : ceil($endTime/1800) * 1800;
            for ($i = $btime; $i < $etime; $i += 1800) {
                $appoint = StaffAppoint::where('appoint_time',$i)
                                    ->where('staff_id', $staffId)
                                    ->first();
                if ($appoint) {
                    StaffAppoint::where('staff_id',$staffId)
                                ->where('appoint_time', $i)
                                ->update(['is_leave' => '1']);
                } else {
                    StaffAppoint::insert([
                        'staff_id' => $staffId,
                        'seller_id' => $staff->seller_id,
                        'appoint_time' => $i,
                        'appoint_day' => Time::toDayTime($i),
                        'appoint_week' => Time::toDate($i,'w'),
                        'is_leave' => '1'
                    ]);
                }

            }
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
            return $result;
        }
    }

}
