<?php 
namespace YiZan\Http\Controllers\Admin;

use View, Input, Lang, Route, Page, Response;

/**
 * 商家分类
 */
class GoodsCateController extends AuthController {
	/**
	 * 商家分类列表
	 */
	public function index() {
        $list = $this->getcate();
        if( count($list) > 0 ) {
            $pids[] = array_unique(array_reduce($list, create_function('$v,$w', '$v[$w["id"]]=$w["pid"];return $v;'))); 
            View::share('list', $list);
            View::share('pids',$pids);
        }
        //var_dump($list);
		return $this->display();
	}

    /**
     * 添加商家分类
     */
    public function create(){
        // $cate = $this->getcate();

        //限制2级 无需要恢复无限级 注释当前代码，释放无限极注释代码
        // $lev = 2;
        // $num = $lev - 1;
        // $_cate = [];
        // foreach ($cate as $key => $value) {
        //     if($value['level'] < $num)
        //         $_cate[] = $value;
        // }
        $cate[] = [ 
            "id" => 0,
            "pid" => 0,
            "name" => "顶级分类",
            "sort" => 100,
            "status" => 1,
            "level" => 0,
            "levelname" => "顶级分类"
        ];
        //array_unshift($_cate,$cate2); 
       //var_dump($_cate);
        View::share('cate', $cate);
        return $this->display('edit');
    }

    /**
     * 添加商家分类
     */
    public function edit(){
        $args = Input::all();
        if( !empty($args['id']) ) {
            $result = $this->requestApi('goods.cate.all');  
            if($result['code'] == 0){
                //限制2级 验证是否存在子集 存在子集不允许移动 如需要恢复无限极，注释当前代码，同时修改模板页面JS代码
                $lev = 2;
                $num = $lev - 1;
                $levs = [];
                foreach ($result['data'] as $key => $value) {
                    if($value['pid'] >= $num)
                        $levs[] = $value['id'];
                    if($value['pid']==$args['id'])
                        View::share('hasson', json_encode([1]));
                }
                View::share('levs', json_encode($levs));
                //end
                foreach ($result['data'] as $key => $value) {
                    if($value['id']==$args['id']){
                        $data = $value;
                        break;
                    }
                }
                $this->generateTree(0,$result['data']);
                $cate = $this->_cates;
                $cate2 = [ 
                    "id" => 0,
                    "pid" => 0,
                    "name" => "顶级分类",
                    "sort" => 100,
                    "status" => 1,
                    "level" => 0,
                    "levelname" => "顶级分类"
                ];
                array_unshift($cate,$cate2); 
                $this->_cates = [];
                $this->generateTree($data['id'],$result['data']);
                $son =  $this->_cates;
                array_unshift($son,$data); 
                $son = json_encode( array_map('array_shift', $son) );
                View::share('data', $data);
                View::share('cate', $cate);
                View::share('son', $son);
            }
        }
        return $this->display();
    }

    /**
     * 保存商家分类
     */
    public function save() {
        $args = Input::get();
        if ((int)$args['id'] > 0) {
            $url = u('GoodsCate/edit',['id'=>$args['id']]);
            $result = $this->requestApi('goods.cate.update', $args);
        } else {
            $url = u('GoodsCate/index');
            $result = $this->requestApi('goods.cate.create', $args);
        }
        if($result['code'] == 0){
            return $this->success($result['msg'] ? $result['msg'] : Lang::get('admin.code.98008'), $url);
        }else{
            return $this->error($result['msg'] ? $result['msg'] : Lang::get('admin.code.98009'));
        }

    }

    //获取分类
    public function getcate() {
        $result = $this->requestApi('goods.cate.lists'); 
        if($result['code']==0) {
            $this->generateTree(0,$result['data']);
        }
        //生成树形
        $cate = $this->_cates;
        return $cate;
    }

    /**
     * 删除商家分类
     */
    public function destroy(){
        $args = Input::get();
        $data = $this->requestApi('goods.cate.delete', ['id' =>$args['id'], 'sellerId'=>$args['sellerId']]);
        if($args['sellerId'] > 0){
            $url = u('Service/catelists',['sellerId'=>$args['sellerId']]);
        } else {
            $url = u('GoodsCate/index');
        } 
        if( $data['code'] > 0 ) {
            return $this->error($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98006'),$url );
        }
        return $this->success($data['msg']?$data['msg']:$data['msg'] = Lang::get('admin.code.98005'), $url , $data['data']);
    } 

}
