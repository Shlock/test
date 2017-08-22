<?php namespace YiZan\Services;

use YiZan\Models\Region;

use YiZan\Utils\String;
use YiZan\Utils\Http;
use DB;

class RegionService extends BaseService {

	public static function getById($id) {
        $region = Region::find($id);
        if ($region) {
            return $region->toArray();
        }
        return false;
    }

    public static function getCityByIp($ip) {
        $result = Http::get('http://ip.taobao.com/service/getIpInfo2.php', ['ip' => $ip]);
        $result = empty($result) ? false : @json_decode($result, true);

        $location = ['province'=>0, 'city'=>0];
        
        if ($result && $result['code'] == 0 && isset($result['data'])) {
            if (!empty($result['data']['region'])) {
                $location['province'] = self::getIdByName($result['data']['region']);
                if ($location['province'] > 0) {
                    $location['city'] = self::getIdByName($result['data']['city'], $location['province']);
                    return $location;
                }
            }
        }

        $result = Http::get('http://whois.pconline.com.cn/ipJson.jsp', ['ip' => $ip]);
        if (!empty($result)) {
            $result = trim($result);
            $index = strpos($result, 'IPCallBack(');
            if ($index > 0) {
                $result = iconv('GBK', 'UTF-8', substr($result,$index + 11, - 3));
                $result = @json_decode($result, true);
                if ($result && isset($result['pro']) && isset($result['city'])) {
                    if (!empty($result['pro'])) {
                        $location['province'] = self::getIdByName($result['pro']);
                        if ($location['province'] > 0) {
                            $location['city'] = self::getIdByName($result['city'], $location['province']);
                            return $location;
                        }
                    }
                }
            }
        }
        
        $result = Http::get('http://int.dpool.sina.com.cn/iplookup/iplookup.php', ['format'=>'json', 'ip' => $ip]);
        $result = empty($result) ? false : @json_decode($result, true);
        if ($result && $result['ret'] == 1) {
            if (!empty($result['province'])) {
                $location['province'] = self::getIdByName($result['province']);
                if ($location['province'] > 0) {
                    $location['city'] = self::getIdByName($result['city'], $location['province']);
                    return $location;
                }
            }
        }
        return $location;
    }

    /**
	 * 根据地区名称获取编号
	 * @param  string $name  地区名称
	 * @param  int 	  $pid 	 父级编号
	 * @return int           地区编号
	 */
	public static function getIdByName($name, $pid = 0) {
		if (empty($name)) {
			return 0;
		}

		$region = Region::select(DB::raw('id,name,MATCH(matchs) AGAINST(\'' . String::strToUnicode($name, '+') . '\') AS similarity'))
					->whereRaw('MATCH(matchs) AGAINST(\'' . String::strToUnicode($name, '+') . '\' IN BOOLEAN MODE)')
					->where('pid', $pid)
					->orderBy('similarity', 'desc')
					->first();
		
		if ($region) {
			return $region->id;
		} else {
			return 0;
		}
	}

    public static function getOpenCityByIp($ip) {
        $result = Http::get('http://ip.taobao.com/service/getIpInfo2.php', ['ip' => $ip]);
        $result = empty($result) ? false : @json_decode($result, true);
        
        if ($result && $result['code'] == 0 && isset($result['data'])) {
            if (!empty($result['data']['city'])) {
                $city = self::getOpenCityByName($result['data']['city']);
                if ($city) {
                    return $city;
                }
            }

            if (!empty($result['data']['region'])) {
                $city = self::getOpenCityByName($result['data']['region']);
                if ($city) {
                    return $city;
                }
            }
        }

        $result = Http::get('http://whois.pconline.com.cn/ipJson.jsp', ['ip' => $ip]);
        if (!empty($result)) {
            $result = trim($result);
            $index = strpos($result, 'IPCallBack(');
            if ($index > 0) {
                $result = iconv('GBK', 'UTF-8', substr($result,$index + 11, - 3));
                $result = @json_decode($result, true);
                if ($result && isset($result['pro']) && isset($result['city'])) {
                    if (!empty($result['city'])) {
                        $city = self::getOpenCityByName($result['city']);
                        if ($city) {
                            return $city;
                        }
                    }

                    if (!empty($result['pro'])) {
                        $city = self::getOpenCityByName($result['pro']);
                        if ($city) {
                            return $city;
                        }
                    }
                }
            }
        }
        
        $result = Http::get('http://int.dpool.sina.com.cn/iplookup/iplookup.php', ['format'=>'json', 'ip' => $ip]);
        $result = empty($result) ? false : @json_decode($result, true);
        if ($result && $result['ret'] == 1) {
            if (!empty($result['city'])) {
                $city = self::getOpenCityByName($result['city']);
                if ($city) {
                    return $city;
                }
            }

            if (!empty($result['province'])) {
                $city = self::getOpenCityByName($result['province']);
                if ($city) {
                    return $city;
                }
            }
        }
        $city = Region::where('is_service', 1)
                        ->orderBy('is_default','desc')
                        ->orderBy('sort','asc')
                        ->first();
        if($city) {
            return $city->toArray();
        }
        return null;
    }

