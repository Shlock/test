<?php 
namespace YiZan\Services\Sellerweb;

use YiZan\Models\Staff\StaffLeave;
use YiZan\Models\StaffAppoint;
use YiZan\Models\SellerStaff;
use YiZan\Utils\Time;
use DB, Lang, Validator,Exception;

class StaffLeaveService extends \YiZan\Services\BaseService 
{

    /**
     * 员工请假列表
     * @param $sellerId 服务机构编号
     * @param $page 页码
     * @param $pageSize 每页数
     */
    public static function getList($sellerId, $page, $pageSize = 20) {
        $list = StaffLeave::where('seller_id', $sellerId)->with('staff');
        $lists = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();
        return $lists;
    }

    /**
     * 删除员工请假记录
     * @param $id 请假记录编号
     * @param $sellerId 服务机构编号
     */
    public static function delete($id, $sellerId) {
        $result = [
            'code'	=> 0,
            'data'	=> null,
            'msg' => Lang::get('api_sellerweb.success.delete')
        ];
        $check = StaffLeave::where('id',$id)
                            ->where('seller_id', $sellerId)
                            ->first()->toArray();
        if (!$check) {
            $result['code'] = 50601;
            return $result;
        }
        DB::beginTransaction();
        try {
            StaffLeave::where('id',$id)->where('seller_id', $sellerId)->delete();
            StaffAppoint::where('appoint_time','>=',Time::toTime($check['beginTime']))
                ->where('appoint_time', '<', Time::toTime($check['endTime']))
                ->where('seller_id', $sellerId)
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
     * @param $agree 处理结果
     * @param $sellerId 服务机构编号
     */
    public static function dispose($id, $agree, $sellerId) {
        $result = [
            'code'	=> 0,
            'data'	=> null,
            'msg' => ''
        ];
        $check = DB::table("staff_leave")
            ->where('id',$id)
            ->where('seller_id', $sellerId)
            ->where('dispose_time','=',0)
            ->first();
        
        if (!$check) {
            $result['code'] = 50601;
            return $result;
        }
        if (!in_array($agree, ['1','-1'])) {
            $result['code'] = 50603;
            return $result;
        }
        DB::beginTransaction();
        try {
            //DB::connection()->enableQueryLog();
            StaffLeave::where('id',$id)->where('seller_id', $sellerId)->update(['is_agree' => $agree, 'dispose_time' => UTC_TIME]);
            if ($agree == 1) {
                $btime = $check->begin_time % SERVICE_TIME_LEN == 0 ? $check->begin_time : ceil($check->begin_time/SERVICE_TIME_LEN) * SERVICE_TIME_LEN;
                $etime = $check->end_time % SERVICE_TIME_LEN == 0 ? $check->end_time : ceil($check->end_time/SERVICE_TIME_LEN) * SERVICE_TIME_LEN;

                for ($i = $btime; $i < $etime; $i += SERVICE_TIME_LEN) {
                    $appoint = StaffAppoint::where('appoint_time',$i)
                        ->where('staff_id', $check->staff_id)
                        ->first();
                    if ($appoint) {
                        StaffAppoint::where('staff_id',$check->staff_id)
                            ->where('appoint_time', $i)
                            ->update(['is_leave' => '1']);
                    } else {
                        StaffAppoint::insert([
                            'staff_id' => $check->staff_id,
                            'seller_id' => $check->seller_id,
                            'appoint_time' => $i,
                            'appoint_day' => Time::toDayTime($i),
                            'is_leave' => '1'
                        ]);
                    }
                }
            }
//print_r(DB::getQueryLog());exit;
            DB::commit();
            return $result;
        } catch(Exception $e) {
            $result['code'] = 50604;
            DB::rollback();
            return $result;
        }
    }
}
