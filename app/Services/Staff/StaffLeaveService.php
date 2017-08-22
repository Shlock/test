<?php 
namespace YiZan\Services\Staff;

use YiZan\Models\Staff\StaffLeave;
use YiZan\Models\StaffAppoint;
use YiZan\Models\SellerStaff;
use YiZan\Utils\Time;
use DB, Lang, Validator;

class StaffLeaveService extends \YiZan\Services\BaseService 
{
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
            'msg' => Lang::get('api_staff.success.staffleave_create')
        ];
        $rules = array(
            'begin_time' => ['required','gt:0'],
            'end_time' 	 => ['required','gt:0'],
            'remark' 	 => ['required']
        );

        $messages = array(
            'begin_time.required' => '50002',
            'begin_time.gt' => '50002',
            'end_time.required' => '50003',
            'end_time.gt' => '50003',
            'remark.required'	=> '50004'
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
            $result['code'] = '50005';
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
            $result['code'] = 50006;
            return $result;
        }

        //员工在此请假期间是否有订单
       /* $check_appoint = StaffAppoint::where('appoint_time', '>=', $beginTime)
                            ->where('appoint_time', '<', $endTime)
                            ->where('status','1')
                            ->where('staff_id',$staffId)
                            ->first();
        if ($check_appoint) {
            $result['code'] = 50001;
            return $result;
        }*/


        $staff = SellerStaff::where('id',$staffId)->first();

        DB::beginTransaction();
        try {
            //员工请假
/*            $staffleave = new StaffLeave();
            $staffleave->seller_id = $staff->seller_id;
            $staffleave->staff_id = $staff->id;
            $staffleave->begin_time = $beginTime;
            $staffleave->end_time = $endTime;
            $staffleave->remark = $remark;
            $staffleave->create_time = UTC_TIME;
            $staffleave->save();*/
            StaffLeave::insert([
                'seller_id' => $staff->seller_id,
                'staff_id' => $staff->id,
                'begin_time' => $beginTime,
                'end_time' => $endTime,
                'remark' => $remark,
                'create_time' => UTC_TIME,
                'status' => 0,
                'is_agree' => 0,
                'dispose_time' => 0,
                'dispose_result' => '',
            	'dispose_admin_id' => 0,
            ]);
            //员工预约时间
            //$staffappiont = new StaffAppoint();
            //格式化时间,半小时为间隔
            /*$btime = $beginTime % 1800 == 0 ? $beginTime : ceil($beginTime/1800) * 1800;
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
                        'is_leave' => '1'
                    ]);
                }

            }*/


            DB::commit();
            return $result;
        } catch (Exception $e) {

            DB::rollback();
            $result['code'] = 99999;
            return $result;
        }
    }

    /**
     * 员工请假列表
     * @param $staffId 员工编号
     * @param $page 页码
     * @param $pageSize 每页数
     */
    public static function getList($staffId, $page, $pageSize = 20) {
        $list = StaffLeave::where('staff_id', $staffId);
        $lists = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();
        foreach ($list as $k=>$v) {
            $list[$k]['beginTime'] = Time::toDate($v['beginTime'], 'Y-m-d H:i:s');
            $list[$k]['endTime'] = Time::toDate($v['endTime'], 'Y-m-d H:i:s');
        }
        return $lists;
    }

    /**
     * 删除员工请假记录
     * @param $ids 请假记录编号数组
     * @param $staffId 员工编号
     */
    public static function delete($ids, $staffId) {
        $result = [
            'code'	=> 0,
            'data'	=> null,
            'msg' => Lang::get('api_staff.success.staffleave_delete')
        ];
        $check = StaffLeave::whereIn('id',$ids)
                            ->where('staff_id', $staffId)
                            ->get()->toArray();
        if ((!is_array($ids) && count($ids) < 1) || count($check) < 1) {
            $result['code'] = 50007;
            return $result;
        }
        DB::beginTransaction();
        try {
            StaffLeave::whereIn('id',$ids)->where('staff_id', $staffId)->delete();
            /*foreach ($check as $val) {
                StaffAppoint::where('appoint_time','>=',Time::toTime($val['beginTime']))
                    ->where('appoint_time', '<', Time::toTime($val['endTime']))
                    ->where('staff_id', $staffId)
                    ->update(['is_leave' => 0]);
            }*/
            DB::commit();
            return $result;
        } catch(Exception $e) {
            $result['code'] = 50008;
            DB::rollback();
            return $result;
        }
    }

    /**
     * 请假详情
     * @param int $staffId 员工编号
     * @param int $id 请假编号
     */
    public static function detail($staffId, $id) {
        $data = StaffLeave::where('id', $id)
                            ->where('staff_id', $staffId)
                            ->first()
                            ->toArray();
        return $data;
    }
}
