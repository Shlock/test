<?php namespace YiZan\Services\System;


use YiZan\Models\ForumPlate;
class ForumPlateService extends \YiZan\Services\ForumPlateService {
	
	public static function getLists($name, $isTotal, $page, $pageSize){
		$list = ForumPlate::orderBy('sort', 'ASC');
		if($name){
			$list->where('name', $name);
		}
		
    	$totalCount = $list->count();

    	if($isTotal){
			$list = $list->get()
			             ->toArray();
    	} else {
			$list = $list->skip(($page - 1) * $pageSize)
			             ->take($pageSize)
			             ->get()
			             ->toArray();
    	}


    	return ["list"=>$list, "totalCount"=>$totalCount];
	}

	public static function save($id, $name, $icon, $sort, $status){
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '添加成功'
		];	
		if($name == ''){
			$result['code'] = 30911;
			return $result;
		}
		if($icon == ''){
			$result['code'] = 30912;
			return $result;
		}
		$forumplate = ForumPlate::find($id);
		if(!$forumplate){
			$forumplate = new ForumPlate();
		}
		$forumplate->name 		= $name;
		$icon = self::movePublicImage($icon);
        if (!$icon) {//转移图片失败
            $result['code'] = 30213;
            return $result;
        }
		$forumplate->icon 		= $icon;
		$forumplate->sort 		= $sort;
		$forumplate->status 		= $status;
		try {
			$forumplate->save();
		} catch (Exception $e) {
			$result['code'] = 99999;
		}
		return $result;
	}

	public static function get($id){
		return ForumPlate::find($id);
	}

	public static function delete($id){
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '删除成功'
		];
		ForumPlate::where('id', $id)
				  ->delete();
		ForumPosts::where('plate_id', $id)->update(['is_del'=>1]);
		return $result;
	}

}
