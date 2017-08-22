<?php 
namespace YiZan\Services\System;

use YiZan\Models\System\Goods;
use YiZan\Models\System\GoodsExtend;
use YiZan\Utils\String;
use YiZan\Models\Restaurant;
use Illuminate\Database\Query\Expression;
use DB, Validator;

class GoodsService extends \YiZan\Services\GoodsService {
    /**
     * 菜品搜索
     * @param  [type] $mobileName 手机或者名称
     * @return [type]             [description]
     */
    public static function searchGoods($name, $sellerId) {
        $list = Goods::select('id', 'name')
                     ->where('is_del', 0);

        if ($sellerId > 0) {
            $list->where('seller_id', $sellerId);
        } else {
            $list->where('seller_id', '>', 0);
        }

        $match = empty($name) ? '' : String::strToUnicode($name,'+');
        if (!empty($match)) {
            $list->selectRaw("IF(name = '{$name}',1,0) AS eq,
                        MATCH(name_match) AGAINST('{$match}') AS similarity")
                 ->whereRaw('MATCH(name_match) AGAINST(\'' . $match . '\' IN BOOLEAN MODE)')
                 ->orderBy('eq', 'desc')
                 ->orderBy('similarity', 'desc');
        }
        return $list->orderBy('id', 'desc')->skip(0)->take(30)->get()->toArray();
    }

	/**
     * 菜品列表
     * @param  int $sellerId 商家编号
     * @param  int $type Goods类型
     * @param  string goods名称
     * @param  int $cateId 分类编号
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          菜品信息
	 */
	public static function getSystemList($sellerId, $type, $name, $cateId, $page, $pageSize)
    {
        // DB::connection()->enableQueryLog(); 
        $list = Goods::where('type', $type) ; 
        if($sellerId > 0){
            $list->where('seller_id', $sellerId);
        }
        if ($cateId == true) {
            $list->where('goods.cate_id', $cateId);
        } 

        if ($name == true) {
            $list->where('goods.name', 'like', '%'.$name.'%');
        } 

        if($status > 0) {
            $list->where('status', $status - 1);
        }

        $totalCount = $list->count();
        
        $list = $list->skip(($page - 1) * $pageSize)
                     ->take($pageSize)
                     ->select('goods.*')
                     ->with('cate','seller')
                     ->get()
                     ->toArray();
        
        // print_r(DB::getQueryLog());exit;
        return ["list"=>$list, "totalCount"=>$totalCount];
	}


    /**
     * 服务列表
     * @param  int $type 服务类型  2:跑腿 3:家政 4:汽车 5:其他
     * @param  string $name 服务名称
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          菜品信息
     */
    public static function getServiceList($type, $name, $page, $pageSize)
    {

        $list = Goods::orderBy('id','desc');
        if ($type > 1) {
            $list->where('type', $type);
        } else {
            $list->where('type','>', 1);
        }

        if ($name != '') {
            $list->where('name','like','%'.$name.'%');

        }
        $totalCount = $list->count();

        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();

        return ["list"=>$list, "totalCount"=>$totalCount];
    }

    /**
     * 更改菜品状态
     * @param int $id;  菜品编号
     * @return [type] [description]
     */
    public static function auditGoods($id, $status, $isSystem, $disposeResult, $cityPrices, $adminId){  
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];

        $status = $status == 1 ? Goods::STATUS_ENABLED : Goods::STATUS_NOT_BY;
        if ($status != 1 && empty($disposeResult)) {
            $result['code'] = 30910;
            return $result;
        }

        $goods = Goods::find($id);
        if (!$goods) {
            $result['code'] = 30214;
            return $result;
        }

        //当状态为待审核时
        if($goods->status == Goods::STATUS_AUDITING){
            DB::beginTransaction();
            try {
                $goods->status              = $status;
                $goods->dispose_result      = $disposeResult;
                $goods->dispose_time        = UTC_TIME;
                $goods->dispose_admin_id    = $adminId;
                $goods->update_time         = UTC_TIME;
                if ($isSystem == 1) {
                    $goods->seller_id = 0;
                }
                $goods->save();
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                $result['code'] = 99999;
            }
        }
        return $result;
    }

    public static function updateSellerExtend($sellerId) {
        $goodsStatistics = Goods::where('seller_id', $sellerId)
                                ->selectRaw('COUNT(id) AS gcount, SUM(price) as tprice')
                                ->first();

        $sellerExtend = SellerExtend::where('seller_id', $sellerId)->first();
        if(!$sellerExtend) {
            $sellerExtend = new SellerExtend;
            $sellerExtend->seller_id = $sellerId;
        }
        
        $sellerExtend->goods_count = $goodsStatistics->gcount;
        $sellerExtend->goods_total_price = $goodsStatistics->tprice;
        $sellerExtend->goods_avg_price = $goodsStatistics->gcount == 0 ? 0 : $goodsStatistics->tprice / $goodsStatistics->gcount;
        $sellerExtend->save();
    }

