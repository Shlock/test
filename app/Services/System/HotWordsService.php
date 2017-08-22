<?php
namespace YiZan\Services\System;

use YiZan\Models\HotWords; 
use YiZan\Models\Region; 
use YiZan\Utils\Time;
use YiZan\Utils\String;
use DB, Exception,Validator;

/**
 * 热搜关键词
 */
class HotWordsService extends \YiZan\Services\HotWordsService {
 	
 	/**
 	 * 热搜关键词列表
 	 *
 	 */
 	public static function getLists($hotwords, $city, $page, $pageSize){ 
 		// DB::connection()->enableQueryLog();
 		$list = HotWords::orderBy('id', 'DESC');
 		if($hotwords){
 			$list->where('hotwords', 'like', '%'.$hotwords."%");
 		}
 		if($city){
 			$cityIds = Region::where('name', 'like', '%'.$city."%")
 							 ->lists('id'); 
 			$cityIdStr = implode(',', $cityIds);
 			if($cityIdStr){
 				$list->whereRaw("(province_id in (".$cityIdStr.") or city_id in (".$cityIdStr.") or area_id in (".$cityIdStr."))");
 			} else { 
 				return ['list'=>[], 'totalCount'=>0];
 			}
 			
 		}
 		$totalCount = $list->count();
 		$list = $list->skip(($page-1)*$pageSize)
 					 ->take($pageSize)
 					 ->with('province','city','area')
 					 ->get();
 		// print_r(DB::getQueryLog());exit;
 		return ['list'=>$list, 'totalCount'=>$totalCount];
 	}

 	/**
 	 *  保存热搜关键词
 	 */
 	public static function save($id, $hotwords, $provinceId, $cityId, $areaId, $sort, $status){
 		$result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
        );

		$rules = array(
			'hotwords'         => ['required'], 
		);

		$messages = array(
            'hotwords.required'	    => 70309,	// 请填写名称 
        );

		$validator = Validator::make(
            [
				'hotwords'      => $hotwords, 
			], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }
        
 		$hotWords = new HotWords();

 		if($id > 0){
 			$hotWords = HotWords::find($id);
 		}

 		$hotWords->hotwords 	= $hotwords;
 		$hotWords->province_id 	= $provinceId;
 		$hotWords->city_id 		= $cityId;
 		$hotWords->area_id 		= $areaId;
 		$hotWords->sort 		= $sort;
 		$hotWords->status 		= $status;
 		$hotWords->create_time 	= UTC_TIME;
 		$hotWords->save();
 		return $result;
 	}

 	/**
 	 * 关键词详情
 	 */
 	public static function getById($id){
 		return HotWords::where('id', $id)
 					   ->with('province','city','area')
 					   ->first();
 	}

 	/**
 	 * 删除关键词
 	 */
 	public static function delete($id){
 		$result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
        );

 		$bl = HotWords::where('id', $id)
 					  ->delete();
 		if(!$bl){
 			$result['code'] = 70310;
 		}

 		return $result;
 	} 

    public static function updateStatus($id,$status){
        $result = [
            'code'  => 0,
            'data'  => null,
            'msg'   => ""
        ];
 
        $status = $status > 0 ? 1 : 0;

        $bl = HotWords::where('id',$id)->update(['status' => $status]);

        if(!$bl){
        	$result['code'] = 80001;
        }

        return $result;
    }

}
