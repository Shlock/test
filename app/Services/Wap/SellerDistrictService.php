<?php namespace YiZan\Services\Wap;
 
use YiZan\Models\SellerDistrict;
use YiZan\Models\SellerStaffDistrict;
use YiZan\Models\SellerStaff;
use Illuminate\Database\Query\Expression;

use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use DB, Validator, Lang;

/**
 * 商圈服务
 */
class SellerDistrictService extends YiZan\Services\SellerDistrictService { 

    /**
     * 查看员工
     * @param  [type] $districtId [小区ID]
     * @return [array]            [返回数组]
     */
    public static function lookstaff($districtId) {
        $list = SellerStaff::select('seller_staff.*')
                        ->join('seller_staff_district', function($join) use($districtId) {
                            $join->on('seller_staff_district.staff_id', '=', 'seller_staff.id')
                                ->where('seller_staff_district.district_id', '=', $districtId);
                        });     

        $totalCount = $list->count();
        $list = $list->get()
                    ->toArray();
        return ["list"=>$list, "totalCount"=>$totalCount];
    }

    /**
     * [searchResponsible 搜索小区]
     * @param  [type] $sellerId [商家ID]
     * @param  [type] $keywords [小区关键字]
     * @return [type]           [description]
     */
    public static function searchresponsible($sellerId, $keywords, $page, $pageSize){
        parent::searchresponsible();
        $list = SellerDistrict::where('name', 'like', '%'.$keywords.'%')
                                ->orWhere('address', 'like', '%'.$keywords.'%');

        $totalCount = $list->count();
        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize) 
            ->get()
            ->toArray();
        return ["list"=>$list, "totalCount"=>$totalCount];
    }


    /**
     * [getstaff 获取小区员工列表]
     * @param  [type] $districtId  [小区ID]
     * @param  [type] $name        [员工姓名]
     * @param  [type] $mobile      [员工电话]
     * @param  [type] $responsible [是否当前小区 1=负责 2=不负责]
     * @param  [type] $page        [分页]
     * @param  [type] $pageSize    [分页大小]
     * @return [type]              [返回数组]
     */
    public static function getstaff($districtId, $name=null, $mobile=null, $responsible=2, $page, $pageSize) {
        $list = SellerStaff::select('seller_staff.*');
        if($responsible == 1){
            $list->addSelect('seller_district.name as districtName')
                ->join('seller_staff_district', function($join) use($districtId) {
                    $join->on('seller_staff_district.staff_id', '=', 'seller_staff.id')
                        ->where('seller_staff_district.district_id', '=', $districtId);
                })
                ->join('seller_district','seller_district.id', '=', 'seller_staff_district.district_id'); 
        }else{
            $list->whereNotExists(function($query) use($districtId) {
                    $seller_staff_table = DB::getTablePrefix().'seller_staff';
                    $query->select(DB::raw(1))
                          ->from('seller_staff_district')
                          ->where('seller_staff_district.district_id', '=', $districtId)
                          ->where('seller_staff_district.staff_id', '=', new Expression("{$seller_staff_table}.id"));
                });
        }

        if (!empty($name)) {
            $list->where('seller_staff.name', 'like', '%'.$name.'%');
        }
        if(!empty($mobile)) {
            $list->where('seller_staff.mobile', 'like', '%'.$mobile.'%');
        }

        $totalCount = $list->count();
        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize) 
            ->groupBy('seller_staff.id')
            ->get()
            ->toArray();

        return ["list"=>$list, "totalCount"=>$totalCount];
    }



}