    /**
     * 获取菜品
     * @param  int $id 菜品id
     * @return array   菜品
     */
	public static function getSystemGoodsById($id) {
        $goods = Goods::where('goods.id', $id)
                      ->select('goods.*') 
                      ->with('cate')
                      ->first();
        
        if($goods == true) {
            $goods->staff_ids = DB::table("goods_staff")
                ->select("seller_staff.id", "seller_staff.name")
                ->join("seller_staff", "seller_staff.id", "=", "goods_staff.staff_id")
                ->where("goods_staff.goods_id", $id)
                ->get();  
        } 
        return $goods; 
	}

    /**
     * 删除菜品
     * @param int  $id 菜品id
     * @return array   删除结果
     */
	public static function deleteSystem($id) {
		$result = [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		];

        $goods = Goods::find($id);
        if (!$goods || $goods->seller_id < 1) {
            $result['code'] = 30214;
        }

        DB::beginTransaction();
        
        try
        {
            Goods::where('id', $id)->delete();

            //删除菜品扩展
            \YiZan\Models\GoodsExtend::where('goods_id', $id)->delete();
            
            DB::commit();
        } 
        catch (Exception $e) 
        {
            DB::rollback();
            $result['code'] = 99999;
        }
		return $result;
	}

    /**
     * 保存菜品信息
     * @param $id
     * @param $restaurantId
     * @param $typeId
     * @param $name
     * @param $image
     * @param $joinService
     * @param $price
     * @param $oldPrice
     * @param $status
     * @param $sort
     */
    public static function save($id, $restaurantId, $typeId, $name, $image, $joinService, $price, $oldPrice, $status, $sort) {
        $result = [
            'code' => 0,
            'msg' => '',
            'data' => null
        ];
        if ($id > 0) {
            $check = Goods::where('id', $id)->where('type', 1)->first();
            if (!$check) {
                $result['code'] = 50406;
                return $result;
            }
        }
        $checkRes = Restaurant::where('id', $restaurantId)->first();
        if (!$checkRes) {
            $result['code'] = 50409;
            return  $result;
        }
        $rules = array(
            'restaurantId'  => ['min:1'],
            'typeId'        => ['min:1'],
            'name'          => ['required'],
            'sort'          => ['min:1']
        );

        $messages = array
        (
            'restaurantId.min'  => 50401,	// 请输入标题
            'typeId.min'	    => 50402,	// 请选择分类
            'name.required'	    => 50403,	// 请输入详细
            'sort.min'	        => 50404	// 请输入详细
        );

        $validator = Validator::make(
            [
                'restaurantId'  => $restaurantId,
                'typeId'        => $typeId,
                'name'          => $name,
                'sort'          => $sort
            ], $rules, $messages);

        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();

            $result['code'] = $messages->first();

            return $result;
        }

        if ((float)$oldPrice < 0.0001 || (float)$price < 0.0001) {
            $result['code'] = 50405;
            return $result;
        }

