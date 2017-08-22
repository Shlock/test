<?php 
namespace YiZan\Services;

use YiZan\Models\ArticleCate;
use YiZan\Models\Article;
use YiZan\Utils\String;
use DB, Validator;
/**
 * 文章分类
 */
class ArticleCateService extends BaseService 
{
	/**
     * 文章分类列表
     * @return array          文章分类信息
     */
	public static function getList() 
    {
		return ArticleCate::orderBy('sort', "ASC")->get();
	}
    /**
     * 添加文章分类
     * @param int $pid 父编号
     * @param string $name 服务名称
     * @param int $sort 排序
     * @param int $status 状态
     * @return array   创建结果
     */
    public static function create($pid, $name, $sort, $status) 
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

		$rules = array(
			'name'          => ['required']
		);

		$messages = array
        (
            'name.required'	    => 60202	// 名称不能为空
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

        $cate = new ArticleCate();
        
        $cate->pid       = $pid;
        $cate->name      = $name;
        $cate->sort 	 = $sort;
        $cate->status 	 = $status;
        
        $cate->save();
        
        return $result;
    }
    /**
     * 更新文章分类
     * @param int $id 文章分类id
     * @param int $pid 父编号
     * @param string $name 服务名称
     * @param int $sort 排序
     * @param int $status 状态
     * @return array   创建结果
     */
    public static function update($id, $pid, $name, $sort, $status) 
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> ''
		);

		$rules = array(
			'name'          => ['required']
		);

		$messages = array
        (
            'name.required'	    => 60202	// 名称不能为空
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
        
        ArticleCate::where("id", $id)->update(array(
               'pid'      => $pid,
               'name'     => $name,
               'sort' 	  => $sort,
               'status'   => $status
           ));
        
        
        return $result;
    }
    /**
     * 删除文章分类
     * @param int  $id 文章分类id
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
        
        $article = Article::where('cate_id',$id)->first();

        if(empty($article)){

		    ArticleCate::where('id', $id)->delete();
        
        } else {

            $result['code'] = 60203;

        }
		return $result;
	}
}
