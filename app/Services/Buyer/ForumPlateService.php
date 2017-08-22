<?php namespace YiZan\Services\Buyer;

use YiZan\Models\ForumPlate;

class ForumPlateService extends \YiZan\Services\ForumPlateService {

	/**
	 * 板块列表
	 */
	public static function lists(){
		$list = ForumPlate::where('status', 1)
						  ->orderBy('sort', 'ASC')
						  ->get()
						  ->toArray();
		return $list;
	} 
	
	public static function get($id){
		$forumplate = ForumPlate::where('id',$id)
								->first();
		return $forumplate;
	}

}
