<?php namespace YiZan\Services\Sellerweb;

use YiZan\Models\Sellerweb\Region; 
use YiZan\Utils\String;
use YiZan\Utils\Http;
use DB;

class RegionService extends \YiZan\Services\RegionService {

    /**
     * 获取开通城市列表
     * @return [type] [description]
     */
    public static function getOpenCitys(){
        $regions = Region::where('is_service', 1) 
                        ->orderBy('sort','asc')
                        ->orderBy('id','asc')
                        ->get()
                        ->toArray(); 
        $regions_data = []; 
        foreach ($regions as $item) {
            if($item['level'] == 1){
                $regions_data[$item['id']] = ['i'=>$item['id'],'n'=>$item['name']];
                $child_citys = self::getCitysById($item['id']);
                foreach ($child_citys as $citem) {
                    $regions_data[$item['id']]['child'][$citem['id']] = ['i'=>$citem['id'],'n'=>$citem['name']]; 
                    $last_citys = self::getCitysById($citem['id']); 
                    foreach ($last_citys as $litem) { 
                        $regions_data[$item['id']]['child'][$citem['id']]['child'][$litem['id']] = ['i'=>$litem['id'],'n'=>$litem['name']];
                    }
                }
            } else if($item['level'] == 2){
                if(empty($regions[$item['pid']])){
                    $pcity = self::getCitysById($item['pid'],'id',false); 
                    $regions_data[$pcity['id']] = ['i'=>$pcity['id'],'n'=>$pcity['name']];
                    $regions_data[$pcity['id']]['child'][$item['id']] = ['i'=>$item['id'],'n'=>$item['name']];
                    $last_citys = self::getCitysById($item['id']); 
                    foreach ($last_citys as $litem) { 
                        $regions_data[$pcity['id']]['child'][$item['id']]['child'][$litem['id']] = ['i'=>$litem['id'],'n'=>$litem['name']];
                    }
                }
            } else {
                $city = self::getCitysById($item['pid'],'id',false);   
                $pcity = self::getCitysById($city['pid'],'id',false); 
                if(empty($regions[$pcity['id']])){
                    $regions_data[$pcity['id']] = ['i'=>$pcity['id'],'n'=>$pcity['name']];
                }
                if(empty($regions[$city['id']])){
                    $regions_data[$pcity['id']]['child'][$city['id']] = ['i'=>$city['id'],'n'=>$city['name']];
                }
                $regions_data[$pcity['id']]['child'][$city['id']]['child'][$item['id']] = ['i'=>$item['id'],'n'=>$item['name']];
            }

        }
        return $regions_data;
    }

}
