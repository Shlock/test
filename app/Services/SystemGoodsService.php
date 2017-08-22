<?php 
namespace YiZan\Services;

use YiZan\Models\System\Goods;
use YiZan\Models\GoodsExtend;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use YiZan\Utils\Helper;
use Illuminate\Database\Query\Expression;
use DB, Validator;

class SystemGoodsService extends BaseService {
    /**
     * 根据编号获取通用服务
     * @param  integer $id 通用服务编号
     * @return array       通用服务信息
     */
    public static function getById($id) {
        if($id < 1){
            return false;
        }
        return Goods::with('cate')->where('seller_id', 0)->find($id);
    }

	/**
	 * 获取通用服务列表
	 * @param  [type] $page        页码
	 * @param  [type] $pageSize    分页大小
	 * @param  [type] $name    	   关键字
	 * @param  [type] $type      服务类型
	 */
	public static function getList($page, $pageSize, $name = '',$type) {

        if (is_array($type) || (int)$type > 0) {
            $list = Goods::whereIn('type', $type)
                        ->orderBy('id', 'desc');
        } else {
            $list = Goods::orderBy('id', 'desc');
        }
        if (!empty($name)) {
            $list->where('name','like','%'.$name.'%');
        }

        $data['totalCount'] = $list->count();

        $data['list'] = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();

        return $data;
    }

    /**
     * 获取通用服务列表
     * @param  [type] $sellerId        卖家编号
     * @param  [type] $page        页码
     * @param  [type] $pageSize    分页大小
     * @param  [type] $name        关键字
     * @param  [type] $cateId      分类编号
     * @param  [type] $status      状态
     */
    public static function getListForSellerAdd($sellerId, $page, $pageSize, $name = '', $cateId = 0, $status = 0) {

        $list = Goods::with('cate')
                    ->where('seller_id', 0)
                    ->orderBy('sort', 'asc')
                    ->orderBy('id', 'desc');
        
		$name = empty($name) ? '' : String::strToUnicode($name,'+');
		if (!empty($name)) {
			$list->whereRaw('MATCH(name_match) AGAINST(\'' . $name . '\' IN BOOLEAN MODE)');
		}

        if ($cateId > 0) {
            $list->where('cate_id', $cateId);
        }

		if ($status > 0) {
			$list->where('status', $status - 1);
		}

        $tablePrefix    = DB::getTablePrefix();
        $goods_table    = DB::getTablePrefix().'goods';

        $list->whereNotExists(function($query) use($sellerId, $goods_table) {
            $query->select(DB::raw(1))
                  ->from('goods_seller')
                  ->where('goods_seller.goods_id', '=', new Expression("{$goods_table}.id"))
                  ->where('goods_seller.seller_id', '=', $sellerId);
        });

        $data['totalCount'] = $list->count();
        
        $data['list'] = $list->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();

        return $data;
    }

    /**
     * 更改服务状态
     * @param int $id;  服务编号
     * @return [type] [description]
     */
    public static function updateStatus($id, $status){
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];

        if ($id < 1) {
            $result['code'] = 30214;
            return $result;
        }
        $status = $status > 0 ? 1 : 0;

