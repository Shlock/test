<?php 
namespace YiZan\Http\Controllers\Seller;

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
		!empty($post['page']) ? $args['page'] = intval($post['page']) : $args['page'] = 1;
		$result = $this->requestApi('article.lists', $args);
		View::share('list', $result['data']['list']); 
		return $this->display();
	}

	/**
	 * 文章添加-编辑详细
	 */
	public function edit() { 
		$result = $this->requestApi('article.get', Input::all());
		View::share('data', $result['data']); 
		return $this->display();
	}

	/**
	 * 文章添加-编辑处理
	 */
	public function create() { 
		return $this->display('edit');
	} 

	public function save() {
		$args = Input::all();
		$result = $this->requestApi('article.save',$args);
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
