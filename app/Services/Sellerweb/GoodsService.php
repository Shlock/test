<?php 
namespace YiZan\Services\Sellerweb;

// use YiZan\Models\System\Goods;
use YiZan\Models\Sellerweb\Goods;
use YiZan\Models\SystemGoods;
use YiZan\Models\GoodsExtend;
use YiZan\Models\GoodsSeller;
use YiZan\Models\GoodsNorms;
use YiZan\Models\GoodsTag;
use YiZan\Models\GoodsTagRelated;
use YiZan\Utils\String;
use YiZan\Models\Seller;
use DB, Validator, Lang;

class GoodsService extends \YiZan\Services\GoodsService
{
    /**
     * 服务搜索
     * @param  [type] $mobileName 手机或者名称
     * @return [type]             [description]
     */
    public static function searchGoods($name, $sellerId) {
        $list = Goods::select('id', 'name');
        if ($sellerId > 0) {
            $list->where('seller_id', $sellerId);
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
     * 服务/商品列表
     * @param  string $name 服务名称
     * @param  string $sellerName 服务人员
     * @param  int $cateId 分类编号
     * @param  array $status 状态
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          服务信息
	 */
	public static function getSystemList($sellerId, $type, $name, $cateId, $status, $page, $pageSize) {
		// DB::connection()->enableQueryLog(); 
        $list = Goods::where('goods.type', $type)
                     ->where('goods.seller_id', $sellerId)
                     ->select('goods.*')
                     ->with('cate'); 

        if (!empty($name)) {
          $list->where('goods.name', 'like', '%'.$name.'%');
        }
    		if ($cateId == true) {
    			$list->where('goods.cate_id', $cateId);
    		} 

        if($status > 0) {
            $list->where('goods.status', $status - 1);
        }

    		$totalCount = $list->count();
            
    		$list = $list->skip(($page - 1) * $pageSize)
                     ->take($pageSize)
                     ->get()
                     ->toArray();
        
        // print_r(DB::getQueryLog());exit;
        return ["list"=>$list, "totalCount"=>$totalCount];
	}
    /**
     * 添加服务/商品
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
    public static function systemCreate($weight,$sellerId, $sellerType, $type, $staffIds, $name, $price, 
        $cateId, $brief, $images, $duration, $unit, $stock, $buyLimit, $norms, $status, $sort = 100, $deductType, $deductVal = 0) {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);
        
        if($sellerType != \YiZan\Models\Seller::SELF_ORGANIZATION && $type == Goods::SELLER_SERVICE)
        {
            if(is_array($staffIds) == false || count($staffIds) == 0) {
                $result['code'] = 30201;	// 请选择服务人员
                return $result;
            }
        }

        $rules = array(
            'name'         => ['required'],
            'cateId'       => ['min:1'],
            'price'        => ['required'],
            'brief'        => ['required'],
            'images'       => ['required']
        );

        $messages = array (
            'name.required'     => 50224,   // 商品名称不能为空
            'cateId.min'        => 60011,   // 请选择服务分类
            'price.required'    => 30203,   // 请设置正确的价格
            'brief.required'    => 50226,   // 商品描述不能为空
            'images.required'   => 50225    // 商品图片不能为空
        );

        $validator = Validator::make(
            [
                'name'      => $name,
                'cateId'    => $cateId,
                'price'     => $price,
                'brief'     => $brief,
                'images'    => is_array($images) ? implode(',', $images) : ""
            ], $rules, $messages);

        //验证信息
        if ($validator->fails()){
            $messages = $validator->messages();

            $result['code'] = $messages->first();

            return $result;
        }

        $price = sprintf("%.2f", $price);
        if($price < 0.01){
            $result['code'] = 30203;    // 请设置正确的价格
            return $result;
        }
        //开始事务
        DB::beginTransaction();
        $dbPrefix = DB::getTablePrefix();   
        try{

            $goods = new Goods();
            
            $goods->seller_id       = $sellerId;  
            $goods->type            = $type; 
            $goods->name            = $name; 
            $goods->price           = $price; 
            $goods->weight          = $weight;
            $goods->cate_id         = $cateId;
            $goods->brief           = $brief; 

            $newImages = [];                
            foreach ($images as $image) {
                if (!empty($image)) {
                    $image = self::moveGoodsImage($goods->seller_id, $goods->id, $image);
                    //转移图片失败
                    if (!$image) {
                        $result['code'] = 30213;
                        
                        return $result;
                    }                    
                    $newImages[] = $image;
                }
            }
            $goods->images          = count($newImages) ? implode(',', $newImages) : "";
            if ($type == Goods::SELLER_GOODS) {
                $goods->stock           = $stock;
                $goods->buy_limit       = $buyLimit;
            } else {
                $goods->duration        = $duration;
                $goods->unit            = $unit;
                $goods->deduct_type     = $deductType;
                $goods->deduct_val      = $deductVal;
            }
            $goods->status          = $status;
            $goods->sort            = $sort;
            $goods->create_time     = UTC_TIME; 
            //添加商品
            $goods->save(); 
            $id = $goods->id;
            //添加商品扩展信息
            $goodsExtend = new GoodsExtend();
            $goodsExtend->seller_id = $sellerId;
            $goodsExtend->goods_id  = $id;
            $goodsExtend->save();

            if($type == Goods::SELLER_GOODS) {
                //添加商品规格信息
                $norms = json_decode($norms, true);
                foreach ($norms['names'] as $key => $value) {
                    $goods_norms = new GoodsNorms();
                    $goods_norms->seller_id     = $sellerId;
                    $goods_norms->goods_id      = $goods->id;
                    $goods_norms->name          = $value;
                    $goods_norms->price         = $norms['prices'][$key];
                    $goods_norms->weight         = $norms['weights'][$key];
                    $goods_norms->stock         = $norms['stocks'][$key];
                    $goods_norms->save(); 
                }
            } else {
                if(is_array($staffIds) == true && count($staffIds) >= 1) {
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
                } else {
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
                            WHERE seller_id = {$sellerId}";
                    
                    DB::unprepared($sql);
                } 
            }    
            DB::commit();
        } catch(Exception $e){
            DB::rollback();
            $result['code'] = 30217; 
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
    public static function systemUpdate($weight,$id, $sellerId, $sellerType, $type, $staffIds, $name, $price, 
        $cateId, $brief, $images, $duration, $unit, $stock, $buyLimit, $norms, $status, $sort = 100, $deductType, $deductVal = 0) {
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
                    'price'        => ['required'],
                    'cateId'       => ['min:1'],
                    'brief'        => ['required'],
                    'images'       => ['required']
                );
                
                $messages = array ( 
                    'name.required'     => 50224,   // 商品名称不能为空
                    'price.required'    => 30203,   // 商品价格不能为空
                    'cateId.min'        => 50228,   // 商品价格不能错误
                    'brief.required'    => 50226,   // 商品描述不能为空
                    'images.required'   => 50225    // 商品图片不能为空
                );

                $validator = Validator::make(
                    [ 
                        'name'      => $name,
                        'price'     => $price,
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

                $price = sprintf("%.2f", $price);
                if($price < 0.01){
                    $result['code'] = 30203;    // 请设置正确的价格
                    return $result;
                }

                if(!$cateId){
                    $result['code'] = 60011; //请选择分类
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
                       'weight'          => $weight,
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
                    $update_info['duration']        = $duration;
                    $update_info['unit']            = $unit; 
                    $update_info['deduct_type']     = $deductType;
                    $update_info['deduct_val']      = $deductVal;
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
                } else {
                    GoodsNorms::where('seller_id', $sellerId)
                              ->where('goods_id', $id)
                              ->delete();  
                }
                foreach ($norms['names'] as $key => $value) {          
                    $norms_item = GoodsNorms::where('seller_id', $sellerId)
                                            ->where('goods_id', $id)
                                            ->where('id', $norms['ids'][$key])
                                            ->first();
                    if($norms_item){
                        $norms_item->name           = $value;
                        $norms_item->price          = $norms['prices'][$key];
                        $goods_norms->weight         = $norms['weights'][$key];
                        $norms_item->stock          = $norms['stocks'][$key]; 
                        $norms_item->save();
                    } else {
                        $goods_norms = new GoodsNorms();
                        $goods_norms->seller_id     = $sellerId;
                        $goods_norms->goods_id      = $goods->id;
                        $goods_norms->name          = $value;
                        $goods_norms->price         = $norms['prices'][$key];
                        $goods_norms->weight         = $norms['weights'][$key];
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
     * 获取服务
     * @param int $sellerId 服务人员编号
     * @param  int $id 服务id
     * @return object   服务
     */
	public static function getSystemGoodsById($sellerId, $id) {
		$goods = Goods::where('goods.id', $id)
            ->select('goods.*','goods_seller.sale_status','goods_seller.call_price','goods_seller.deduct_type','goods_seller.deduct_value')
            ->join('goods_seller', function($join) use($sellerId) {
                $join->on('goods_seller.goods_id', '=', 'goods.id')
                    ->where('goods_seller.seller_id', '=', $sellerId);
            })
            ->with('cate')
		    ->first();
        
        if($goods == true) {
            $goods->staff_ids = DB::table("goods_staff")
                ->select("seller_staff.id", "seller_staff.name")
                ->join("seller_staff", "seller_staff.id", "=", "goods_staff.staff_id")
                ->where("goods_staff.goods_id", $id)
                ->get();
            $goodsTags = DB::table("goods_tag_related")
                ->select("goods_tag.name")
                ->join("goods_tag","goods_tag.id","=","goods_tag_related.tag_id")
                ->where("goods_tag_related.goods_id", $id)
                ->get();
            $tagArr = [];
            foreach ($goodsTags as $value) { 
                $tagArr[] = $value->name;
            } 
            $goods->goods_tags = implode(',', $tagArr); 
        } 
        return $goods;
	}
    
    /**
     * 删除服务
     * @param int  $id 服务id
     * @return array   删除结果
     */
	public static function deleteSystem($sellerId,$id) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		];

        DB::beginTransaction();
        try{
            Goods::where('id', $id)->where('seller_id',$sellerId)->delete();

            //删除服务属性
            \YiZan\Models\GoodsAttr::where('goods_id', $id)->where('seller_id',$sellerId)->delete();
            //删除服务举报
            \YiZan\Models\GoodsComplain::where('goods_id', $id)->where('seller_id',$sellerId)->delete();
            //删除服务扩展
            \YiZan\Models\GoodsExtend::where('goods_id', $id)->where('seller_id',$sellerId)->delete();
            //删除服务规格
            \YiZan\Models\GoodsModel::where('goods_id', $id)->where('seller_id',$sellerId)->delete();
            //删除服务标签
            \YiZan\Models\GoodsTagRelated::where('goods_id', $id)
                                        ->where('seller_id',$sellerId)
                                        ->where('type', 0)
                                        ->delete();
            //删除提供服务的员工
            \YiZan\Models\GoodsStaff::where('goods_id', $id)->where('seller_id',$sellerId)->delete();

            self::updateSellerExtend($goods->seller_id);
            self::removeGoodsImage($goods->seller_id, $goods->id);
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
		return $result;
	}

    /**
     * [getGoodsById 获取菜品详细]
     * @param  [type] $sellerId     [服务站编号]
     * @param  [type] $id           [菜品编号]
     * @return [type]               [description]
     */
    public static function getGoodsById($sellerId, $id) { 
        $goods = Goods::where('id', $id)
                    ->where('seller_id', $sellerId) 
                    ->with(['seller', 'cate', 'norms'=>function($query) use($sellerId) {
                                    $query->where('seller_id', $sellerId) ;
                                }])
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
     * 菜单审核列表
     * @param [type]  $name     [美食名称]
     * @param [type]  $status   [状态]
     * @param [type]  $page     [页码]
     * @param integer $pageSize [分页参数]
     * @return [array]             [返回数组]
     */
    public static function GoodsAuditLists($sellerId, $name, $disposeStatus, $page, $pageSize = 20) 
    {
        $list = Goods::where('seller_id', $sellerId)->where('type',1)->orderBy('id', 'desc');
        if(!empty($name)) {
            $list->where('name', 'like', '%'.$name.'%');
        }
        if(!empty($disposeStatus) > 0) {
            $list->where('dispose_status', $disposeStatus - 2 );
        }

        $totalCount = $list->count();
        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->with('type', 'restaurant')
            ->get()
            ->toArray();
        return ["list"=>$list, "totalCount"=>$totalCount];   
    }

    /**
     * [add 添加编辑菜品]
     * @param [type] $id           [菜品编号]
     * @param [type] $sellerId     [商家编号]
     * @param [type] $typeId       [分类编号]
     * @param [type] $restaurantId [餐厅编号]
     * @param [type] $name         [菜品名称]
     * @param [type] $images       [菜品图片]
     * @param [type] $joinService  [参与服务]
     * @param [type] $price        [现价]
     * @param [type] $oldPrice     [原价]
     * @param [type] $status       [状态， 默认为下架状态]
     * @param [type] $sort         [排序]
     */
    public static function save($id, $sellerId, $typeId, $restaurantId, $name, $images, $joinService, $price, $oldPrice, $status, $sort) {
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'sellerId'        => ['min:1'],
            'typeId'          => ['min:1'],
            'restaurantId'    => ['min:1'],
            'name'            => ['required'],
            'images'          => ['required'],
            'joinService'     => ['digits_between:0,4'],
            'price'           => ['numeric','min:0'],
            'oldPrice'        => ['numeric','min:0'],
            'status'          => ['numeric','required'],
            'sort'            => ['numeric','required']
        );
        
        $messages = array
        (
            'sellerId.min'          => 60010,   // 服务站参数错误
            'typeId.required'       => 60011,   // 请选择分类
            'restaurantId.min'      => 60012,   // 餐厅参数错误
            'name.required'         => 60013,   // 菜品名称不能为空
            'images.required'       => 60014,    // 请上传菜品图片
            'joinService.digits_between'   => 60015,    // 请选择参与服务
            'price.numeric'         => 60016,    // 现价必须为数字
            'price.min'             => 60017,    // 现价必须大于0
            'oldPrice.numeric'      => 60018,    // 原价必须为数字
            'oldPrice.min'          => 60019,    // 原价必须大于0
            'status.numeric'        => 60020,    // 状态错误
            'status.required'       => 60021,    // 状态错误
            'sort.numeric'          => 60022,    // 排序必须为数字
            'sort.required'         => 60023,    // 排序不能为空
        );

        $validator = Validator::make(
            [
                'sellerId'      => $sellerId,
                'typeId'        => $typeId,
                'restaurantId'      => $restaurantId,
                'name'      => $name,
                'images'        => $images,
                'joinService'       => $joinService,
                'price'     => $price,
                'oldPrice'      => $oldPrice,
                'status'        => $status,
                'sort'      => $sort,
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails()) 
        {
            $messages = $validator->messages();
            
            $result['code'] = $messages->first();
            
            return $result;
        }

        if($id > 0) {
            $goods = Goods::where('id', $id)->where('seller_id', $sellerId)->first();
            //处于上架状态不能编辑
            if($goods->status == 1) {
                $result['code'] = 60024;
                $result['msg']  = Lang::get('api_sellerweb.code.60024');
                return $result;
            }
            //处于审核状态 不能编辑
            if($goods->dispose_status == 0) {
                $result['code'] = 60025;
                $result['msg']  = Lang::get('api_sellerweb.code.60025');
                return $result;
            }
        }else{
            $goods = new Goods();
        }

        $goods->seller_id  = $sellerId;
        $goods->type_id  = $typeId;
        $goods->restaurant_id  = $restaurantId;
        $goods->name  = $name;
        $goods->images  = $images;
        $goods->join_service  = $joinService;
        $goods->price  = $price > 0 ? $price : 0;
        $goods->old_price  = $oldPrice > 0 ? $oldPrice : 0;
        $goods->status  = 0;          //不论是添加还是编辑 均为下架状态
        $goods->dispose_status  = 0;  //不论是添加还是编辑 均需要后台审核
        $goods->sort  = $sort >= 0 ? $sort : 0;
        $goods->create_time = UTC_TIME;

        $blu = $goods->save();
        if($blu) {
            $checkExtend = GoodsExtend::where('goods_id', $goods->id)->first();
            if (!$checkExtend) {
                GoodsExtend::insert([
                    'goods_id' => $goods->id,
                    'seller_id' => $goods->seller_id,
                    'restaurant_id' => $goods->restaurant_id
                ]);
            }
            $result['msg'] = Lang::get('api_sellerweb.success.success');
        }else{
            $result['msg'] = Lang::get('api_sellerweb.code.99900');
        }
        return $result;
    }

    /**
     * 删除菜品
     * @param int  $id 服务id
     * @return array   删除结果
     */
    public static function deleteGoods($sellerId,$id) 
    {
        $result = 
        [
            'code'  => 0,
            'data'  => null,
            'msg'   => Lang::get('api_sellerweb.success.delete')
        ];

        DB::beginTransaction();
        try{
            Goods::where('id', $id)->where('seller_id',$sellerId)->delete();
            GoodsExtend::where('goods_id', $id)->where('seller_id', $sellerId)->delete();
            self::removeGoodsImage($sellerId, $id);
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    }

}
