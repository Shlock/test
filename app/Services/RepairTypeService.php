<?php 
namespace YiZan\Services;

use YiZan\Models\RepairType; 
use DB, Validator;
/**
 * 报修类型
 */
class RepairTypeService extends BaseService {

	/**
     * 列表 
     * @param  int $page 页码
     * @param  int $pageSize 每页数
     * @return array          文章信息
     */
	public static function getList($page, $pageSize) {

        $list = RepairType::orderBy('id', 'desc'); 

		$totalCount = $list->count();
        
		$list = $list->skip(($page - 1) * $pageSize)
                     ->take($pageSize) 
                     ->get()
                     ->toArray();
        
        return ["list"=>$list, "totalCount"=>$totalCount];
	}

    /**
     * 添加
     * @param int       $id   编号
     * @param string    $name 名称 
     * @param int       $sort 排序 
     * @return array   创建结果
     */
    public static function save($id, $name, $sort) 
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

		$rules = array(
			'name'         => ['required'], 
		);

		$messages = array
        (
            'name.required'	    => 50106,	// 请输入名称
        );

		$validator = Validator::make(
            [
				'name'      => $name, 
			], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }
        if($id > 0){
            $repairType = RepairType::find($id);
            if(empty($repairType)){
                $result['code'] = 50107;
                return $result;
            }
        } else {
            $repairType = new RepairType(); 
        } 
        $repairType->name    = $name; 
        $repairType->sort 	 = $sort;  
        $repairType->save();
        
        return $result;
    }

   
    /**
     * 获取 
     * @param  int $id 文章id
     * @return array   文章
     */
	public static function getById($id) 
    {
		return RepairType::find($id);
	}

    /**
     * 删除 
     * @param  int  $id 编号
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
  
		RepairType::where('id', $id)->delete();
        
		return $result;
	}
}
