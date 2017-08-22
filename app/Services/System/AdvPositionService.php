<?php 
namespace YiZan\Services\System;

use YiZan\Models\System\AdvPosition;
use YiZan\Utils\String;
use DB, Validator;
/**
 * 广告位管理
 */
class AdvPositionService extends \YiZan\Services\AdvPositionService 
{
	/**
     * 广告位列表
     * @param string $clientType 客户端类型
     * @return array          广告位信息
     */
	public static function getList($clientType) 
    {
        $list = AdvPosition::orderBy('id', 'desc');
  
        if($clientType == true)
        {
            $list->where("client_type", $clientType);
        }
        
		return $list->get()
            ->toArray();
	}
    /**
     * 添加广告位
     * @param string $code 位置代码
     * @param int $isAutoCode 自动生成代码
     * @param string $name 位置名称
     * @param string $clientType 客户端类型
     * @param int $width 广告宽度
     * @param int $height 广告高度
     * @param string $brief 位置描述
     * @param string $style 样式
     * @return array   创建结果
     */
    public static function create($code, $isAutoCode, $name, $clientType, $width, $height, $brief, $style) 
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

		$rules = array(
            'name' => ['required']
		);

		$messages = array(
            'name.required'	    => 70201	// 请填写位置名称
        );

		$validator = Validator::make([
				'name'      => $name
			], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) {
	    	$messages = $validator->messages();
	    	$result['code'] = $messages->first();
	    	return $result;
	    }

        if($isAutoCode == 0 && empty($code)){//广告位代码为空
            $result['code'] = 70202;
            return $result;
        } elseif($isAutoCode == 0 && !empty($code)){//广告位代码重复
            $count = AdvPosition::where('code', $code)->count();
            if ($count > 0) {
                $result['code'] = 70203;
                return $result;
            }
        }

        if ($isAutoCode == 1) {//自动生成代码
            $code = strtoupper($clientType) .'_'. String::randString(8, 2);
        }

        $position = new AdvPosition();
  
        $position->code         = $code;
        $position->name         = $name;
        $position->client_type  = $clientType;
        $position->width        = $width;
        $position->height       = $height;
        $position->brief        = $brief;
        $position->style 	    = $style;
        
        $position->save();
        
        return $result;
    }
    /**
     * 更新广告位
     * @param int $id 广告位id
     * @param string $name 位置名称
     * @param string $clientType 客户端类型
     * @param int $width 广告宽度
     * @param int $height 广告高度
     * @param string $brief 位置描述
     * @param string $style 样式
     * @return array   创建结果
     */
    public static function update($id, $name, $clientType, $width, $height, $brief, $style) 
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

		$rules = array(
			'name'         => ['required']
		);

		$messages = array
        (
            'name.required'	    => 70201	// 请填写位置名称
        );

		$validator = Validator::make(
            [
				'name'      => $name
			], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }
        
        AdvPosition::where("id", $id)->update(array(
               'name'           => $name,
               'client_type'    => $clientType,
               'width'          => $width,
               'height'         => $height,
               'brief'          => $brief,
               'style'          => $style
           ));

        return $result;
    }
    /**
     * 获取广告位
     * @param  int $id 广告位id
     * @return object   广告位信息
     */
	public static function getById($id) 
    {
		return AdvPosition::where('id', $id)
		    ->first();
	}
    /**
     * 删除广告位
     * @param int  $id 广告位id
     * @return array   删除结果
     */
	public static function delete($id) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		];
        
		AdvPosition::where('id', $id)->where('is_system', 0)->delete();
        
		return $result;
	}
}
