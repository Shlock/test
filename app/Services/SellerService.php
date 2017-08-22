<?php namespace YiZan\Services;

use YiZan\Models\Seller;
use YiZan\Models\Goods;
use YiZan\Models\Order;
use YiZan\Models\SellerExtend;
use YiZan\Models\SellerCateRelated;
use YiZan\Models\SellerCreditRank;
use YiZan\Models\StaffServiceTime;
use YiZan\Models\StaffServiceTimeSet;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use DB, Lang;

/**
 * 服务机构&个人
 */
class SellerService extends BaseService {
    /**
     * 获取服务机构&个人
     * @param  integer  $sellerId 编号
     * @param  integer  $userId   会员编号
     * @return Seller
     */
    public static function getSeller($sellerId, $userId = 0) {
        if ($userId > 0) {
            return Seller::with(array('collect' => function($query) use($userId) {
                    $query->where('user_id', $userId);
                }, 'extend.creditRank', 'staff'))->find($sellerId);
        } else {
            return Seller::with('extend.creditRank', 'staff')->find($sellerId);
        }
    }

    /**
     * 获取卖家
     * @param int $sellerId 卖家id
     * @param int $userId 用户id
     * @return array 卖家信息
     */
    public static function getById($sellerId = 0, $userId = 0) {
        if($sellerId > 0) {
            return Seller::with('province', 'city', 'area')->find($sellerId);
        }
        
        if($userId > 0) {
            return Seller::where("user_id", $userId)->with('province', 'city', 'area')->first();
        }
        return null;
    }

    /**
     * Summary of getList
     * @param mixed $cateId 
     * @param mixed $page 
     * @param mixed $order 
     * @param mixed $sort 
     * @param mixed $keywords 
     * @param mixed $userId 
     * @return mixed
     */
    public static function getList($cateId, $page, $sort, $keywords = '', $userId = 0) 
    {
        $dbPrefix = DB::getTablePrefix();
        $mapPoint = $mapPoint ? str_replace(",", " ", $mapPoint) : '0 0';
        $sql = "
SELECT  S.map_point_str AS mapPoint,
        S.id, 
        S.name, 
        S.logo, S.mobile, 
        S.brief AS detail,
        IF(C.seller_id IS NOT NULL, 1, 0) AS isCollect,
        NULL AS banner,
        '8:00–23:00' AS businessHours, 
        delivery_fee AS deliveryFee,
        service_fee AS serviceFee,
        CONCAT('起送价<span style=\"color:red\">￥',
            S.service_fee,
            '</span>&nbsp;运费<span style=\"color:#ff2d4b\">',
            S.delivery_fee,
            '</span>元&nbsp;<span style=\"color:#ff2d4b\"></span>包邮') AS freight, 
        S.service_tel AS tel, 
        S.address,
        E.order_count AS orderCount,
        (SELECT IFNULL(ROUND(SUM(star)/COUNT(id),1),0) FROM {$dbPrefix}order_rate as DR where DR.seller_id = S.id) AS score,
        (SELECT COUNT(id) FROM {$dbPrefix}goods as G where G.seller_id = S.id and G.type = 1) as countGoods,
        (SELECT COUNT(id) FROM {$dbPrefix}goods as GS where GS.seller_id = S.id and GS.type = 2) as countService
    FROM {$dbPrefix}seller AS S
        LEFT OUTER JOIN {$dbPrefix}user_collect AS C ON S.id = C.seller_id
            AND C.user_id = {$userId}
            AND {$userId} > 0
            AND C.type = 2 /* 店铺 */
        INNER JOIN {$dbPrefix}seller_extend AS E ON E.seller_id = S.id
        WHERE S.status = 1 /* 已审核 */
        ";
        
        if($cateId > 0)
        {
            $sql = "
{$sql}
            AND EXISTS
            (
                SELECT 1
                    FROM {$dbPrefix}seller_cate_related AS R
                    WHERE R.seller_id = S.id
                        AND R.cate_id = {$cateId}
            )
            ";
        }
        
        if (!empty($keywords)) 
        {
            $keywords = String::strToUnicode($keywords,'+');
            
            $sql = "
{$sql}
            AND MATCH(S.name_match) AGAINST('{$keywords}' IN BOOLEAN MODE)
            ";
        }
        
        switch ($sort) 
        {
            case 1: // 销量倒序
                $sort = "E.order_count DESC";
            break;

            case 2: // 起送价倒序
                $sort = "S.service_fee DESC";
            break;
            
            default:
                $sort = "S.sort ASC";
                break;
        }
        
        $start = max(0, ($page - 1) * 20);
        
        $end = $start + 20;
        
        $sql = "
{$sql}
            ORDER BY {$sort}, S.id ASC
            LIMIT {$start}, {$end}
            ";
       
        return DB::select($sql);
    }

