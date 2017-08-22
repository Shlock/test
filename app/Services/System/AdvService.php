<?php 
namespace YiZan\Services\System;

use YiZan\Models\System\Adv;
use YiZan\Utils\String;
use YiZan\Utils\Time;
use DB, Validator;
/**
 * 广告管理
 */
class AdvService extends \YiZan\Services\AdvService 
{
	/**
     * 广告列表
     * @param string $clientType 客户端类型
     * @param  string $code 页码
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          广告信息
     */
	public static function getList($code, $page,$pageSize) 
    {
        $list = Adv::orderBy('id', 'desc');

        if ($code !== '') {
            $list->whereIn("position_id", function($query)use($code)
            {
                $query->select("id")
                    ->from('adv_position')
                    ->where('code', $code);

            });
        }
        
        $totalCount = $list->count();

        $list = $list->with('city', 'position')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get()
            ->toArray();
        
		return ["list"=>$list, "totalCount"=>$totalCount];
	}
    /**
     * 添加广告
     * @param int $cityId 显示城市
     * @param int $positionId 广告位编号
     * @param string $name 广告名称
     * @param string $image 图片
     * @param string $bgColor 背景颜色
     * @param string $type 动作类型
     * @param string $arg 动作参数
     * @param int $sort 排序
     * @param int $status 状态
     * @return array   创建结果
     */
    public static function create($cityId, $positionId, $name, $image, $bgColor, $type, $arg, $sort, $status) 
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
        );

		$rules = array(
			'name'         => ['required'],
			'image'        => ['required'],
			'type'         => ['required']
		);

		$messages = array(
            'name.required'	    => 70103,	// 请填写名称
            'image.required'	=> 70104,	// 请上传图片
            'type.required'	    => 70106,	// 请选择动作类型
        );

		$validator = Validator::make(
            [
				'name'      => $name,
				'image'     => $image,
				'type'      => $type
			], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }

        if($type < 8 && $arg === ''){
            $result['code'] = 70108;
            return $result;
        }

        $image = self::movePublicImage($image);
        if (!$image) {
            $result['code'] = 70105;
            return $result;
        }

        $adv = new Adv();
  
        $adv->city_id       = $cityId;
        $adv->position_id   = $positionId;
        $adv->name          = $name;
        $adv->image         = $image;
        $adv->bg_color      = $bgColor;
        $adv->type          = $type;
        $adv->arg           = $arg;
        $adv->sort 	        = $sort;
        $adv->status 	    = $status;
        $adv->create_time   = Time::getTime();
        
        $adv->save();
        
        return $result;
    }
    /**
     * 更新广告
     * @param int $id 广告id
     * @param int $cityId 显示城市
     * @param int $positionId 广告位编号
     * @param string $name 广告名称
     * @param string $clientType 客户端类型
     * @param string $image 图片
     * @param string $bgColor 背景颜色
     * @param string $type 动作类型
     * @param string $arg 动作参数
     * @param int $sort 排序
     * @param int $status 状态
     * @return array   创建结果
     */
    public static function update($id, $cityId, $positionId, $name, $image, $bgColor, $type, $arg, $sort, $status)
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

		$rules = array(
			'name'         => ['required'],
			'image'        => ['required'],
			'type'         => ['required']
		);

		$messages = array
        (
            'name.required'	    => 70103,	// 请填写名称
            'image.required'	=> 70104,	// 请上传图片
            'type.required'	    => 70106,	// 请选择动作类型
        );

		$validator = Validator::make(
            [
				'name'      => $name,
				'image'     => $image,
				'type'      => $type
			], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }

        if($type < 8 && $arg === ''){
            $result['code'] = 70108;
            return $result;
        }

        $adv = Adv::find($id);
  
        if ($image != $adv->image) {
            $image = self::movePublicImage($image);
            if (!$image) {
                $result['code'] = 70105;
                return $result;
            }
        }
        
        Adv::where("id", $id)->update(array(
               'city_id'        => $cityId,
               'position_id'    => $positionId,
               'name'           => $name,
               'image'          => $image,
               'bg_color'       => $bgColor,
               'type'           => $type,
               'arg'            => $arg,
               'sort' 	        => $sort,
               'status'         => $status
           ));

        return $result;
    }
    /**
     * 获取广告
     * @param  int $id 广告id
     * @return array   广告
     */
	public static function getById($id) 
    {
		return Adv::where('id', $id)
            ->with('city', 'position')
		    ->first();
	}
    /**
     * 设置状态
     * @param int  $id 广告id
     * @param int  $status 状态
     * @return array   删除结果
     */
	public static function setstatus($id, $status) 
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		];
        
		Adv::where('id', $id)->update(["status"=>$status]);
        
		return $result;
	}
    /**
     * 删除广告
     * @param int  $id 广告id
     * @return array   删除结果
     */
	public static function delete($id) 
    {
		$result = [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> ""
		];
		Adv::where('id', $id)->delete();
		return $result;
	}
}
