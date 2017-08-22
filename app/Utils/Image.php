<?php
namespace YiZan\Utils;

use Config;

class Image {
    /**
     * 获取表单上传参数
     * @return [type] [description]
     */
    public static function getFormArgs(){
        $class = 'YiZan\\Utils\\Image\\'.Config::get('app.image_type').'Image';
        $image = new $class();
        $path  = 'images/'.Time::toDate(UTC_TIME, 'Y/m/d').'/'.Helper::getSn().'.jpg';
        return $image->getFormArgs($path);
    }

    public static function upload($data, $path){
        $class = 'YiZan\\Utils\\Image\\'.Config::get('app.image_type').'Image';
        $image = new $class();
        return $image->upload($data, $path);
    }

    /**
     * 删除
     * @param  array $paths 路径数组
     * @return boolean
     */
    public static function remove($paths){
    	$class = 'YiZan\\Utils\\Image\\'.Config::get('app.image_type').'Image';
    	$image = new $class();
    	return $image->remove($paths);
    }

    /**
     * 移动
     * @param  [type] $formPath [description]
     * @param  [type] $dir      [description]
     * @return [type]           [description]
     */
    public static function move($formPath, $dir){
    	$class = 'YiZan\\Utils\\Image\\'.Config::get('app.image_type').'Image';
    	$image = new $class();
    	return $image->move($formPath,$dir);
    }
}