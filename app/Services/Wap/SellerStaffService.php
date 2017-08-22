<?php namespace YiZan\Services\Wap;
use YiZan\Models\SellerStaffExtend;
use YiZan\Models\SellerStaff;
use YiZan\Models\StaffAppoint;
use YiZan\Models\SellerExtend;
use YiZan\Models\SellerCreditRank;
use YiZan\Models\SellerAppoint;
use YiZan\Models\Seller;
use YiZan\Utils\Helper;
use DB, Exception,Time;
class SellerStaffService extends \YiZan\Services\SellerStaffService 
{

    // /**
    //  * [getList description]
    //  * @param  [int] $cityId        [城市编号]
    //  * @param  [int] $goodsId        [服务编号]
    //  * @param  [type] $page        [description]
    //  * @param  [type] $order       [description]
    //  * @param  [type] $sort        [description]
    //  * @param  string $keywords    [description]
    //  * @param  string $appointTime [description]
    //  * @param  string $mapPoint    [description]
    //  * @return [type]              [description]
    //  */
    // public static function getStaffList($cityId, $goodsId, $page, $order, $sort, $keywords = '', $appointTime = '', $mapPoint = '', $appointMapPoint = '') {

    //     $mapPoint = empty($appointMapPoint) ? Helper::foramtMapPoint($mapPoint) : Helper::foramtMapPoint($appointMapPoint);
    //     $mapPoint = $mapPoint ? str_replace(',', ' ', $mapPoint) : '';
    //     $list = SellerStaff::where('status', 1)
    //                         ->where('is_del',0)
    //                         ->with('extend');
    //     //可接单的服务机构
    //     $list->whereIn('seller_staff.seller_id',function($query)
    //     {
    //         $query->select("id")
    //             ->from('seller')
    //             ->where('status', 1)
    //             ->where('business_status', 1);
    //     });
    //     $sort = $sort == 1 ? 'desc' : 'asc';
    //     switch ($order) {
    //         case 1://距离排序
    //             if ($mapPoint) {
    //                 $list->addSelect(DB::raw("ST_Distance(".env('DB_PREFIX')."seller_staff.map_point,GeomFromText('POINT({$mapPoint})')) AS map_distance"));
    //                 $list->orderBy('map_distance', $sort);
    //             }
    //             break;

    //         case 2://星级排序
    //             $list->join('seller_staff_extend', 'seller_staff_extend.staff_id', '=', 'seller_staff.id');
    //             $list->orderBy('order_count', $sort);
    //             break;

    //         case 3://均价排序
    //             $list->join('seller_staff_extend', 'seller_staff_extend.staff_id', '=', 'seller_staff.id');
    //             $list->orderBy('comment_good_count', $sort);
    //             break;
    //     }

    //     $list->orderBy('sort', 'asc');
    //     $list->orderBy('seller_staff.id', 'asc');

    //     $keywords = empty($keywords) ? '' : String::strToUnicode($keywords,'+');
    //     if (!empty($keywords)) {
    //         $list->whereRaw('MATCH('.env('DB_PREFIX').'seller_staff.name_match) AGAINST(\'' . $keywords . '\' IN BOOLEAN MODE)');
    //     }

    //     if ($goodsId == true) {
    //         $list->whereIn('seller_staff.id',function($query) use ($goodsId)
    //         {
    //             $query->select("staff_id")
    //                 ->from('goods_staff')
    //                 ->where('goods_id', $goodsId);
    //         });
    //     }
    //     if($cityId == true)
    //     {
    //         $list->where(function($query) use($cityId)
    //         {
    //             $query->where('province_id', $cityId)
    //                 ->orWhere('city_id', $cityId);
    //         });
    //     }

    //     if (!empty($appointTime)) {//预约时间
    //         $appointTime = Time::toTime($appointTime);
    //         $list->leftJoin('staff_appoint', function($join) use($appointTime) {
    //             $join->on('staff_appoint.staff_id', '=', 'seller_staff.id')
    //                 ->where('staff_appoint.appoint_time', '=', $appointTime);
    //         });

    //         $list->where(function($query) {
    //             $query->where('staff_appoint.status', 0)
    //                 ->orWhereNull('staff_appoint.status');
    //         });
    //     }

    //     $list->addSelect(DB::raw("*, (select count(1) from ".env('DB_PREFIX')."order as o where o.seller_staff_id = ".env('DB_PREFIX')."seller_staff.id) as now_order"));
    //     return $list->skip(($page - 1) * 20)->take(20)->get()->toArray();
    // }


    // /**
    //  * 获取员工
    //  * @param  [type]  $staffId [description]
    //  * @param  integer $userId   [description]
    //  * @return [type]            [description]
    //  */
    // public static function getStaff($staffId, $userId = 0) {
    //     if ((int)$userId > 0) {
    //         return SellerStaff::with(array('collect' => function($query) use($userId) {
    //             $query->where('user_id', $userId);
    //         }, 'extend','seller'))->find($staffId);
    //     } else {
    //         return SellerStaff::with('extend','seller')->find($staffId);
    //     }
    // }


}
