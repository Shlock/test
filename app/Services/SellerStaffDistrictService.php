<?php namespace YiZan\Services;
 
use YiZan\Models\SellerStaffDistrict;
use YiZan\Models\SellerDistrict;
use YiZan\Models\SellerStaff;
use YiZan\Models\StaffMap;

use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use DB, Validator, Lang;

/**
 * 商圈服务
 */
class SellerStaffDistrictService extends BaseService {

    /**
     * 商圈列表
     * @param $staffId 员工编号
     */
    public static function getList($staffId) {
        $seller_id = SellerStaff::where('id', $staffId)->pluck('seller_id');
        $list = SellerDistrict::where('seller_id', $seller_id)->addSelect('id','name')->get()->toArray();
        $dids = SellerStaffDistrict::where('staff_id', $staffId)->lists('district_id');
        foreach ($list as $key=>$val) {
            $list[$key]['selected'] = in_array($val['id'], $dids) ? 1 : 0;
        }
        return $list;
    }

	/**
	 * [save 保存商圈] 
	 * @param  [int] 	$staffId     [员工编号]
	 * @param  [int] 	$districtIds  [商圈编号]
	 * @return [array]               [结果代码]
	 */
	public static function save($staffId, $districtIds) {

        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api_staffwap.success.set')
        );

        if(count($districtIds) < 1) {
            $result['code'] = 10001;//商圈不能为空
            return $result;
        }

        $district_count = SellerDistrict::whereIn('id', $districtIds)->count();
        if($district_count != count($districtIds)){
            $result['code'] = 10002;//有不存在的商圈
            return $result;
        }

        DB::beginTransaction();
        try{
            SellerStaffDistrict::where('staff_id', $staffId)->delete();
            $seller_id = SellerStaff::where('id', $staffId)->pluck('seller_id');
            $data = [];
            foreach ($districtIds as $key=>$val) {
                $data[$key] = [
                    'seller_id' => $seller_id,
                    'staff_id' => $staffId,
                    'district_id' => $val
                ];
            }
            SellerStaffDistrict::insert($data);
            DB::commit();
        } catch(Exception $e){
            DB::rollback();
            $result['code'] = 10003;
        }

        return $result;
	} 

	/**
	 * [delete 删除商圈]
	 * @param  [int]   $staffId        [员工编号]
	 * @param  [int]   $districtId     [商圈编号]
	 * @return [array]                 [结果代码]
	 */
	public static function delete($staffId, $districtId){
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api_staffwap.success.delete')
        ); 

        try{
	        SellerStaffDistrict::where('staff_id', $staffId)
	        			->where('district_id', $districtId)
	        			->delete();
        }catch(Exception $e){
        	$result['code'] = 10003;
        }
        return $result;
	}


    /**
     * 员工商圈详情
     * @param $staffId 员工编号
     */
    public static function detail($staffId) {
        $data = SellerStaff::where('id', $staffId)->with('district.district')->first()->toArray();
        foreach ($data['district'] as $key=>$val) {
                $data['district'][$key] = [
                    'id' => $val['district']['id'],
                    'name' => $val['district']['name']
                ];
        }
        return $data;
    }

    /**
     * 更新服务范围
     * @param $staffId 员工编号
     * @param $mapPos 服务范围地图坐标
     */
    public static function range($staffId, $mapPos) {
        $result = [
            'code'	=> 0,
            'data'	=> null,
            'msg' => Lang::get('api_staffwap.success.set')
        ];

        if ($mapPos == '') {
            $result['code'] = 10004;
            return $result;
        }
        $mapPos = Helper::foramtMapPos($mapPos);
        if (!$mapPos){
            $result['code'] = 10004;    // 服务范围错误
            return $result;
        }
        //更新员工表
        $format_map_pos = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
        $update = SellerStaff::where('id', $staffId)
            ->update([
                'map_pos' => $format_map_pos,
                'map_pos_str' => $mapPos["str"]
            ]);
        if ($update === false) {
            $result['code'] = 10003;
            return $result;
        }
        //更新员工坐标表
        $staff = SellerStaff::where('id', $staffId)->first();
        $map = StaffMap::where('staff_id', $staffId)->first();
        if ($map) {
            StaffMap::where('staff_id', $staffId)->update(['map_pos' => $format_map_pos]);
        } else {
            StaffMap::insert([
                'seller_id' => $staff->seller_id,
                'staff_id' => $staffId,
                'map_pos' => $format_map_pos
            ]);
        }
        return $result;
    }

}