        $data = [
            'restaurant_id' => $restaurantId,
            'type_id' => $typeId,
            'name' => $name,
            'images' => $image,
            'join_service' => $joinService,
            'price' => $price,
            'old_price' => $oldPrice,
            'status' => $status,
            'sort' => $sort
        ];
        if ($id > 0) {
            if ($check->image != $image){
                self::moveGoodsImage($check->seller_id, $id, $image);
                self::removeGoodsImage($check->seller_id, $id);
            }
            Goods::where('id', $id)->update($data);
        } else {
            $data['seller_id'] = $checkRes->seller_id;
            $data['create_time'] = UTC_TIME;
            $data['dispose_time'] = UTC_TIME;
            $data['dispose_result'] = '后台管理员添加';
            $data['dispose_status'] = 1;
            $id = Goods::insertGetId($data);
        }
        $checkExtend = GoodsExtend::where('goods_id', $id)->first();
        if (!$checkExtend) {
            GoodsExtend::insert([
                'goods_id' => $id,
                'seller_id' => $checkRes->seller_id,
                'restaurant_id' => $restaurantId
            ]);
        }
        return $result;

    }

    /**
     * 菜品审核
     * @param int $id 菜品编号
     * @param int $status 处理状态
     * @param strint $remark 处理备注
     */
    public static function dispose($id, $status, $remark) {
        $result = [
            'code' => 0,
            'msg' => '',
            'data' => null
        ];
        $check = Goods::where('id', $id)->first();
        if (!$check) {
            $result['code'] = 50406;
            return $result;
        }
        if ($check->dispose_status != 0) {
            $result['code'] = 50407;
            return $result;
        }
        Goods::where('id', $id)->update([
            'dispose_status' => $status,
            'dispose_result' => $remark,
            'dispose_time' => UTC_TIME
        ]);
        return $result;
    }

    /**
     * 保存服务数据
     * @param int $id 服务编号
     * @param int $type 服务所属栏目
     * @param string $name 服务名称
     * @param string $image 服务图片
     * @param string $brief 服务简介
     * @param int $sort 排序
     */
    public static function saveService($id, $type, $name, $image, $brief, $sort) {
        $result = [
            'code' => 0,
            'msg' => '',
            'data' => null
        ];
        if ($id < 1) {
            $types = ['4', '5'];
            if (!in_array($type, $types)) {
                $result['code'] = 50501;
                return $result;
            }
        } else{
            $check = Goods::where('id', $id)->first();
            if (!$check) {
                $result['code'] = 40002;
                return $result;
            }
            if (($type == 2 && $id != 1) || ($type == 3 && $id != 2)) {
                $result['code'] = 50502;
                return $result;
            }
        }

        $rules = array(
            'name'          => ['required'],
            'sort'          => ['min:1']
        );

        $messages = array
        (
            'name.required'	    => 50403,	// 名称必须
            'sort.min'	        => 50404	// 排序
        );

        $validator = Validator::make(
            [
                'name'          => $name,
                'sort'          => $sort
            ], $rules, $messages);

        //验证信息
        if ($validator->fails())
        {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }
        if ($id < 1) {//创建服务
            $id = Goods::insertGetId([
                'type_id' => 0,
                'seller_id' => 0,
                'restaurant_id' => 0,
                'price' => '0.00',
                'old_price' => '0.00',
                'join_service' => 0,
                'type' => $type,
                'name' => $name,
                'images' => $image,
                'brief' => $brief,
                'sort' => $sort,
                'status' => 1,
                'create_time' => UTC_TIME
            ]);
        } else {
            if ($check->image != $image){
                self::moveGoodsImage($check->seller_id, $id, $image);
                self::removeGoodsImage($check->seller_id, $id);
            }
            Goods::where('id', $id)->update([
                'type' => $type,
                'name' => $name,
                'images' => $image,
                'brief' => $brief,
                'sort' => $sort,
            ]);
        }

        $checkExtend = GoodsExtend::where('goods_id', $id)->first();
        if (!$checkExtend) {
            GoodsExtend::insert([
                'goods_id' => $id,
                'seller_id' => 0,
                'restaurant_id' => 0
            ]);
        }

        return $result;
    }