        Goods::where('id',$id)->update(['status' => $status]);
        return $result;
    }

    /**
     * 保存服务
     * @param int $id               服务编号
     * @param string $name          服务名称
     * @param int $priceType    价格类型
     * @param double $price         价格
     * @param double $marketPrice   门店价格
     * @param int $cateId           分类编号
     * @param string $brief         简介
     * @param array $images         图片数组
     * @param int $duration         时长（秒）
     * @param int $sort             排序
     * @param int $cityPrices       各城市价格
     * @return array                创建结果
     */
    public static function saveGoods($id, $name, $priceType, $price, $marketPrice, $cateId, $brief, $images, $duration, $sort, $cityPrices, $detail, $adminId) {

        $result = array(
            'code'  => self::SUCCESS,
            'data'  => null,
            'msg'   => ''
        );

        $rules = array(
            'name'          => ['required'],
            'priceType'     => ['in:1,2'],
            //'price'       => ['egt:0'],
            'marketPrice'   => ['egt:'.$price],
            'cateId'        => ['gt:0'],
            'brief'         => ['required'],
            'images'        => ['required'],
        );
        
        $messages = array (
            'name.required'     => 30202,   // 名称不能为空
            'priceType.in'      => 30215,   // 请选择正确的价格类型
           // 'price.gt'            => 30203,   // 请设置正确的门店价格
            'marketPrice.egt'   => 30204,   // 请设置正确的门店价格
           // 'marketPrice.egt' => 30205,   // 名称不能为空
            'cateId.gt'         => 30206,   // 请选择服务分类
            'brief.required'    => 30207,   // 简介不能为空
            'images.required'   => 30208,   // 请上传服务图片
        );

        $validator = Validator::make([
                'name'          => $name,
                'priceType'     => $priceType,
                //'price'       => $price,
                'marketPrice'   => $marketPrice,
                'cateId'        => $cateId,
                'brief'         => $brief,
                'images'        => is_array($images) ? implode(',', $images) : ""
            ], $rules, $messages);
        
        //验证信息
        if ($validator->fails()) {
            $messages = $validator->messages();
            $result['code'] = $messages->first();
            return $result;
        }

        if ($id > 0) {//服务不存在
            $goods = Goods::find($id);
            if (!$goods || $goods->seller_id > 0) {
                $result['code'] = 30214;
                return $result;
            }
        } else {
            $goods = new Goods;
            $goods->create_time = UTC_TIME;
        }
        
        DB::beginTransaction();
        try {
            $goods->name            = $name;
            $goods->name_match      = String::strToUnicode($name.$brief);
            $goods->price_type      = $priceType;
            $goods->price           = $price;
            $goods->market_price    = $marketPrice;
            $goods->cate_id         = $cateId;
            $goods->brief           = $brief;
            $goods->detail          = $detail;
            $goods->duration        = $duration;
            //$goods->status          = 1;
            $goods->sort            = $sort;
            $goods->update_time     = UTC_TIME;
            $goods->images          = implode(',' , $images);
            $goods->save();
            $goods->save();

            //保存城市价格
            /*SystemGoodsPrice::where('system_goods_id', $goods->id)->delete();
            $cityPrices = is_array($cityPrices) ? $cityPrices : [];
            foreach($cityPrices as $city_id => $cityPrice){
                $systemGoodsPrice                   = new SystemGoodsPrice();
                $systemGoodsPrice->system_goods_id  = $goods->id;
                $systemGoodsPrice->city_id          = $city_id;
                $systemGoodsPrice->price            = $cityPrice['price'];
                $systemGoodsPrice->market_price     = $cityPrice['marketPrice'];
                $systemGoodsPrice->save();
            }*/

            if ($id < 1) {//当为添加时, 创建扩展信息
                $goodsExtend = new GoodsExtend();
                $goodsExtend->goods_id = $goods->id;
                $goodsExtend->save();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    }

    /**
     * 删除服务
     * @param int  $id 服务id
     * @return array   删除结果
     */
    public static function deleteGoods($id) {
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];

        $goods = Goods::find($id);
        if (!$goods || $goods->seller_id > 0) {
            $result['code'] = 30214;
            return $result;
        }
        
        DB::beginTransaction();
        
        try
        {
            Goods::where('id', $id)->delete();
            //删除机构提供的服务
            \YiZan\Models\GoodsSeller::where('goods_id', $id)->delete();
            //删除服务属性
            \YiZan\Models\GoodsAttr::where('goods_id', $id)->delete();
            //删除服务扩展
            \YiZan\Models\GoodsExtend::where('goods_id', $id)->delete();
            //删除服务规格
            \YiZan\Models\GoodsModel::where('goods_id', $id)->delete();
            //删除服务标签
            \YiZan\Models\GoodsTagRelated::where('goods_id', $id)->delete();
            //删除提供服务的员工
            \YiZan\Models\GoodsStaff::where('goods_id', $id)->delete();

            DB::commit();
        } 
        catch (Exception $e) 
        {
            DB::rollback();
            $result['code'] = 99999;
        }
        return $result;
    }
}
