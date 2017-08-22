<?php 
namespace YiZan\Services;

use YiZan\Models\Adv;
/**
 * 广告管理
 */
class AdvService extends BaseService {
	public static function getAdvByCode($code, $cityId = 0){
		$list = Adv::select('adv.*')
				   ->join('adv_position', function($join) use($code) {
				        $join->on('adv_position.id', '=', 'adv.position_id')
				        	->where('adv_position.code', '=', $code);
			        })
				   ->whereIn('adv.city_id', array($cityId, 0))
				   ->where('adv.status',1)
				   ->orderBy('adv.city_id','desc')
				   ->orderBy('adv.sort','asc')
				   ->orderBy('adv.id','asc')
				   ->get()->toArray();	

        foreach($list as $key => $value)
        {
            $list[$key]["icon"] = $value["image"];
            
            if($value['type'] == 4)
            {
                $list[$key]["url"] = u('Wap#Article/detail',array('articleId'=>$value['arg']));
            }
        }
        
        return $list;
	}
}