/**
     * 更新服务/商品
     * @param int $id       服务/商品编号
     * @param int $sellerId 服务人员编号
     * @param int $sellerType 服务商类型 
     * @param int $type 类型 
     * @param array $staffIds 可以提供此服务的员工
     * @param string $name 服务名称
     * @param double $price 价格 
     * @param int $cateId 分类编号
     * @param string $brief 简介
     * @param array $images 图片数组
     * @param int $duration 时长（秒）
     * @param int $unit 计时单位
     * @param int $stock 库存
     * @param int $buyLimit 每人限制
     * @param string $norms 规格
     * @param int $sort 排序 
     * @return array   创建结果
     */
    public static function systemUpdate($id, $sellerId, $sellerType, $type, $staffIds, $name, $price, 
        $cateId, $brief, $images, $duration, $unit, $stock, $buyLimit, $norms, $status, $sort = 100) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );
        
        if($sellerType != \YiZan\Models\Seller::SELF_ORGANIZATION && $type == Goods::SELLER_SERVICE)
        {
            if(is_array($staffIds) == false || 
                count($staffIds) == 0)
            {
                $result['code'] = 30201;    // 请选择服务人员
                
                return $result;
            }
        }  

        $goods = Goods::find($id);

        if($goods == false) {
            $result['code'] = 30215;    // 服务不存在
            
            return $result;
        }

        //开始事务
        DB::beginTransaction();  
        try{ 
            if($goods->seller_id != 0){  
                //验证服务信息
                $rules = array( 
                    'name'         => ['required'],
                    //'price'        => ['min:1'],
                    'cateId'       => ['min:1'],
                    'brief'        => ['required'],
                    'images'       => ['required']
                );
                
                $messages = array ( 
                    'name.required'     => 30202,   // 名称不能为空
                    //'price.min'           => 30204,   // 请设置正确的门店价格
                    'cateId.min'        => 30206,   // 请选择服务分类
                    'brief.required'    => 30207,   // 简介不能为空
                    'images.required'   => 30208    // 请上传服务图片
                );

                $validator = Validator::make(
                    [ 
                        'name'      => $name,
                        //'price'     => $price,
                        'cateId'    => $cateId,
                        'brief'     => $brief,
                        'images'    => is_array($images) ? implode(',', $images) : ""
                    ], $rules, $messages);
                
                //验证信息
                if ($validator->fails()){
                    $messages = $validator->messages();
                    
                    $result['code'] = $messages->first();
                    
                    return $result;
                } 
                
                $newImages = [];
                
                $oldImages = $goods->images;
                
                foreach ($images as $image)  {
                    if (!empty($image))  { 
                        if (false !== ($key = array_search($image, $oldImages)))  { 
                            unset($oldImages[$key]);
                            
                            $newImages[] = $image; 
                        } else{
                            $image = self::moveGoodsImage($goods->seller_id, $goods->id, $image); 
                            //转移图片失败
                            if (!$image) 
                            {
                                $result['code'] = 30213;
                                return $result;
                            }
                            
                            $newImages[] = $image;
                        }
                    }
                }
                $update_info = [  
                       'name'            => $name, 
                       'price'           => $price, 
                       'cate_id'         => $cateId,
                       'brief'           => $brief, 
                       'images'          => implode(',', $newImages), 
                       'status'          => $status,
                       'sort'            => $sort
                   ];
                if ($type == Goods::SELLER_GOODS) {
                    $update_info['stock']       = $stock;
                    $update_info['buy_limit']   = $buyLimit; 
                } else {
                    $update_info['duration']    = $duration;
                    $update_info['unit']        = $unit; 
                }
                Goods::where('id', $id)
                     ->where('seller_id',$sellerId)
                     ->update($update_info); 
                     
            }
            //如果是商品则编辑规格
            if($type == Goods::SELLER_GOODS) {
                //添加商品规格信息
                $norms = json_decode($norms, true); 
                //删除不在编辑列表中的规格
                if(!empty($norms['ids'])){ 
                    GoodsNorms::whereNotIn('id',$norms['ids'])
                              ->where('seller_id', $sellerId)
                              ->where('goods_id', $id)
                              ->delete();  
                }
                foreach ($norms['names'] as $key => $value) {          
                    $norms_item = GoodsNorms::where('seller_id', $sellerId)
                                            ->where('goods_id', $id)
                                            ->where('name', $value)
                                            ->first();
                    if($norms_item){
                        $norms_item->price = $norms['prices'][$key];
                        $norms_item->stock = $norms['stocks'][$key]; 
                        $norms_item->save();
                    } else {
                        $goods_norms = new GoodsNorms();
                        $goods_norms->seller_id     = $sellerId;
                        $goods_norms->goods_id      = $goods->id;
                        $goods_norms->name          = $value;
                        $goods_norms->price         = $norms['prices'][$key];
                        $goods_norms->stock         = $norms['stocks'][$key];
                        $goods_norms->save();
                    }
                }
            } else {
                //修改服务的服务员工信息
                $dbPrefix = DB::getTablePrefix();
                
                if($sellerType != \YiZan\Models\Seller::SELF_ORGANIZATION) {

                    $sql = "DELETE FROM {$dbPrefix}goods_staff WHERE goods_id = {$id} AND seller_id = {$sellerId}";
                    
                    DB::unprepared($sql);
                    
                    if(is_array($staffIds) == true && count($staffIds) >= 1){
                        $staffIds = implode(",", $staffIds);
                        
                        self::replaceIn($staffIds);
                        
                        $sql = "
                            INSERT INTO {$dbPrefix}goods_staff
                            (
                                staff_id,
                                goods_id, 
                                seller_id
                            )
                            SELECT  id,
                                    {$id}, 
                                    {$sellerId}
                                FROM {$dbPrefix}seller_staff 
                                WHERE id IN ({$staffIds})";
                        
                        DB::unprepared($sql);
                    }
                }
            } 
             
            DB::commit();
        } catch(Exception $e) {
            DB::rollback();
            $result['code'] = 30217; 
        }
        return $result;
    }  

    /**
     * 删除菜品
     * @param array  $ids 菜品id
     * @return array   删除结果
     */
    public static function deleteService($id) {
        $result = [
            'code'	=> 0,
            'data'	=> null,
            'msg'	=> ""
        ]; 
        Goods::where('id', $id)->delete();
        return $result;
    }


}
