<?php 
namespace YiZan\Services\System;

use YiZan\Models\System\SellerCreditRank;
use YiZan\Utils\String;
use DB, Validator;
/**
 * 信誉等级管理
 */
class SellerCreditRankService extends \YiZan\Services\SellerCreditRankService 
{
	/**
     * 信誉等级列表
     * @return array          信誉等级信息
     */
	public static function getList() 
    {
        $list = SellerCreditRank::orderBy('id', 'desc');
  
		return $list->get()
            ->toArray();
	}
    /**
     * 添加信誉等级
     * @param int $name 等级名称
     * @param string $icon 图标
     * @param int $minScore 最低分
     * @param int $maxScore 最高分
     * @return array   创建结果
     */
    public static function create($name, $icon, $minScore, $maxScore) 
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

		$rules = array(
			'name'        => ['required'],
            'icon'        => ['required']
		);

		$messages = array
        (
            'name.required'	    => 30901,	// 名称不能为空
            'icon.required'	    => 30903	// 图标不能为空
        );
          
		$validator = Validator::make(
            [
				'name'      => $name,
                'icon'      => $icon
			], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }
        
        if($minScore > $maxScore)
        {
            $result['code'] = 30908; // 最高信誉分不能小于最低信誉分
            
	    	return $result;
        }
        
        // 名字是否存在
        if(SellerCreditRank::where("name", $name)->pluck("name") == true)
        {
            $result['code'] = 30902; // 名称已存在
            
	    	return $result;
        }
        
        // 最低信誉分跟其他等级冲突
        if(SellerCreditRank::where("min_score", "=<", $minScore)->where("max_score", ">=", $minScore)->pluck("min_score") == true)
        {
            $result['code'] = 30906; // 最低信誉分跟其他等级冲突
            
	    	return $result;
        }
        
        // 最高信誉分跟其他等级冲突
        if(SellerCreditRank::where("min_score", "=<", $maxScore)->where("max_score", ">=", $maxScore)->pluck("max_score") == true)
        {
            $result['code'] = 30909; // 最高信誉分跟其他等级冲突
            
	    	return $result;
        }

        $rank = new SellerCreditRank();
  
        $rank->name         = $name;
        $rank->icon         = $icon;
        $rank->min_score    = $minScore;
        $rank->max_score    = $maxScore;
        
        $rank->save();
        
        return $result;
    }
    /**
     * 更新信誉等级
     * @param int $id 等级id
     * @param int $name 等级名称
     * @param string $icon 图标
     * @param int $minScore 最低分
     * @param int $maxScore 最高分
     * @return array   创建结果
     */
    public static function update($id, $name, $icon, $minScore, $maxScore) 
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

		$rules = array(
			'name'        => ['required'],
            'icon'        => ['required']
		);

		$messages = array
        (
            'name.required'	    => 30901,	// 名称不能为空
            'icon.required'	    => 30903	// 图标不能为空
        );
        
		$validator = Validator::make(
            [
				'name'      => $name,
                'icon'      => $icon
			], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }
        
        SellerCreditRank::where("id", $id)->update(array(
               'name'       => $name,
               'icon'       => $icon,
               'min_score'  => $minScore,
               'max_score'  => $maxScore
           ));
        
        return $result;
    }
    /**
     * 获取信誉等级
     * @param  int $id 等级id
     * @return array   广告位信息
     */
	public static function getById($id) 
    {
		return SellerCreditRank::where('id', $id)
		    ->first();
	}
    /**
     * 删除信誉等级
     * @param int  $id 等级id
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
        
		SellerCreditRank::where('id', $id)->delete();
        
		return $result;
	}
}