    public static function checkServiceArea($sellerId, $mapPoint) {
        $seller = Seller::find($sellerId);
        if (!$seller) {
            return false;
        }
        $count = Seller::where('id', $sellerId)
            ->whereRaw("ST_Contains(map_pos,GeomFromText('Point({$mapPoint})'))")
            ->count();
        
        return $count > 0;
    }

    public static function incrementExtend($sellerId, $field, $num = 1) 
    {
        SellerExtend::where('seller_id',$sellerId)->increment($field, $num);
    }

    public static function decrementExtend($sellerId, $field, $num = 1) 
    {
        $extend = SellerExtend::where('seller_id',$sellerId)->first();
        
        if($extend == true && $extend->$field > 0)
        {
            SellerExtend::where('seller_id',$sellerId)->decrement($field, $num);
        }
    }

    public static function updateComment($sellerId, $credit, $specialtyScore, $communicateScore, $punctualityScore) 
    {
        $extend = SellerExtend::where('seller_id',$sellerId)->first();
        $extend->comment_total_count++;
        $extend->comment_specialty_total_score += $specialtyScore;
        $extend->comment_specialty_avg_score = $extend->comment_specialty_total_score / $extend->comment_total_count;

        $extend->comment_communicate_total_score += $communicateScore;
        $extend->comment_communicate_avg_score = $extend->comment_communicate_total_score / $extend->comment_total_count;

        $extend->comment_punctuality_total_score += $punctualityScore;
        $extend->comment_punctuality_avg_score = $extend->comment_punctuality_total_score / $extend->comment_total_count;

        switch($credit) {
            case 'good'://好评
                $extend->comment_good_count++;
                $extend->credit_score++;
                $extend->credit_rank_id = self::getCreditRankId($extend->credit_rank_id, $extend->credit_score);
            break;

            case 'neutral'://中评
                $extend->comment_neutral_count++;
            break;

            case 'bad'://差评
                $extend->comment_bad_count++;
                $extend->credit_score--;
                $extend->credit_rank_id = self::getCreditRankId($extend->credit_rank_id, $extend->credit_score);
            break;
        }
        $extend->save();
    }
    /**
     * 删除评价重新统计
     * @param int $sellerId 商家编号
     * @param string $credit 评价类型
     * @param double $specialtyScore 专业总分
     * @param double $communicateScore 沟通总分
     * @param double $punctualityScore 守时总分
     */
    public static function deleteComment($sellerId, $credit, $specialtyScore, $communicateScore, $punctualityScore) 
    {
        $extend = SellerExtend::where('seller_id',$sellerId)->first();
        $extend->comment_total_count--;
        $extend->comment_specialty_total_score -= $specialtyScore;
        $extend->comment_specialty_avg_score = $extend->comment_total_count <= 0 ? 0 : $extend->comment_specialty_total_score / $extend->comment_total_count;

        $extend->comment_communicate_total_score -= $communicateScore;
        $extend->comment_communicate_avg_score = $extend->comment_total_count <= 0 ? 0 : $extend->comment_communicate_total_score / $extend->comment_total_count;

        $extend->comment_punctuality_total_score -= $punctualityScore;
        $extend->comment_punctuality_avg_score = $extend->comment_total_count <= 0 ? 0 : $extend->comment_punctuality_total_score / $extend->comment_total_count;

        switch($credit) {
            case 'good'://好评
                $extend->comment_good_count--;
                $extend->credit_score--;
                $extend->credit_rank_id = self::getCreditRankId($extend->credit_rank_id, $extend->credit_score);
                break;

            case 'neutral'://中评
                $extend->comment_neutral_count--;
                break;

            case 'bad'://差评
                $extend->comment_bad_count--;
                $extend->credit_score--;
                $extend->credit_rank_id = self::getCreditRankId($extend->credit_rank_id, $extend->credit_score);
                break;
        }
        $extend->save();
    }

