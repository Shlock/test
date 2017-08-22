<?php namespace YiZan\Services;

use YiZan\Models\GoodsTag; 
use YiZan\Models\Buyer\GoodsTagRelated; 
use DB,Validator;

class GoodsTagService extends BaseService {

	/**
	 * 获取服务标签列表
	 */
	public static function getList(){
		return GoodsTag::orderBy('sort', 'ASC')->get();
	}

	/**
	 * 获取热门标签
	 */
	public static function getHotTagLists($page, $pageSize = 20){  
		$list = GoodsTagRelated::groupBy('tag_id')
						->join('goods_tag', 'goods_tag.id', '=', 'goods_tag_related.tag_id')
						->where('goods_tag.status', STATUS_ENABLED)
						->select(DB::raw('count(goods_id) as num,tag_id'))
						->groupBy('goods_tag_related.tag_id')
						->with('tag');
		$list = $list->orderBy('num','desc')->skip(($page - 1) * $pageSize)->take($pageSize)->get()->toArray();
		return $list; 
	}

	/**
	 * 根据编号获取标签
	 */
	public static function get($id){
		return GoodsTag::where('id',$id)->first();
	}

	/**
	 * 创建服务标签
	 */
	public static function create( $name, $sort, $status){
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

		$rules = array(
            'name'          => ['required'], 
        );

        $messages = array
        (
            'name.required'     => 30102,   // 名称不能为空 
        );

		$validator = Validator::make(
            [
                'name'      => $name, 
            ], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }

	    $tagItem = GoodsTag::where('name',$name)->first();

	    if($tagItem){
	    	$result['code'] = 30701;
	    	return $result;
	    }

        $tag = new GoodsTag();
         
        $tag->name      = $name; 
        $tag->sort 	 	= $sort; 
        $tag->status 	= $status;
         
    	$tag->save(); 
        return $result;

	}

	/**
	 * 修改服务标签
	 */
	public static function update($id, $name, $sort, $status){
		$result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

		$rules = array(
            'name'          => ['required'], 
		);

		$messages = array
        (
            'name.required'     => 30102,   // 名称不能为空 
        );

		$validator = Validator::make(
            [
				'name'      => $name, 
            ], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }
        
        GoodsTag::where("id", $id)->update(array( 
               'name'     => $name, 
               'sort' 	  => $sort,
               'status'   => $status
           ));
        
        
        return $result;
	}

	/**
	 * 删除服务标签
	 */
	public static function delete($id){
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		];
        
		GoodsTag::where('id', $id)->delete();
        
		return $result;
	}
}
