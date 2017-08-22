<?php

namespace YiZan\Services;

use YiZan\Models\AppIndexBanner;
use YiZan\Models\AppIndexCategory;

class ConfigService extends \YiZan\Services\BaseService {

    /**
     * 获取所在城市首页轮播图
     * @param  string  $app    APP类型
     * @param  integer $cityId 所在城市
     * @return array           Banner数组
     */
    public static function getBanners($app, $cityId = 0) {
        return AppIndexBanner::whereIn('city_id', array($cityId, 0))
                        ->where('status', 1)
                        ->orderBy('city_id', 'desc')
                        ->orderBy('sort', 'asc')
                        ->orderBy('id', 'asc')
                        ->get()->toArray();
    }

    /**
     * 获取所在城市首页分类
     * @param  string  $app    APP类型
     * @param  integer $cityId 所在城市
     * @return array           Banner数组
     */
    public static function getCategorys($app, $cityId = 0) {
        return AppIndexCategory::whereIn('city_id', array($cityId, 0))
                        ->where('status', 1)
                        ->orderBy('city_id', 'desc')
                        ->orderBy('sort', 'asc')
                        ->orderBy('id', 'asc')
                        ->get()->toArray();
    }

}