    public static function getOpenCityByName($name) {
        $region = Region::select(DB::raw('*,MATCH(matchs) AGAINST(\'' . String::strToUnicode($name, '+') . '\') AS similarity'))
                    ->whereRaw('MATCH(matchs) AGAINST(\'' . String::strToUnicode($name, '+') . '\' IN BOOLEAN MODE)')
                    ->where('is_service', 1)
                    ->orderBy('similarity', 'desc')
                    ->first();

        if ($region) {
            return $region->toArray();
        } else {
            return false;
        }
    }

    /**
     * 获取所有省编号
     * @return array 城市数组
     */
    public static function getProvinces() {
        return Region::where('pid', 0)
            ->orderBy('sort','asc')
            ->orderBy('id','asc')
            ->get()
            ->toArray();
    }

	/**
	 * 获取开通服务的城市
	 * @return array 城市数组
	 */
	public static function getServiceCitys() {
		return Region::where('is_service', 1)
			->orderBy('sort','asc')
			->orderBy('id','asc')
			->get()
            ->toArray();
	}

    /**
     * 获取开通服务的城市
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array 城市数组
     */
	public static function getSystemServiceCitys($page, $pageSize) {
		return Region::where('is_service', 1)
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
			->orderBy('sort','asc')
			->orderBy('id','asc')
			->get()
            ->toArray();
	}

    /**
     * 添加开通城市
     * @param int $cityId 城市编号
     * @param int $sort 排序
     * @return array 添加结果
     */
    public static function create($cityId, $sort){
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

        $city = Region::where("id", $cityId)->first();
        
        if($city == false)
        {
            $result['code']	= 10601; // 城市不存在
            
            return $result;
        }
        
        if($city->is_service == true)
        {
            $result['code']	= 10602; // 城市已添加
            
            return $result;
        }
        
        Region::where("id", $cityId)->update(["is_service"=>1, "sort"=>$sort]);
        
        return $result;
    }
    /**
     * 添加开通城市
     * @param int $cityId 城市编号
     * @return array 添加结果
     */
    public static function delete($cityId){
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

        $city = Region::where("id", $cityId)->first();
        
        if($city == false)
        {
            $result['code']	= 10601; // 城市不存在
            
            return $result;
        }
        
        Region::where("id", $cityId)->update(["is_service"=>0]);
        
        return $result;
    }

    public static function setDefault($cityId) {
        Region::where("id", '<>', $cityId)->update(["is_default"=>0]);
        Region::where("id", $cityId)->update(["is_default"=>1]);
    }
    
    
    /**
     * 获取开通城市列表
     * @return [type] [description]
     */
    public static function getOpenCitys(){
        $regions = Region::where('is_service', 1) 
            ->orderBy('level','asc')
            ->orderBy('sort','asc')
            ->orderBy('id','asc')
            ->get()
            ->toArray(); 
        
        $regions_data = []; 
        
        $provinces = [];
        
        $citys = [];
        
        foreach ($regions as $item) 
        {
            if($item['level'] == 1)
            {
                $provinces[$item['id']] = 1;
                
                $tempItem = ['id'=>$item['id'],'name'=>$item['name']];
 
                $child_citys = self::getCitysById($item['id']);
                
                foreach ($child_citys as $citem) 
                {
                    $citys[$citem['id']] = 1;
                    
                    $tempCityItem = ['id'=>$citem['id'],'name'=>$citem['name']];
                    
                    $last_citys = self::getCitysById($citem['id']); 
                    
                    foreach ($last_citys as $litem) 
                    { 
                        $tempCityItem['area'][] = ['id'=>$litem['id'],'name'=>$litem['name']];
                    }
                    
                    $tempItem['city'][] = $tempCityItem; 
                }
                
                $regions_data[] = $tempItem;
            } 
            else if($item['level'] == 2)
            {
                $citys[$item['id']] = 1;
                
                if(empty($provinces[$item['pid']]))
                {
                    $pcity = self::getCitysById($item['pid'],'id',false); 
                    
                    $tempItem = ['id'=>$pcity['id'],'name'=>$pcity['name']];
                    
                    $tempCityItem = ['id'=>$citem['id'],'name'=>$citem['name']];
                    
                    $last_citys = self::getCitysById($item['id']); 
                    
                    foreach ($last_citys as $litem) 
                    { 
                        $tempCityItem['area'][] = ['id'=>$litem['id'],'name'=>$litem['name']];
                    }
                    
                    $tempItem['city'][] = $tempCityItem; 
                    
                    $regions_data[] = $tempItem;
                }
            } 
            else 
            {
                $city = self::getCitysById($item['pid'],'id',false);  
                
                $pcity = self::getCitysById($city['pid'],'id',false); 
                
                if(empty($citys[$item['pid']]))
                {
                    $regions_data[] = 
                        [
                            'id'=>$pcity['id'],
                            'name'=>$pcity['name'],
                            'city'=>
                            [
                                'id'=>$city['id'],
                                'name'=>$city['name'],
                                'area'=>['id'=>$item['id'],'name'=>$item['name']]
                            ]
                        ];
                }
            }

        }
        return $regions_data;
    }

    private static function getCitysById($id,$field = 'pid',$isList = true){
        if($field != 'pid'){
            $field = 'id';
        }
        $regions = Region::where($field, $id) 
                        ->orderBy('sort','asc')
                        ->orderBy('id','asc');
        if($isList){
            $regions = $regions->get()
                               ->toArray();
        } else {
            $regions = $regions->first()
                               ->toArray();
        }
        
        return $regions;                
    }
    
}
