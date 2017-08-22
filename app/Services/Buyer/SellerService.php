<?php namespace YiZan\Services\Buyer;

use YiZan\Models\Seller;
use YiZan\Models\User;
use YiZan\Models\Goods;
use YiZan\Models\SellerExtend;
use YiZan\Models\SellerMap;
use YiZan\Models\SellerCateRelated;
use YiZan\Models\SellerAuthenticate;
use YiZan\Services\AdvService;
use YiZan\Models\SellerStaff;
use YiZan\Models\SellerCate;
use YiZan\Models\SellerStaffExtend;
use YiZan\Models\SellerDeliveryTime;
use YiZan\Models\StaffServiceTimeSet;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use YiZan\Utils\String;
use DB, Lang, Validator;

class SellerService extends \YiZan\Services\SellerService 
{
    /**
     * Summary of getSellerDetail
     * @param mixed $id 
     * @param mixed $userId 
     * @return mixed
     */
    public static function getSellerDetail($id, $userId = 0)
    {
        $dbPrefix = DB::getTablePrefix();
        
        $sql = "
SELECT  S.map_point_str AS mapPointStr,
        S.id, 
        S.name, 
        S.city_id,
        S.logo,S.mobile,S.image,
        S.brief AS detail,
        IF(C.seller_id IS NOT NULL, 1, 0) AS isCollect,
        NULL AS banner,
        delivery_fee AS deliveryFee,
        service_fee AS serviceFee,
        CONCAT('<font color=\"#979797\">起送价</font><font color=\"red\">￥',
            S.service_fee,
            '</font>&nbsp;<font color=\"#979797\">运费</font><font color=\"#ff2d4b\">',
            S.delivery_fee,
            '</font><font color=\"#979797\">元起</font>&nbsp;<font color=\"#ff2d4b\"></font>') AS freight, 
        S.mobile AS tel, 
        S.address,
        E.order_count AS orderCount
    FROM {$dbPrefix}seller AS S
        LEFT OUTER JOIN {$dbPrefix}user_collect AS C ON S.id = C.seller_id
            AND C.user_id = {$userId}
            AND {$userId} > 0
            AND C.type = 2 /* 店铺 */
        INNER JOIN {$dbPrefix}seller_extend AS E ON E.seller_id = S.id
        WHERE S.id = {$id}
        and S.status = 1 and S.is_check = 1
        ";
        $data = DB::selectOne($sql);

        if(empty($data)){
            return $data;
        }

        $adv = AdvService::getAdvByCode('BUYER_JNIWCZKA', $data->city_id);
        
        foreach($adv as $key => $value)
        {
             if($value['type'] == 7) //文章
            {
                $adv[$key]['type'] = '5';
                $adv[$key]["arg"] = u('Wap#Article/detailapp',array('id'=>$value['arg']));
            }
        }
        $data->banner =$adv;
        $sql = "SELECT * FROM {$dbPrefix}staff_service_time where seller_id = {$id}";
        $stime =  DB::select($sql);
        $count = count($stime);
        $data->businessHours = $count > 0 ? $stime[0]->begin_time . '-' . $stime[$count - 1]->end_time : '0:00-24:00';
        $data->countGoods = Goods::where('seller_id', $data->id)->where('status',1)->where('type', 1)->count();//商品
        $data->countService = Goods::where('seller_id', $data->id)->where('status',1)->where('type', 2)->count();//服务
        //$data->isDelivery = 0;
        $time =  SellerDeliveryTime::where('seller_id', $data->id)->get()->toArray();
        foreach ($time as $k => $v) {
            $data->stimes[] = $v['stime'] . '-' . $v['etime'];
        }
        $data->deliveryTime = $data->stimes ? implode(',', $data->stimes) : '00:00-24:00';
        $isDelivery = SellerService::isCanBusiness($data->id);
        $data->isDelivery = $isDelivery;
        $mapPoint = explode(',', $data->mapPointStr);
        $data->mapPoint['x'] = $mapPoint[0];
        $data->mapPoint['y'] = $mapPoint[1];
        
        return $data;
    }
    /**
     * Summary of getSellerList
     * @param mixed $cateId 
     * @param mixed $page 
     * @param mixed $sort 
     * @param mixed $keywords 
     * @param mixed $userId 
     * @return mixed
     */
    public static function getSellerList($cateId, $page, $sort, $keywords = '', $userId = 0, $mapPoint = '0 0')
    {
        $dbPrefix = DB::getTablePrefix();
        $mapPoint = $mapPoint == '' ? '0 0' : str_replace(',', ' ', $mapPoint);
        $sql = "
SELECT  S.map_point_str AS mapPointStr,
        S.id, 
        S.name, 
        S.logo,S.mobile,S.image,
        S.brief AS detail,
        IF(C.seller_id IS NOT NULL, 1, 0) AS isCollect,
        NULL AS banner,
        delivery_fee AS deliveryFee,
        service_fee AS serviceFee,
        CONCAT('<font color=\"#979797\">起送价</font><font color=\"red\">￥',
            S.service_fee,
            '</font>&nbsp;<font color=\"#979797\">运费</font><font color=\"#ff2d4b\">',
            S.delivery_fee,
            '</font><font color=\"#979797\">元</font>&nbsp;<font color=\"#ff2d4b\"></font>') AS freight, 
        S.service_tel AS tel, 
        S.address,
        E.order_count AS orderCount,
        (SELECT IFNULL(ROUND(SUM(star)/COUNT(id),1),0) FROM {$dbPrefix}order_rate as DR where DR.seller_id = S.id) AS score,
        (SELECT COUNT(id) FROM {$dbPrefix}goods as G where G.seller_id = S.id and G.type = 1 and G.status = 1) as countGoods,
        (SELECT COUNT(id) FROM {$dbPrefix}goods as GS where GS.seller_id = S.id and GS.type = 2 and GS.status = 1) as countService,
        ST_Distance(S.map_point,GeomFromText('POINT({$mapPoint})')) AS distance
    FROM {$dbPrefix}seller AS S
        LEFT OUTER JOIN {$dbPrefix}user_collect AS C ON S.id = C.seller_id
            AND C.user_id = {$userId}
            AND {$userId} > 0
            AND C.type = 2 /* 店铺 */
        INNER JOIN {$dbPrefix}seller_extend AS E ON E.seller_id = S.id
        WHERE S.is_check = 1 /* 已审核 */
        AND S.`status` = 1 /* 正常 */
		AND S.`is_del` = 0 /* 未删除 */
        /*and ST_Contains(S.map_pos,GeomFromText('Point({$mapPoint})'))  查询在范围内的商家 */
        ";
        if($cateId > 0)
        {
            $cateIds = [$cateId];
            $childs = SellerCate::where('pid', $cateId)->get();
            foreach ($childs as $child) {
                $cateIds[] = $child['id'];
            }
            $cateIds = implode(',', $cateIds);
            $sql = "
{$sql}
            AND EXISTS
            (
                SELECT 1
                    FROM {$dbPrefix}seller_cate_related AS R
                    WHERE R.seller_id = S.id 
                        AND R.cate_id IN ({$cateIds})
            )
            ";
        }
        
        if (!empty($keywords)) 
        {
            /*$keywords = String::strToUnicode($keywords,'+');
            
            $sql = "
{$sql}
            AND MATCH(S.name_match) AGAINST('{$keywords}' IN BOOLEAN MODE)
            ";*/
			$sql = "{$sql} AND S.name like '%{$keywords}%' ";
        }
        
        switch ($sort) 
        {
            case 1: // 销量倒序
                $sort = "E.order_count DESC";
                break;

            case 2: // 起送价倒序
                $sort = "S.service_fee ASC";
                break;

            case 3: // 距离最近
                $sort = "distance ASC";
                break;

            case 4: // 评分最高
                $sort = "score DESC";
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
        $res = DB::select($sql);
       // print_r($res);
        foreach ($res as $key => $value) {
            $mapPoint = explode(',', $value->mapPointStr);
            $res[$key]->mapPoint['x'] = $mapPoint[0];
            $res[$key]->mapPoint['y'] = $mapPoint[1];

            $sql = "SELECT * FROM {$dbPrefix}staff_service_time where seller_id = ". $value->id;
            $stime =  DB::select($sql);
            $count = count($stime);
            $res[$key]->businessHours = $count > 0 ? $stime[0]->begin_time . '-' . $stime[$count - 1]->end_time : '0:00-24:00';

            $time =  SellerDeliveryTime::where('seller_id', $res[$key]->id)->get()->toArray();
            foreach ($time as $k => $v) {
                $res[$key]->stimes[] = $v['stime'] . '-' . $v['etime'];
            }
            $res[$key]->deliveryTime = $res[$key]->stimes ? implode(',', $res[$key]->stimes) : '00:00-24:00';
 
            $isDelivery = SellerService::isCanBusiness($value->id);
            $res[$key]->isDelivery = $isDelivery;
           // print_r($isDelivery);

        }
       
       //print_r($res);
        return $res;
    }

     /**
     * 检查是否注册商家并且通过
     * @param int $id 会员编号
     */
    public function checkUser($id){ 
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => '成功'
        );
        
        $data = Seller::where('seller.user_id', $id)
            ->join("seller_authenticate", "seller.id", "=", "seller_authenticate.seller_id")
            ->select("*")
            ->first();
        
        if($data)
        {
            if($data->is_check == 1) 
            {
                $data->appurl = u("wap#seller/app");
                $result['code'] = 30019;
                $result['msg'] = '失败';
            }
            
            $data->detail = $data->brief;
            DB::connection()->enableQueryLog();
            $data->cateIds = SellerCateRelated::where("seller_id", $data->id)
                ->join("seller_cate", "seller_cate_related.cate_id", "=", "seller_cate.id")
                ->select("seller_cate.id", "seller_cate.name")
                ->get()
                ->toArray();
         }
        
        $result['data'] = $data;

        return $result;
    }

