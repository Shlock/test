<?php 
namespace YiZan\Services\Staff;

//use YiZan\Models\System\Goods;
use YiZan\Models\Sellerweb\Goods;
use YiZan\Models\SystemGoods;
use YiZan\Models\GoodsExtend;
use YiZan\Models\GoodsSeller;
use YiZan\Models\GoodsNorms;
use YiZan\Models\GoodsCate;
use YiZan\Models\SellerStaff;
use YiZan\Models\GoodsTagRelated;
use YiZan\Utils\String;
use YiZan\Models\Seller;
use DB, Validator, Lang;

class GoodsService extends \YiZan\Services\GoodsService
{

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
    public static function getLists($sellerId, $page, $cateId, $status = 1, $keywords) {
      //  DB::connection()->enableQueryLog(); 
        $list = Goods::where('cate_id', $cateId)
                     ->where('seller_id', $sellerId)
                     ->select('goods.*')
                     ->with('cate', 'extend','norms', 'goodsStaff.staffers'); 

        if ($status > 1) {
            $list->where('status', 0);
        } else {
            $list->where('status', $status);
        }
        if (!empty($keywords)) {
           $list->where('name', 'like', '%'.$keywords.'%');
        }

        $list = $list->skip(($page - 1) * 20)
                     ->take(20)
                     ->get()
                     ->toArray();
 //print_r(DB::getQueryLog());exit;
        foreach ($list as $key => $value) {
            $list[$key]['saleCount'] = $value['extend']['salesVolume'];
            $list[$key]['date'] = yzday($value['createTime']);
            $list[$key]['imgs'] = $value['images'];
            foreach ($list[$key]['goodsStaff'] as $k => $v) {
                $list[$key]['staff'][] = $v['staffers'];
            }
           
           // $list[$key]['staff'] = SellerStaff::whereIn('id', $list[$key]['staffIds'])->get()->toArray();
        }
        // print_r($list);
        // exit;
        
        return $list;
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
    public static function goodsCreate($sellerId, $cateId, $name, $images, $norms, $brief, $price, 
        $duration, $staffIds, $stock, $sort = 100, $type, $sellerType) {
        
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );
        
        if($sellerType != \YiZan\Models\Seller::SELF_ORGANIZATION && $type == \YiZan\Models\Goods::SELLER_SERVICE)
        {
            if(is_array($staffIds) == false || count($staffIds) == 0) {
                $result['code'] = 30201;    // 请选择服务人员
                return $result;
            }
        }

        //开始事务
        DB::beginTransaction();
        $dbPrefix = DB::getTablePrefix();   
        try{  
           
            // $rules = array( 
            //     'name'          => ['required'],
            //     //'price'          => ['min:0'],
            //     //'cateId'        => ['min:1'],
            //     'brief'        => ['required'],
            //     'images'       => ['required']
            // );
            
            // $messages = array
            // ( 
            //     'name.required'     => 30202,   // 名称不能为空
            //     //'price.min'           => 30204,   // 请设置正确的门店价格
            //     //'cateId.min'        => 30206,   // 请选择服务分类
            //     'brief.required'    => 30207,   // 简介不能为空
            //     'images.required'   => 30208    // 请上传服务图片
            // );

            // $validator = Validator::make(
            //     [ 
            //         'name'      => $name,
            //         //'price'     => $price,
            //         'cateId'    => $cateId,
            //         'brief'     => $brief,
            //         'images'    => is_array($images) ? implode(',', $images) : ""
            //     ], $rules, $messages);
            
            // //验证信息
            // if ($validator->fails()) 
            // {
            //     $messages = $validator->messages();
                
            //     $result['code'] = $messages->first();
                
            //     return $result;
            // } 
            
            $goods = new Goods();
            $goods->seller_id       = $sellerId;  
            $goods->type            = $type; 
            $goods->name            = $name; 
            $goods->cate_id         = $cateId;
            $goods->brief           = $brief; 

            $newImages = [];     
            
            if (!empty($images)) {
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
            }  
             
            $goods->images          = count($newImages) ? implode(',', $newImages) : "";
            if ($type == \YiZan\Models\Goods::SELLER_GOODS) {
                $goods->stock           = $norms ? (int)$norms[0]['stock'] : $stock;
                $goods->price           = $norms ? (float)$norms[0]['price'] : $price; 
               // $goods->buy_limit       = $buyLimit;
                // $goods->deduct_type     = $deductType;
                // $goods->deduct_val      = $deductVal;
            } else {
                $goods->price           = $price;
                $goods->duration        = $duration;
                $goods->unit            = 0; //分钟
            }
            $goods->status          = 1; //默认上架
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

            if($type == \YiZan\Models\Goods::SELLER_GOODS) {
                //添加商品规格信息
                //var_dump($norms);
                //$norms = json_decode($norms, true);
                foreach ($norms as $key => $value) {
                    $goods_norms = new GoodsNorms();
                    $goods_norms->seller_id     = $sellerId;
                    $goods_norms->goods_id      = $goods->id;
                    $goods_norms->name          = $value['name'];
                    $goods_norms->price         = $value['price'];
                    $goods_norms->stock         = (int)$value['stock'];
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
    public static function goodsUpdate( $sellerId, $id, $cateId, $name, $images, $norms, $brief, $price, 
        $duration, $staffIds, $stock) {
        //var_dump($sellerId);
        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );
        //print_r($norms);
        $type = GoodsCate::where('id', $cateId)->pluck('type');
        $sellerType = Seller::where('id', $sellerId)->pluck('type');
       //print_r($type);
        if ($id < 1) {
            return self::goodsCreate($sellerId, $cateId, $name, $images, $norms, $brief, $price, $duration, $staffIds, $stock, 100, $type, $sellerType, $deductType, $deductVal);
        } else {
            if($sellerType != \YiZan\Models\Seller::SELF_ORGANIZATION && $type == \YiZan\Models\Goods::SELLER_SERVICE)
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
                    $rules = array( 
                        'name'          => ['required'],
                        'brief'        => ['required'],
                        'imgs'       => ['required']
                    );
                    
                    $messages = array
                    ( 
                        'name.required'     => 30202,   // 名称不能为空
                        'brief.required'    => 30207,   // 简介不能为空
                        'imgs.required'   => 30208    // 请上传服务图片
                    );

                    $validator = Validator::make(
                        [ 
                            'name'      => $name,
                            'cateId'    => $cateId,
                            'brief'     => $brief,
                            'imgs'    => is_array($images) ? implode(',', $images) : ""
                        ], $rules, $messages);
                    
                    //验证信息
                    if ($validator->fails()) 
                    {
                        $messages = $validator->messages();
                        
                        $result['code'] = $messages->first();
                        
                        return $result;
                    } 
                    $newImages = [];
                    $oldImages = $goods->images;
                    if (!empty($images)) {
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
                    }

                    $update_info = [  
                           'name'            => $name, 
                           'cate_id'         => $cateId,
                           'brief'           => $brief, 
                           'images'          => implode(',', $newImages)
                       ];
                    if ($type == Goods::SELLER_GOODS) {
                        $update_info['stock']       = $norms ? (int)$norms[0]['stock'] : $stock;
                        $update_info['price']       = $norms ? (float)$norms[0]['price'] : $price;
                       // $update_info['buy_limit']   = $buyLimit; 
                        // $update_info['deduct_type'] = $deductType;
                        // $update_info['deduct_val']  = $deductVal;
                    } else {
                        $update_info['price']       = $price;
                        $update_info['duration']    = $duration;
                        $update_info['unit']        = 0; 
                    }
                    Goods::where('id', $id)
                         ->where('seller_id',$sellerId)
                         ->update($update_info); 
                         
                }
                //如果是商品则编辑规格
                if($type == \YiZan\Models\Goods::SELLER_GOODS) {
                    //添加商品规格信息
                   // $norms = json_decode($norms, true); 
                    //删除不在编辑列表中的规格
                    foreach ($norms as $key => $value) {
                        $nids[] = $value['id'];
                    }
                    if(!empty($nids)){ 
                        GoodsNorms::whereNotIn('id',$nids)
                                  ->where('seller_id', $sellerId)
                                  ->where('goods_id', $id)
                                  ->delete();  
                    }
                    foreach ($norms as $key => $value) {          
                        $norms_item = GoodsNorms::where('seller_id', $sellerId)
                                                ->where('goods_id', $id)
                                                ->where('id', $value['id'])
                                                ->first();
                        if($norms_item){
                            $norms_item->price = $value['price'];
                            $norms_item->name = $value['name'];
                            $norms_item->stock = (int)$value['stock']; 
                            $norms_item->save();
                        } else {
                            $goods_norms = new GoodsNorms();
                            $goods_norms->seller_id     = $sellerId;
                            $goods_norms->goods_id      = $goods->id;
                            $goods_norms->name          = $value['name'];
                            $goods_norms->price         = $value['price'];
                            $goods_norms->stock         = (int)$value['stock'];
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
        }
 
        return $result;
    }    

    /**
     * 操作服务，上下架，删除
     * @param int  $id 服务id
     * @return array   结果
     */
    public static function opGoods($sellerId, $ids, $type) 
    {
        $result = 
        [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];
        if (!is_array($ids)) {
            $ids = (int)$ids;
            if ($ids < 1) {
                return false;
            }
            $ids = [$ids];
        }

        switch ($type) {
            case 1: //上架
                Goods::whereIn('id', $ids)->where('seller_id',$sellerId)->update(['status'=> 1]);
                break;
            case 2: //下架
                Goods::whereIn('id', $ids)->where('seller_id',$sellerId)->update(['status'=>0]);
                break;
            case 3: //删除
                DB::beginTransaction();
                try{
                    Goods::whereIn('id', $ids)->where('seller_id',$sellerId)->delete();

                    //删除服务属性
                   // \YiZan\Models\GoodsAttr::whereIn('goods_id', $ids)->where('seller_id',$sellerId)->delete();
                    //删除服务举报
                  //  \YiZan\Models\GoodsComplain::whereIn('goods_id', $ids)->where('seller_id',$sellerId)->delete();
                    //删除服务扩展
                    \YiZan\Models\GoodsExtend::whereIn('goods_id', $ids)->where('seller_id',$sellerId)->delete();
                    //删除商品规格
                     \YiZan\Models\GoodsNorms::whereIn('goods_id', $ids)->where('seller_id',$sellerId)->delete();
                   // \YiZan\Models\GoodsModel::whereIn('goods_id', $ids)->where('seller_id',$sellerId)->delete();
                    //删除服务标签
                    // \YiZan\Models\GoodsTagRelated::whereIn('goods_id', $ids)
                    //                             ->where('seller_id',$sellerId)
                    //                             ->where('type', 0)
                    //                             ->delete();
                    //删除提供服务的员工
                    \YiZan\Models\GoodsStaff::whereIn('goods_id', $ids)->where('seller_id',$sellerId)->delete();
                    //删除收藏的服务商品
                    \YiZan\Models\UserCollect::whereIn('goods_id', $ids)->where('seller_id',$sellerId)->delete();
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollback();
                    $result['code'] = 99999;
                }
                break;
        }
        
        return $result;
    }


}