    public static function getCreditRankId($id, $score) {
        $credit_rank = SellerCreditRank::where('min_score', '<=', $score)
                                    ->where('max_score', '>=', $score)
                                    ->first();
        if ($credit_rank && $credit_rank->id != $id) {
            return $credit_rank->id;
        }
        return $id;
    }

    /**
     * 获取10个推荐商家
     * @param string $apoint 经纬度
     */
    public static function getRecommendSellers($apoint){
        $apoint = $apoint == '' ? '0 0' : str_replace(',', ' ', $apoint);
        $page = 1;
        //DB::connection()->enableQueryLog();
        $list = Seller::where('status', 1)
                      ->where('is_check', 1)
                      ->where('type', '<>', 3)
                      ->whereNotNull('map_point');
                      //->orderBy('sort', 'ASC');
        if($apoint){  
            $list->addSelect(DB::raw("ST_Distance(".env('DB_PREFIX')."seller.map_point,GeomFromText('POINT({$apoint})')) AS map_distance"));
            $list->orderBy('map_distance', 'ASC');
        }

        $list = $list->addSelect(DB::raw(
            env('DB_PREFIX')."seller.*,
            (select IFNULL(ROUND(SUM(star)/COUNT(id),1),0) as score from ".env('DB_PREFIX')."order_rate as o where o.seller_id = ".env('DB_PREFIX')."seller.id) as score,
            (select count(id) from ".env('DB_PREFIX')."goods as g where g.seller_id = ".env('DB_PREFIX')."seller.id and g.type = 1 and g.status = 1) as countGoods,
            (select count(id) from ".env('DB_PREFIX')."goods as s where s.seller_id = ".env('DB_PREFIX')."seller.id and s.type = 2 and s.status = 1) as countService
            "))
                     ->skip(($page - 1) * 10)
                     ->take(10)
                     ->with('deliveryTimes','extend')
                     ->get()
                     ->toArray();
        // print_r(DB::getQueryLog());exit;
        $dbPrefix = DB::getTablePrefix();
        foreach ($list as $key => $value) {
            $sql = "SELECT * FROM {$dbPrefix}staff_service_time where seller_id = {$value['id']}";
            $stime =  DB::select($sql);
            $count = count($stime);
            $list[$key]['businessHours'] = $count > 0 ? $stime[0]->begin_time . '-' . $stime[$count - 1]->end_time : '0:00-24:00';
            if ($value['serviceFee'] > 0) {
                $serviceFee = '<font color="#979797">起送价</font><font color="red">￥'.$value['serviceFee'].'</font>';
            } else {
                $serviceFee = '<font color="#979797">无起送价</font>';
            }
            $html = $serviceFee . '&nbsp;<font color="#979797">运费</font><font color="#ff2d4b">'.$value['deliveryFee'].'</font><font color="#979797">元起</font>&nbsp;<font color="#ff2d4b"></font>';
            $list[$key]['freight'] = $html;
           // $list[$key]['isDelivery'] = 0;
            $time =  $value['deliveryTimes'];
            foreach ($time as $k => $v) {
                $list[$key]['stimes'][] = $v['stime'] . '-' . $v['etime'];
            }
            $list[$key]['deliveryTime'] = $list[$key]['stimes'] ? implode(',', $list[$key]['stimes']) : '00:00-24:00';
            
            $isDelivery = self::isCanBusiness($value->id);
            $list[$key]['isDelivery'] = $isDelivery;
            if (empty($value['mapPointStr'])) {
                unset($list[$key]['mapPoint']);
            }
            $list[$key]['orderCount'] = $value['extend']['orderCount'];
            unset($list[$key]['extend']);
        }
       
       // print_r($list);
        return $list;
    }

    /**
     * 是否营业
     */
    public static function isCanBusiness($sellerId) {
        $isDelivery = 1;
        $servicetime = StaffServiceTimeSet::where('seller_id', $sellerId)->first();
        
        if ($servicetime) {
            $servicetime = $servicetime->toArray();
            foreach ($servicetime['hours'] as $key => $value) {
                $hours[] = Time::toTime($value);
            }
            if (in_array(UTC_HOUR, $hours)) {
                $isDelivery = 1;
            } else {
                $isDelivery = 0;
            }
        } 

        return $isDelivery;
    }

    /**
     * 经营类型
     */
    public static function getSellerCateLists($sellerId){
        $list = SellerCateRelated::where('seller_id', $sellerId) 
                                 ->with('cates')
                                 ->get();
        
        foreach ($list as $key => $value) { 
            $list[$key] = $value['cates'];
        }
        return $list;
    }

    /** 
     * 获取订单年份
     */
    public function getyear(){
        $list = Order::select(DB::raw("FROM_UNIXTIME(create_time,'%Y') as year_name"))
                     ->where('create_time', '<>', '')
                     ->groupBy('year_name')
                     ->get()
                     ->toArray();
        return $list;
    }

    /**
     * 商家月统计
     */
    public static function getBusinessListByMonth($sellerId, $month, $year, $cityId){   
        DB::connection()->enableQueryLog();
        $prefix = DB::getTablePrefix();
        $current = $year.'-'.sprintf("%02d", $month);
        $t = Time::toDate(Time::toTime($current), 't');  
        if($current == Time::toDate(UTC_TIME, 'Y-m')){
            $t = Time::toDate(UTC_TIME, 'd');
        } else if(Time::toTime($current) > Time::toTime(Time::toDate(UTC_DAY, 'Y-m'))){
            return ["list" => [], "sum" => []];
        }
        $sumsql = "SELECT seller_id,IFNULL(sum(total_fee), 0) as totalPayfee,
                    (IFNULL((select SUM(pay_fee + IF(discount_fee > total_fee, total_fee, discount_fee) - drawn_fee) from ".$prefix."order as od where od.seller_id = ".$sellerId." and FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m') = '".$current."' and od.pay_status = 1 and od.pay_type <> 'cashOnDelivery' AND (od.status in (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.") OR (od.status = ".ORDER_STATUS_USER_DELETE." and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_SELLER_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_ADMIN_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL))), 0) - IFNULL((select SUM(od.drawn_fee) from ".$prefix."order as od where od.seller_id = ".$sellerId." and FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m') = '".$current."' and od.pay_status = 1 and od.pay_type <> '' and od.pay_type = 'cashOnDelivery' AND (od.status in (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.") OR (od.status = ".ORDER_STATUS_USER_DELETE." and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_SELLER_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_ADMIN_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL))), 0) ) as totalSellerFee,
                    count(".$prefix."order.id) as totalNum,IFNULL(sum(".$prefix."order.drawn_fee), 0) as totalDrawnfee ,
                    IFNULL((select SUM(od.pay_fee) from ".$prefix."order as od where od.seller_id = ".$sellerId." and FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m') = '".$current."' and od.pay_status = 1 and od.pay_type <> 'cashOnDelivery' AND (od.status in (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.") OR (od.status = ".ORDER_STATUS_USER_DELETE." and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_SELLER_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_ADMIN_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL))), 0) as totalOnline,
                    IFNULL((select SUM(od.pay_fee) from ".$prefix."order as od where od.seller_id = ".$sellerId." and FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m') = '".$current."' and od.pay_status = 1 and od.pay_type <> '' and od.pay_type = 'cashOnDelivery' AND (od.status in (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.") OR (od.status = ".ORDER_STATUS_USER_DELETE." and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_SELLER_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_ADMIN_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL))), 0) as totalCash, 
                    IFNULL(sum(IF(discount_fee > total_fee, total_fee, discount_fee)), 0) as totalDiscountFee
            FROM ".$prefix."order
            WHERE pay_status = 1 
            AND seller_id = ".$sellerId."
            AND (status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
            OR (status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL) 
            OR (status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL) 
            OR (status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL))
            AND FROM_UNIXTIME(".$prefix."order.buyer_finish_time,'%Y-%m') = '".$current."'";
        $sum = DB::select($sumsql); 
        $sum['seller'] = Seller::find($sellerId);
        /**
         sql说明：左连接order表 查询 id, name, totalPayfee(总金额), totalNum(总数量), totalDrawnfee(总佣金), totalOnline(总在线支付), totalCash(总现金支付), totalDiscountFee(总优惠金额), totalSellerFee(商家盈利)
         在线和现金支付数据 通过子查询实现
         */
        $sql = "select IFNULL(sum(".$prefix."order.total_fee), 0) as totalPayfee,
                count(".$prefix."order.id) as totalNum,
                IFNULL(sum(".$prefix."order.drawn_fee), 0) as totalDrawnfee ,
                (IFNULL((select SUM(od.pay_fee + IF(od.discount_fee > od.total_fee, od.total_fee, od.discount_fee) - od.drawn_fee) from ".$prefix."order as od where od.seller_id = ".$sellerId." and FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m-%d') = FROM_UNIXTIME(".$prefix."order.buyer_finish_time,'%Y-%m-%d') and od.pay_status = 1 and od.pay_type <> 'cashOnDelivery' AND (od.status in (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.") OR (od.status = ".ORDER_STATUS_USER_DELETE." and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_SELLER_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_ADMIN_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL))), 0) - IFNULL((select SUM(od.drawn_fee) from ".$prefix."order as od where od.seller_id = ".$sellerId." and FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m-%d') = FROM_UNIXTIME(".$prefix."order.buyer_finish_time,'%Y-%m-%d') and od.pay_status = 1 and od.pay_type <> '' and od.pay_type = 'cashOnDelivery' AND (od.status in (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.") OR (od.status = ".ORDER_STATUS_USER_DELETE." and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_SELLER_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_ADMIN_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL))), 0)) as totalSellerFee,
                IFNULL((select SUM(od.pay_fee) from ".$prefix."order as od where od.seller_id = ".$sellerId." and FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m-%d') = FROM_UNIXTIME(".$prefix."order.buyer_finish_time,'%Y-%m-%d') and od.pay_status = 1 and od.pay_type <> 'cashOnDelivery' AND (od.status in (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.") OR (od.status = ".ORDER_STATUS_USER_DELETE." and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_SELLER_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_ADMIN_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL))), 0) as totalOnline,
                IFNULL((select SUM(od.pay_fee) from ".$prefix."order as od where od.seller_id = ".$sellerId." and FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m-%d') = FROM_UNIXTIME(".$prefix."order.buyer_finish_time,'%Y-%m-%d') and od.pay_status = 1 and od.pay_type <> '' and od.pay_type = 'cashOnDelivery' AND (od.status in (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.") OR (od.status = ".ORDER_STATUS_USER_DELETE." and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_SELLER_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_ADMIN_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL))), 0) as totalCash,
                IFNULL(sum(IF(discount_fee > total_fee, total_fee, discount_fee)), 0) as totalDiscountFee,
                FROM_UNIXTIME(buyer_finish_time,'%Y-%m-%d') as daytime
                from ".$prefix."order 
                where ".$prefix."order.seller_id = ".$sellerId." 
                and ".$prefix."order.pay_status = 1  
                and FROM_UNIXTIME(".$prefix."order.buyer_finish_time,'%Y-%m') = '".$current."' 
                AND (".$prefix."order.status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.")
                OR (".$prefix."order.status = ".ORDER_STATUS_USER_DELETE." AND ".$prefix."order.buyer_finish_time > 0 AND ".$prefix."order.cancel_time IS NULL) 
                OR (".$prefix."order.status = ".ORDER_STATUS_SELLER_DELETE." AND ".$prefix."order.auto_finish_time > 0 AND ".$prefix."order.cancel_time IS NULL) 
                OR (".$prefix."order.status = ".ORDER_STATUS_ADMIN_DELETE." AND ".$prefix."order.auto_finish_time > 0 AND ".$prefix."order.cancel_time IS NULL))
                GROUP BY FROM_UNIXTIME(".$prefix."order.buyer_finish_time,'%Y-%m-%d')"; 
        $queryData = DB::select($sql);  
        $list = [];
        for($i = 1; $i <= $t; $i++) {
            $daytime = $current . '-' . sprintf("%02d", $i);  
            $dayData = [
                'totalPayfee' => 0,
                'totalNum' => 0,
                'totalDrawnfee' => 0,
                'totalSellerFee' => 0,
                'totalOnline' => 0,
                'totalCash' => 0,
                'totalDiscountFee' => 0, 
                'daytime' => $daytime,
            ]; 
            $bool = false;
            foreach ($queryData as $item) { 
                $item = (array)$item;
                if($item['daytime'] == $daytime){
                    $bool = true; 
                    break;
                }
            }
            if($bool){
                $list[] = $item;
            } else {
                $list[] = $dayData;
            }
        }  
        // print_r(DB::getQueryLog());exit;  
        //file_put_contents('/mnt/test/sq/storage/logs/sellerservice1.log', print_r(DB::getQueryLog(),true)."\n", FILE_APPEND);
        return ["list" => $list, "sum" => $sum[0]];
    }

    /**
     * 商家日统计
     */
    public static function getBusinessListByDay($sellerId, $day, $sn, $status, $page, $pageSize){   
        // DB::connection()->enableQueryLog();
        $prefix = DB::getTablePrefix();  
        $list = Order::where('seller_id', $sellerId)
                     ->whereRaw("FROM_UNIXTIME(buyer_finish_time,'%Y-%m-%d') = '".$day."'");
        
        if($sn == true){
            $list->where('sn', $sn);
        }

        if($status == true){
            switch ($status) {
                case '2'://已取消
                    $arrStatus = [ORDER_STATUS_CANCEL_USER, ORDER_STATUS_CANCEL_AUTO, ORDER_STATUS_CANCEL_SELLER, ORDER_STATUS_CANCEL_ADMIN, ORDER_STATUS_REFUND_AUDITING,ORDER_STATUS_CANCEL_REFUNDING,ORDER_STATUS_REFUND_HANDLE,ORDER_STATUS_REFUND_FAIL,ORDER_STATUS_REFUND_SUCCESS,ORDER_REFUND_SELLER_AGREE,ORDER_REFUND_SELLER_REFUSE,ORDER_REFUND_ADMIN_AGREE,ORDER_REFUND_ADMIN_REFUSE];
                    break;
                case '3'://未完成
                    $arrStatus = [ORDER_STATUS_PAY_SUCCESS,ORDER_STATUS_START_SERVICE,ORDER_STATUS_PAY_DELIVERY,ORDER_STATUS_AFFIRM_SELLER,ORDER_STATUS_FINISH_STAFF];
                    break;
                
                default://已完成
                    $arrStatus = [ORDER_STATUS_FINISH_SYSTEM, ORDER_STATUS_FINISH_USER, ORDER_STATUS_USER_DELETE, ORDER_STATUS_SELLER_DELETE, ORDER_STATUS_ADMIN_DELETE]; 
                    break;
            }
            $list->whereIn('status', $arrStatus);
        }

        $totalCount = $list->count();

        $list = $list->skip(($page - 1) * $pageSize)
                     ->take($pageSize)
                     ->get()
                     ->toArray();

        $sum = Order::where('seller_id', $sellerId)
                    ->whereRaw("FROM_UNIXTIME(buyer_finish_time,'%Y-%m-%d') = '".$day."' AND (status IN (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.") OR (status = ".ORDER_STATUS_USER_DELETE." AND buyer_finish_time > 0 AND cancel_time IS NULL) OR (status = ".ORDER_STATUS_SELLER_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL) OR (status = ".ORDER_STATUS_ADMIN_DELETE." AND auto_finish_time > 0 AND cancel_time IS NULL))")
                    ->where('pay_status', 1) 
                    ->selectRaw("seller_id, count(id) as totalNum, (IFNULL((select SUM(od.pay_fee + IF(od.discount_fee > od.total_fee, od.total_fee, od.discount_fee) - od.drawn_fee) from ".$prefix."order as od where od.seller_id = ".$sellerId." and FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m-%d') = '".$day."' and od.pay_status = 1 and od.pay_type <> 'cashOnDelivery' AND (od.status in (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.") OR (od.status = ".ORDER_STATUS_USER_DELETE." and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_SELLER_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_ADMIN_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL))), 0) - IFNULL((select SUM(od.drawn_fee) from ".$prefix."order as od where od.seller_id = ".$sellerId." and FROM_UNIXTIME(od.buyer_finish_time,'%Y-%m-%d') = '".$day."' and od.pay_status = 1 and od.pay_type <> '' and od.pay_type = 'cashOnDelivery' AND (od.status in (".ORDER_STATUS_FINISH_SYSTEM.", ".ORDER_STATUS_FINISH_USER.") OR (od.status = ".ORDER_STATUS_USER_DELETE." and od.buyer_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_SELLER_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL) OR (od.status = ".ORDER_STATUS_ADMIN_DELETE." and od.auto_finish_time > 0 AND od.cancel_time IS NULL))), 0)) as totalSellerFee") 
                    ->first() ; 
        $sum['seller'] = Seller::find($sellerId); 
        // print_r(DB::getQueryLog());exit;  
        //file_put_contents('/mnt/test/sq/storage/logs/sellerservice.log', print_r(DB::getQueryLog(),true)."\n", FILE_APPEND);
        return ["list" => $list, "totalCount" => $totalCount, "sum" => $sum];
    }
	/**
     * 验证身份证号
     * @param $vStr
     * @return bool
     */
    public function isCreditNo($vStr)
    {
        $vCity = array(
            '11', '12', '13', '14', '15', '21', '22',
            '23', '31', '32', '33', '34', '35', '36',
            '37', '41', '42', '43', '44', '45', '46',
            '50', '51', '52', '53', '54', '61', '62',
            '63', '64', '65', '71', '81', '82', '91'
        );

        if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr))
        {
            return false;
        }

        if (!in_array(substr($vStr, 0, 2), $vCity))
        {
            return false;
        }

        $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
        $vLength = strlen($vStr);

        if ($vLength == 18)
        {
            $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
        }
        else
        {
            $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
        }

        if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday)
        {
            return false;
        }
        if ($vLength == 18)
        {
            $vSum = 0;

            for ($i = 17; $i >= 0; $i--)
            {
                $vSubStr = substr($vStr, 17 - $i, 1);
                $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr, 11));
            }

            if ($vSum % 11 != 1)
            {
                return false;
            }
        }

        return true;
    }
}
