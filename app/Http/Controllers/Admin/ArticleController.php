<?php 
namespace YiZan\Http\Controllers\Admin;

use YiZan\Models\Article;
use YiZan\Http\Requests\Admin\ArticleCreatePostRequest;
use View, Input, Lang, Route, Page,Response;

/**
 * 文章管理
 */
class ArticleController extends AuthController {
	/**
	 * 文章列表
	 */
	public function index() {
		$post = Input::all();
		!empty($post['title']) ? $args['title'] = strval($post['title']) : null;
		!empty($post['cateId']) ? $args['cateId'] = intval($post['cateId']) : null;
		!empty($post['page']) ? $args['page'] = intval($post['page']) : $args['page'] = 1;
		$result = $this->requestApi('article.lists', $args);
		if( $result['code'] == 0 ) {
			View::share('list', $result['data']['list']);
		}
		$cate_list = $this->getcate();
		$_cate_list = [];
		foreach ($cate_list as $key => $value) {
			$_cate_list[$value['id']] = $value;
		}
		View::share('cate_list', $_cate_list);
		return $this->display();
	}

	/**
	 * 文章添加-编辑详细
	 */
	public function edit() {
		$args = Input::all();
		//编辑
		if( !empty($args['id']) ) {
			$args['id'] = $args['id'];
			$result = $this->requestApi('article.get',$args);
			View::share('data', $result['data']);
		}
		$cate = $this->getcate();
		View::share('cate', $cate);
		return $this->display();
	}

	/**
	 * 文章添加-编辑处理
	 */
	public function create() {
		$cate = $this->getcate();
		View::share('cate', $cate);
		return $this->display('edit');
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

	public function save(ArticleCreatePostRequest $request) {
		$args = Input::all();
		if( !empty($args['id']) ) {
			$result = $this->requestApi('article.update',$args); //更新
		}
		else {
			$result = $this->requestApi('article.create',$args);  //创建
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98008'), u('Article/index'), $result['data']);
	}

	/**
	 * 文章分类-删除分类
	 */
	public function destroy() {
		$args = Input::all();

		if( !empty( $args['id'] ) ) {
			$result = $this->requestApi('article.delete',$args); 
		}
		if( $result['code'] > 0 ) {
			return $this->error($result['msg']);
		}
		return $this->success(Lang::get('admin.code.98005') , u('Article/index'), $result['data']);
	}

	public function updateStatus() {
		$args = Input::all();
		$result = $this->requestApi('article.updateStatus',$args);
		return Response::json($result);
	}
}
