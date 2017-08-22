<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\ArticleCate;
use YiZan\Http\Requests\Admin\GoodsCateCreatePostRequest;
use View, Input, Lang, Route, Page,Response;

/**
 * 文章分类
 */
class ArticleCateController extends AuthController {
	/**
	 * 文章分类-分类列表-添加-编辑分类
	 */
	public function index() {
		$list = $this->getcate();
		if( count($list) > 0 ) {
			$pids[] = array_unique(array_reduce($list, create_function('$v,$w', '$v[$w["id"]]=$w["pid"];return $v;'))); 
			View::share('list', $list);
			View::share('pids',$pids);
		}
		return $this->display();
	}

	/**
	 * 文章分类-添加、编辑分类
	 */
	public function create() {
		$cate = $this->getcate();
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
		View::share('cate', $cate);
		return $this->display("edit");
	}
	/**
	 * 文章添加-编辑详细
	 */
	public function edit() {
		$args = Input::all();
		if( !empty($args['id']) ) {
			$result = $this->requestApi('article.cate.lists');
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

		return $this->display("edit");
	}

	//获取分类
	public function getcate() {
		$result = $this->requestApi('article.cate.lists');
		if($result['code']==0) {
			$this->generateTree(0,$result['data']);
		}
		//生成树形
		$cate = $this->_cates;
		return $cate;
	}

	public function save(GoodsCateCreatePostRequest $request) {
		$args = Input::all();
		if( !empty($args['id']) ) {
			$result = $this->requestApi('article.cate.update',$args); //更新
		}
		else {
			$result = $this->requestApi('article.cate.create',$args);  //创建
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('ArticleCate/index'), $result['data']);
	}

	/**
	 * 文章分类-删除分类
	 */
	public function destroy() {
		$args = Input::all();
		if( !empty( $args['id'] ) ) {
			$result = $this->requestApi('article.cate.delete',$args); 
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98005'), u('ArticleCate/index'), $result['data']);
	}

	public function updateStatus() {
		$args = Input::all();
		$result = $this->requestApi('article.cate.updateStatus',$args);
		return Response::json($result);
	}


}