    /**
     * 商家注册
     */
    public static function createSeller($userId, $sellerType, $logo, $name, $cateIds, $address,$addressDetail,$provinceId,$cityId,$areaId,$mapPointStr,$mapPosStr, $mobile, $pwd, $idcardSn,
                                        $idcardPositiveImg, $idcardNegativeImg, $businessLicenceImg, $introduction, $serviceFee, $deliveryFee,$contacts, $serviceTel) {
        $result = array(
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api.success.submit')
        );

        $seller = Seller::where('user_id', $userId)
                        ->first();

        if($seller && $seller->is_check != -1)
        {
            $result['code'] = 30022;
            return $result;
        }
        $rules = array(
            'mobile'                        => ['required','regex:/^1[0-9]{10}$/'],  
            'name'                          => ['required','min:2','max:30'],
            'logo'                          => ['required'],
            'cateIds'                       => ['required'],
            'address'                       => ['required'], 
            'idcardSn'                      => ['required','regex:/^[0-9]{18}|[0-9]{15}|[0-9]{17}[xX]{1}$/'],
            'idcardPositiveImg'             => ['required'],
            'idcardNegativeImg'             => ['required'],
            'contacts'                       => ['required'],
            'serviceTel'                     => ['required','regex:/^((0[0-9]{2,3}\-?)?[0-9]{7,8}$)|(1[0-9]{10})$/']
            // 'businessLicenceImg'            => ['required'],
           // 'introduction'                  => ['required'],
        );

        $messages = array(
            'mobile.required'               => '30020',
            'mobile.regex'                  => '30021',  
            'name.required'                 => '30003',
            'name.min'                      => '30004',
            'name.max'                      => '30005', 
            'logo.required'                 => '30006',
            'cateIds.required'              => '30007',
            'address.required'              => '30008',   // 请输入地址
            'idcardSn.required'             => '30009',
            'idcardSn.regex'                => '30010',
            'idcardPositiveImg.required'    => '30011',
            'idcardNegativeImg.required'    => '30012',
            'contacts.required'              =>  '30023',
            'serviceTel.required'              =>  '30024',
            'serviceTel.regex'              =>  '30025'
            // 'businessLicenceImg.required'   => '30013',
           // 'introduction.required'         => '30014',   //

        );

        $validator = Validator::make([
                'mobile'                => $mobile,  
                'name'                  => $name,
                'logo'                  => $logo,
                'cateIds'               => $cateIds, 
                'address'               => $address, 
                'idcardSn'              => $idcardSn,
                'idcardPositiveImg'     => $idcardPositiveImg,
                'idcardNegativeImg'     => $idcardNegativeImg,
                'contacts'              => $contacts,
                'serviceTel'              => $serviceTel
               // 'businessLicenceImg'    => $businessLicenceImg,
               // 'introduction'          => $introduction, 
            ], $rules, $messages);
        if ($validator->fails()) {//验证信息
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        $mapPoint = Helper::foramtMapPoint($mapPointStr);
        if (!$mapPoint){
            $result['code'] = 30615;    // 地图定位错误
            return $result;
        }

        if(empty($mapPosStr)){
            $point = explode(',', $mapPointStr);
            $point1 = ($point[0] + 0.01) . ',' . ($point[1] + 0.01);
            $point2 = ($point[0] + 0.01) . ',' . ($point[1] - 0.01);
            $point3 = ($point[0] - 0.01) . ',' . ($point[1] - 0.01);
            $point4 = ($point[0] - 0.01) . ',' . ($point[1] + 0.01); 
            $mapPosStr = $point1 . '|' . $point2 . '|' . $point3 . '|' . $point4;
        }

        $mapPos = Helper::foramtMapPos($mapPosStr);

        if (!$mapPos) {
            $result['code'] = 30617;    // 服务范围错误
            return $result;
        } 

        DB::beginTransaction();
        try {
            $user = User::find($userId);
            if (!$user) { 
                $result['code'] = 30015;
                return $result;
            }

            $logo = self::moveUserImage($user->id, $logo);
            if (!$logo) {//转移图片失败
                $result['code'] = 30016;
                return $result;
            } 
            
            if($seller == false)
            {
                $seller = new Seller;
            }
           
            $seller->type             = $sellerType;
            $seller->user_id          = $user->id;
            $seller->mobile           = $mobile;
            $seller->logo             = $logo;
            $seller->name             = $name;
            $seller->name_match       = String::strToUnicode($name.$mobile);
            $seller->address          = $address;
            $seller->address_detail  = $addressDetail;
            $seller->province_id      = $provinceId;
            $seller->city_id           = $cityId;
            $seller->area_id           = $areaId;
            $seller->map_point_str    = $mapPoint;
            $seller->map_pos_str      = $mapPos['str'];
            $seller->map_point        = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
            $seller->map_pos          = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
            $seller->create_time      = UTC_TIME;
            $seller->create_day       = UTC_DAY;
            $seller->brief            = $introduction; 
            $seller->service_fee      = floatval($serviceFee);
            $seller->delivery_fee     = floatval($deliveryFee);
            $seller->contacts       = $contacts;
            $seller->service_tel     = $serviceTel;
            $seller->is_check         = 0;
            $seller->save();

            //创建商家扩展信息表
            if(SellerExtend::where("seller_id", $seller->id)->first() == false)
            {
                $sellerExtend = new SellerExtend();
                $sellerExtend->seller_id = $seller->id;
                $sellerExtend->save();
            }

            $auth = SellerAuthenticate::where('idcard_sn', $idcardSn)->first();

            if($auth == true)
            {
                if($auth->seller_id != $seller->id)
                {
                    $result['code'] = 30017;    //身份证号码已存在
                    DB::rollback();
                    return $result;
                }
            }
            else
            {
                $auth = SellerAuthenticate::where('seller_id', $seller->id)->first();
                
                if($auth == false)
                {
                    $auth = new SellerAuthenticate();
                }
            }

            $idcardPositiveImg = self::moveSellerImage($seller->id, $idcardPositiveImg);
            if (!$idcardPositiveImg) 
            {
                //转移图片失败
                $result['code'] = 30016;
                return $result;
            }

            $idcardNegativeImg = self::moveSellerImage($seller->id, $idcardNegativeImg);
            if (!$idcardNegativeImg) 
            {
                //转移图片失败
                $result['code'] = 30016;
                return $result;
            }
           
            if($seller->type == Seller::SERVICE_ORGANIZATION)
            {
                if($businessLicenceImg == false)
                {
                    $result['code'] = 30013; // 公司营业执照相片不能为空
                    return $result;
                }
                
                $businessLicenceImg = self::moveSellerImage($seller->id, $businessLicenceImg);
                
                if (!$businessLicenceImg) 
                {
                    //转移图片失败
                    $result['code'] = 30016;
                    return $result;
                }

                $businessLicenceImg = $businessLicenceImg;
            }

            if ($seller->type == Seller::SELF_ORGANIZATION)
            {
                // if($businessLicenceImg == false)
                // {
                //     $result['code'] = 10207; // 资质证书不能为空
                //     return $result;
                // }
                
                // $certificateImg = self::moveSellerImage($seller->id, $businessLicenceImg);
                
                // if (!$certificateImg) 
                // {
                //     //转移图片失败
                //     $result['code'] = 30016;
                //     return $result;
                // }
                // $certificateImg = $certificateImg;
            }
            
            $auth->seller_id            = $seller->id;
            $auth->idcard_sn            = $idcardSn;
            $auth->idcard_positive_img  = $idcardPositiveImg;
            $auth->idcard_negative_img  = $idcardNegativeImg;
            $auth->certificate_img      = isset($certificateImg) ? $certificateImg : NULL;
            $auth->business_licence_img = $businessLicenceImg;
            $auth->update_time          = UTC_TIME;
            $auth->save();

            //if($sellerType == Seller::SELF_ORGANIZATION)
            //{
                //如果个人加盟版 则保存至员工表
                $staff = SellerStaff::where("user_id", $user->id)->where("seller_id", $seller->id)->first();
                
                if($staff == false)
                {
                    $staff = new SellerStaff();
                }
                //个人加盟、商家加盟都生成一个员工
                if($sellerType == Seller::SELF_ORGANIZATION) {
                    $staff->type           = 0;
                } else {
                    $staff->type           = 3;
                }
                $staff->user_id            = $user->id;
                $staff->seller_id          = $seller->id;
                $staff->avatar             = $user->avatar;
                $staff->mobile             = $mobile;
                $staff->name               = $name;
                $staff->name_match         = String::strToUnicode($name.$mobile);
                $staff->address            = $address;
                $staff->status             = 1;
                $staff->create_time        = UTC_TIME;
                $staff->create_day         = UTC_DAY;
                $staff->save();
         
                //保存员工扩展信息
                if(SellerStaffExtend::where("staff_id", $staff->id)->where("seller_id", $seller->id)->first() == false)
                {
                    $sellerStaffExtend = new SellerStaffExtend();
                    $sellerStaffExtend->staff_id = $staff->id;
                    $sellerStaffExtend->seller_id = $seller->id;;
                    $sellerStaffExtend->save();
                }

            //}
               // var_dump($cateIds);
            SellerCateRelated::where('seller_id', $seller->id)->delete();
            $cateIds = is_array($cateIds) ? $cateIds : explode(',', $cateIds);
            foreach ($cateIds as $key => $value) 
            {
                $cate = new SellerCateRelated();
                $cate->seller_id = $seller->id;
                $cate->cate_id = $value;
                $cate->save();
            }

            DB::commit();
            $mapPoint = '0,0';
            $mapPos["pos"] = '0 0,10 0,10 10,0 10,0 0';
            
            if(SellerMap::where("seller_id", $seller->id)->first() == false)
            {
                $sellerMap = new SellerMap();
                $sellerMap->seller_id       = $seller->id;
                $sellerMap->map_point       = DB::raw("GeomFromText('POINT(" . str_replace(',', ' ', $mapPoint) . ")')");
                $sellerMap->map_pos         = DB::raw("GeomFromText('Polygon((" . $mapPos["pos"] . "))')");
                $sellerMap->save();
            }
            
            $result['data'] = $seller;
        } 
        catch (Exception $e) 
        {
            DB::rollback();
            $result['code'] = 30018;
        }
        return $result;
    }

    
}
