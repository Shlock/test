<?php 
namespace YiZan\Services;

use YiZan\Models\SellerCate;
use YiZan\Models\SellerCateRelated;
use YiZan\Models\Seller;
use YiZan\Utils\String;
use DB, Validator, Lang;

class SellerCateService extends BaseService 
{
    /**
     * 商家分类列表
     * @return array          商家分类信息
     */
    public static function wapGetList()
    {
        return SellerCate::where("status", 1)->orderBy('sort', 'ASC')->get()->toArray();
    }
    

	/**
     * 商家分类列表
     * @return array          商家分类信息
     */
	public static function getList($page, $pageSize) 
    {
        $list = SellerCate::where('pid',0)  
                            ->with(['childs' => function($query){
                                $query->with(['seller.sellers'])->orderBy('sort', 'ASC');
                            },'seller.sellers'])
                            ->orderBy('sort', 'ASC')
                            ->get()
                            ->toArray();
         return $list;   
	}
    /**
     * 添加商家分类
     * @param string $name 分类名称
     * @param string $pid 顶级分类
     * @param int $sort 排序
     * @return array   创建结果
     */
    public static function create($name, $pid, $status, $logo, $type, $sort)
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.create_sellercate')
		);

		$rules = array(
            'name'          => ['required']
        );

        if($pid < 1) {
            $rules['logo'] = ['required'];
        }

        $messages = array
        (
            'name.required'     => 30102,   // 名称不能为空
        );

        if($pid < 1) {
            $messages['logo.required'] = 10110;
        }

		$validator = Validator::make(
            [
                'name'      => $name,
                'logo'      => $logo
            ], $rules, $messages
        );
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }

        $Type = new SellerCate();

        $Type->name      = $name;
        $Type->pid       = $pid;
        $Type->logo      = $logo;
        $Type->status    = $status;
        $Type->type      = $type;
        $Type->sort 	 = $sort;
        
        $Type->save();
        
        return $result;
    }
    /**
     * 更新商家分类
     * @param int $id 商家分类id
     * @param string $ico 分类名称
     * @param string $name 分类名称
     * @param int $sort 排序
     * @return array   创建结果
     */
    public static function update($id, $name, $pid, $status, $logo, $type, $sort)
    {
        $result = array(
			'code'	=> self::SUCCESS,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.update_sellercate')
		);

		$rules = array(
            'name'          => ['required']
		);

        if($pid < 1) {
            $rules['logo'] = ['required'];
        }

        $messages = array
        (
            'name.required'     => 30102,   // 名称不能为空
        );

        if($pid < 1) {
            $messages['logo.required'] = 10110;
        }

		$validator = Validator::make(
            [
				'name'      => $name,
                'logo'      => $logo
            ], $rules, $messages);
        
        //验证信息
		if ($validator->fails()) 
        {
	    	$messages = $validator->messages();
            
	    	$result['code'] = $messages->first();
            
	    	return $result;
	    }
        
        SellerCate::where("id", $id)->update(array(
               'name'     => $name,
               'pid'     => $pid,
               'logo'     => $logo,
               'status'  => $status,
               'type'     => $type,
               'sort' 	  => $sort
           ));
        
        
        return $result;
    }
    /**
     * 删除商家分类
     * @param array  $ids 商家分类id
     * @return array   删除结果
     */
	public static function delete($ids)
    {
		$result = 
        [
			'code'	=> 0,
			'data'	=> null,
			'msg'	=> Lang::get('api.success.delete_sellercate')
		];
        self::replaceIn(implode(',', $ids));

        SellerCate::whereIn('id', $ids)->delete();
		
		return $result;
	}

    /**
     * 获取商家分类内容
     * @param int $id 商家分类编号
     */
    public static function get($id) {
        return SellerCate::where('id',$id)->first();
    }


    /**
     * 移除in语句的不标准字符
     * @param string $ids int,int,int
     */
    public static function replaceIn(&$ids) {
        if($ids != null) {
            $ids = preg_replace("/(^,)|(,$)|([^0-9,])/", "", $ids);
        }
    }

    /**
     * 商家分类列表
     * @return array          商家分类信息
     */
    public static function getAll()
    {
        $list = SellerCate::orderBy('pid', 'ASC')->orderBy('sort', 'ASC')->with('seller.sellers')->get()->toArray();
        $cates = [];
        foreach ($list as $key => $cate) {
            if ($cate['pid'] > 0) {
                $cates[$cate['pid']]['selected'] = false;
                $cate['selected'] = true;
                $cate['showName'] = "　".$cate['name'];
                $cates[$cate['pid']]['childs'][] = $cate;
            } else {
                $cate['selected'] = true;
                $cate['showName'] = $cate['name'];
                $cates[$cate['id']] = $cate;
            }
        }

        $list = [];
        foreach ($cates as $cate) {
            if (isset($cate['childs'])) {
                $childs = $cate['childs'];
                unset($cate['childs']);
                $list[] = $cate;

                foreach ($childs as $child) {
                    $list[] = $child;
                }
            } else {
                $list[] = $cate;
            }
        }
        return $list;
    }

    /**
     * 获取商家分类
     * @return array
     */
    public static function getSellerCatesAll(){ 
        $data = SellerCate::where('pid',0) 
                          ->where('status', '=', 1)
                            ->with(['childs' => function($query){
                                $query->with(['seller.sellers'])->where('status', '=', 1)->orderBy('sort', 'ASC');
                            },'seller.sellers'])
                            ->orderBy('sort', 'ASC')
                            ->get()
                            ->toArray(); 
        return $data;
    }



    public static function getCatesAll($id = 0, $type)
    {   

        if ($id > 0) { 
            $result = SellerCate::where('id', $id)
                                ->where('status', 1)
                                ->with('childs')
                                ->orderBy('sort', 'ASC')
                                ->get()
                                ->toArray();
           // return $result ? $result->toArray() : [];
        } else {
            $result = SellerCate::with('childs')->where('pid', 0)->where('status', 1)->orderBy('sort', 'ASC');
            if ($type > 0) {
                $result->where('type', $type);
            }
            $result = $result->get()->toArray();
        }
        return $result;
    }
    /**
     * 获取商家选择的行业分类
     */
    public static function getSellerCateLists($sellerId){
        $list = SellerCateRelated::where('seller_id', $sellerId)
                                 ->with('cates', 'cates.childs')
                                 ->get()
                                 ->toArray();  
        foreach ($list as $key => $value) {
            $list[$key]['pid'] = $value['cates']['pid'];
            $list[$key]['name'] = $value['cates']['name'];
            $list[$key]['logo'] = $value['cates']['logo'];
            $list[$key]['sort'] = $value['cates']['sort'];
            $list[$key]['status'] = $value['cates']['status'];
            $list[$key]['type'] = $value['cates']['type'];
            $list[$key]['childs'] = $value['cates']['childs'];
        }
        return $list;
    }
}
