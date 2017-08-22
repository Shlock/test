<?php namespace YiZan\Services\System;

use YiZan\Models\Article;
use YiZan\Models\Property;
use YiZan\Models\District;
use YiZan\Utils\Time;
use DB,Validator;

class PropertyService extends \YiZan\Services\PropertyService {
	public static function getLists($name, $districtName, $provinceId, $cityId, $areaId, $status, $isTotal, $page, $pageSize){
		$list = Property::orderBy('id', 'DESC');
		
		if($name == true){
			$list->where('name', $name);
		}

		if($districtName == true || $provinceId == true || $cityId == true || $areaId == true){
			$list->join('district', function($join) use($districtName, $provinceId, $cityId, $areaId){
	            $join->on('district.id', '=', 'property.district_id');
	            if($districtName == true){
	            	$join->where('district.name', 'like', "%{$districtName}%");
	            }
	            if($provinceId == true){
	            	$join->where('district.province_id', '=', $provinceId);
	            }
	            if($cityId == true){
	            	$join->where('district.city_id', '=', $cityId);
	            }
	            if($areaId == true){
	            	$join->where('district.area_id', '=', $areaId);
	            }
	        });
		} 

		if($status > 0){
			$list->where('status', $status - 2);
		}
		
    	$totalCount = $list->count();
 		
 		if($isTotal){
	 		$list = $list->with('district', 'user')
			             ->get()
			             ->toArray();
 		} else {
	 		$list = $list->skip(($page - 1) * $pageSize)
			             ->take($pageSize)
			             ->with('district', 'user')
			             ->get()
			             ->toArray();
 		}

    	return ["list"=>$list, "totalCount"=>$totalCount];
	}

	public static function getTotalLists(){
		return Property::orderBy('id', 'DESC') 
		               ->with('district', 'user')
		               ->get()
		               ->toArray();;
	}

	public static function save($id, $companyName, $mobile, $pwd, $contact, 
		$districtId, $idcardSn, $idcardPositiveImg, $idcardNegativeImg, $businessLicenceImg){
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '添加成功'
		];	
		if($companyName == ''){
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
		return Property::find($id);
	}

    /**
     * 更改状态
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
        $status = $status > 0 ? 1 : -1;
        // DB::connection()->enableQueryLog();
        if(is_array($id)){
        	ForumPosts::whereIn('id',$id)->update(['status' => $status]);
        } else {
       	 	ForumPosts::where('id',$id)->update(['status' => $status]);
        }
        // print_r(DB::getQueryLog());exit;
        return $result;
    }

	public static function delete($id){
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '删除成功'
		];
		Property::where('id', $id)
				  ->delete();
		return $result;
	}

	public static function getArticleLists($sellerId, $title, $beginTime, $endTime, $page, $pageSize){
		$list = Article::where('seller_id', $sellerId)->orderBy('id', 'DESC');
		
		if($title == true){
			$list->where('title', 'like', '%'.$title.'%');
		}

		if($beginTime > 0) {
	        $list->where('create_time', '>=', Time::toTime($beginTime));
	    }

	    if($endTime > 0) {
	        $list->where('create_time', '<=', Time::toTime($endTime));
	    }
		
    	$totalCount = $list->count();

	 	$list = $list->skip(($page - 1) * $pageSize)
		             ->take($pageSize)
		             ->with('seller')
		             ->get()
		             ->toArray();

    	return ["list"=>$list, "totalCount"=>$totalCount];
	}

	public static function getArticle($id){
		return Article::find($id);
	}

	public static function articleSave($id, $sellerId, $title, $content){
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '操作成功'
		];	
		if($title == ''){
			$result['code'] = 60101;
			return $result;
		}
		if($content == ''){
			$result['code'] = 60103;
			return $result;
		}

		if ($sellerId < 1) {
			$result['code'] = 80202;
			return $result;
		}
		$article = Article::find($id);
		if(!$article){
			$article = new Article();
		}
		$article->seller_id		= $sellerId;
		$article->title 		= $title;
		$article->content 		= $content;
		$article->sort 			= 100;
		$article->status 		= 1;
		$article->create_time 	= UTC_TIME;

		try {
			$article->save();
		} catch (Exception $e) {
			$result['code'] = 99999;
		}
		return $result;
	}

	public static function deleteArticle($id){
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> '删除成功'
		];
		Article::where('id', $id)->delete();
		return $result;
	}

}
