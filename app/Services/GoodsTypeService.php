<?php 
namespace YiZan\Services;

use YiZan\Models\GoodsType;
use YiZan\Models\Goods;
use YiZan\Utils\String;
use DB, Validator, Lang;

class GoodsTypeService extends BaseService 
{
    /**
     * 菜品分类列表
     * @return array          菜品分类信息
     */
    public static function wapGetList()
    {
        return GoodsType::where("status", 1)->orderBy('sort', 'ASC')->get()->toArray();
    }
    

	/**
     * 菜品分类列表
     * @return array          菜品分类信息
     */
	public static function getList($page, $pageSize) 
    {
		$list = GoodsType::orderBy('sort', "ASC");
        $totalCount = $list->count();
        $list = $list->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();
         return ["list"=>$list, "totalCount"=>$totalCount];   
	}
    /**
     * 添加菜品分类
     * @param string $name 分类名称
     * @param string $ico 分类图标
     * @param int $sort 排序
     * @return array   创建结果
     */
    public static function create($name, $ico, $sort)
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.create_goodstype')
		);

		$rules = array(
            'name'          => ['required'],
            'ico'          => ['required']
        );

        $messages = array
        (
            'name.required'     => 30102,   // 名称不能为空
            'ico.required'     => 30103    // 图标不能为空
        );

		$validator = Validator::make(
            [
                'name'      => $name,
                'ico'      => $ico
            ], $rules, $messages
        );
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }

        $Type = new GoodsType();

        $Type->name      = $name;
        $Type->ico      = $ico;
        $Type->sort 	 = $sort;
        
        $Type->save();
        
        return $result;
    }
    /**
     * 更新菜品分类
     * @param int $id 菜品分类id
     * @param string $ico 分类名称
     * @param string $name 分类名称
     * @param int $sort 排序
     * @return array   创建结果
     */
    public static function update($id, $name, $ico, $sort)
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.update_goodstype')
		);

		$rules = array(
            'name'          => ['required'],
            'ico'          => ['required']
		);

		$messages = array
        (
            'name.required'     => 30102,   // 名称不能为空
            'ico.required'     => 30103    // 图标不能为空
        );

		$validator = Validator::make(
            [
				'name'      => $name,
                'ico'      => $ico
            ], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }
        
        GoodsType::where("id", $id)->update(array(
               'name'     => $name,
               'ico'     => $ico,
               'sort' 	  => $sort
           ));
        
        
        return $result;
    }
    /**
     * 删除菜品分类
     * @param array  $ids 菜品分类id
     * @return array   删除结果
     */
	public static function delete($ids)
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.delete_goodstype')
		];
        self::replaceIn(implode(',', $ids));

        $check = Goods::whereIn('type_id', $ids)->count();
        if ($check > 0) {
            $result['code'] = 50408;
            return $result;
        }
        GoodsType::whereIn('id', $ids)->delete();
		
		return $result;
	}

    /**
     * 获取菜品分类内容
     * @param int $id 菜品分类编号
     */
    public static function get($id) {
        return GoodsType::where('id',$id)->first()->toArray();
    }


    /**
     * 移除in语句的不标准字符
     * @param string $ids int,int,int
     */
    public static function replaceIn(&$ids) {
        if($ids != null) {
            $ids = preg_replace("/(^,)|(,$)|([^0-9,])/", "", $ids);
        }
    }

    /**
     * 菜品分类列表
     * @return array          菜品分类信息
     */
    public static function getAll()
    {
        return GoodsType::orderBy('sort', 'ASC')->get()->toArray();
    }
}
